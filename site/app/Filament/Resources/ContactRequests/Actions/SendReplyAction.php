<?php

declare(strict_types=1);

namespace App\Filament\Resources\ContactRequests\Actions;

use App\Models\ContactRequest;
use App\Models\MailTemplate;
use App\Services\LeadReplySender;
use App\Support\MailDelivery;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;

/**
 * Answer a lead without leaving the admin panel.
 *
 * A template written for the lead's service is pre-filled so the first draft is
 * already specific, and everything stays editable before it goes out. The
 * outcome is reported honestly: a message handled by the log transport is never
 * described as delivered.
 */
final class SendReplyAction extends Action
{
    protected const MAX_BODY = 5000;

    public static function getDefaultName(): ?string
    {
        return 'sendReply';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Send reply')
            ->icon('heroicon-o-paper-airplane')
            ->modalHeading('Reply to this request')
            ->modalDescription(fn (ContactRequest $record): string => 'Goes to '.$record->email.' as a normal email. Their answer comes back to '.config('mail.reply_to.address').'.')
            ->modalSubmitActionLabel('Send email')
            ->modalWidth(Width::TwoExtraLarge)
            ->schema([
                Select::make('mail_template_id')
                    ->label('Start from a template')
                    ->options(fn (ContactRequest $record): array => self::templateOptions($record))
                    ->default(fn (ContactRequest $record): ?int => MailTemplate::defaultFor($record->service)?->id)
                    ->helperText('Replies are written per service. Picking one fills the message below — edit it freely.')
                    ->native(false)
                    ->live()
                    ->afterStateUpdated(function (?int $state, Set $set, ContactRequest $record): void {
                        $draft = self::draft($record, $state);
                        $set('subject', $draft['subject']);
                        $set('body', $draft['body']);
                    }),

                TextInput::make('subject')
                    ->required()
                    ->maxLength(180)
                    ->default(fn (ContactRequest $record): string => self::draft($record)['subject']),

                Textarea::make('body')
                    ->label('Message')
                    ->required()
                    ->minLength(20)
                    ->maxLength(self::MAX_BODY)
                    ->rows(16)
                    ->helperText('Plain text. Line breaks are kept and everything is escaped, so nothing in here can break the email.')
                    ->columnSpanFull()
                    ->default(fn (ContactRequest $record): string => self::draft($record)['body']),
            ])
            ->action(function (ContactRequest $record, array $data, LeadReplySender $sender): void {
                $template = filled($data['mail_template_id'] ?? null)
                    ? MailTemplate::find($data['mail_template_id'])
                    : null;

                $message = $sender->send(
                    $record,
                    $template,
                    trim((string) $data['subject']),
                    trim((string) $data['body']),
                );

                if ($message->failed()) {
                    Notification::make()
                        ->title('The reply was not sent')
                        ->body(($message->error ?: 'The mail server refused the message.').' The attempt is kept in this request\'s history, so nothing is lost.')
                        ->danger()
                        ->persistent()
                        ->send();

                    return;
                }

                if ($message->wasDelivered()) {
                    Notification::make()
                        ->title('Reply sent')
                        ->body('Delivered to '.$record->email.'.')
                        ->success()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Reply recorded, but not delivered')
                    ->body(MailDelivery::problem())
                    ->warning()
                    ->persistent()
                    ->send();
            });
    }

    /**
     * Templates grouped so the ones written for this lead's service come first.
     *
     * @return array<string, array<int, string>>
     */
    private static function templateOptions(ContactRequest $record): array
    {
        $options = [];

        foreach (MailTemplate::choicesFor($record->service) as $template) {
            $group = match (true) {
                $template->service === null => 'Any service',
                $template->service === $record->service => $record->serviceLabel(),
                default => 'Other services',
            };

            $options[$group][$template->id] = $template->name;
        }

        return $options;
    }

    /**
     * @return array{subject: string, body: string}
     */
    private static function draft(ContactRequest $record, ?int $templateId = null): array
    {
        $template = $templateId !== null
            ? MailTemplate::find($templateId)
            : MailTemplate::defaultFor($record->service);

        if ($template === null) {
            return ['subject' => '', 'body' => ''];
        }

        $tokens = $record->replyTokens();

        return [
            'subject' => $template->renderSubject($tokens),
            'body' => $template->renderBody($tokens),
        ];
    }
}

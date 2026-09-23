<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns\Schemas;

use App\Models\OutreachCampaign;
use App\Models\OutreachProspect;
use App\Support\OutreachLint;
use App\Support\OutreachQuota;
use App\Support\OutreachWindow;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\HtmlString;

final class OutreachCampaignForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Callout::make()
                ->color('warning')
                ->icon(Heroicon::OutlinedExclamationTriangle)
                ->heading('Before this campaign can send')
                ->description(fn (): string => implode('   ·   ', OutreachLint::configurationProblems()))
                ->visible(fn (): bool => OutreachLint::configurationProblems() !== [])
                ->columnSpanFull(),

            Section::make('1. What this campaign offers')
                ->description('The offer appears in the email footer as the honest reason the recipient is hearing from us.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Campaign name')
                        ->required()
                        ->maxLength(150)
                        ->helperText('Only shown inside the panel.'),

                    TextInput::make('goal')
                        ->label('The offer')
                        ->maxLength(200)
                        ->placeholder('a free 10-minute website audit')
                        ->helperText('One short phrase, e.g. “a free website audit”.'),

                    Select::make('status')
                        ->options(OutreachCampaign::statuses())
                        ->default(OutreachCampaign::STATUS_DRAFT)
                        ->required()
                        ->native(false)
                        ->helperText('Only an active campaign sends. Nothing goes out while it is a draft or paused, which is what makes it safe to build the sequence first.'),

                    TextInput::make('daily_limit')
                        ->label('Daily limit for this campaign')
                        ->numeric()
                        ->minValue(1)
                        ->placeholder((string) OutreachQuota::dailyLimit())
                        ->helperText('Leave empty to use the site-wide limit of '.OutreachQuota::dailyLimit().' a day.'),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),

            Section::make('2. The sequence')
                ->description('A first email, then the follow-ups. Each step waits a number of days after the one before it, and the sequence stops for good the moment somebody replies, bounces or opts out.')
                ->schema([
                    Repeater::make('steps')
                        ->relationship()
                        ->orderColumn('position')
                        ->reorderable()
                        ->addActionLabel('Add a follow-up step')
                        ->defaultItems(1)
                        ->itemLabel(fn (array $state): ?string => filled($state['subject'] ?? null)
                            ? (string) $state['subject']
                            : 'New step')
                        ->schema([
                            TextInput::make('delay_days')
                                ->label('Wait before sending')
                                ->numeric()
                                ->minValue(0)
                                ->maxValue(120)
                                ->default(0)
                                ->suffix('day(s)')
                                ->helperText('Days after the previous step — or after somebody is added, for the first step.'),

                            TextInput::make('subject')
                                ->required()
                                ->maxLength(180)
                                ->live(onBlur: true)
                                ->columnSpanFull(),

                            Textarea::make('body')
                                ->label('Message')
                                ->required()
                                ->minLength(20)
                                ->maxLength(5000)
                                ->rows(12)
                                ->live(onBlur: true)
                                ->helperText('Plain text. Line breaks are preserved, so write it the way you would write an email.')
                                ->columnSpanFull(),

                            // Sits under the copy it is commenting on, and updates
                            // when the fields above it lose focus.
                            Placeholder::make('copy_check')
                                ->hiddenLabel()
                                ->columnSpanFull()
                                ->content(fn (Get $get): HtmlString => self::copyCheck(
                                    (string) $get('subject'),
                                    (string) $get('body'),
                                )),
                        ])
                        ->columns(3),
                ]),

            Section::make('Placeholders you can use')
                ->description('Filled in for each person before the message is sent. Where we have no value, a neutral phrase is used and the check tells you so.')
                ->collapsed()
                ->schema([
                    Placeholder::make('token_reference')
                        ->hiddenLabel()
                        ->content(fn (): HtmlString => new HtmlString(
                            '<div style="font-size:0.85rem;line-height:1.7;color:#475569;">'
                            .implode('', array_map(
                                fn (string $token): string => '<code style="font-family:ui-monospace,monospace;font-size:0.8rem;background:#f1f5f9;padding:1px 5px;border-radius:4px;">'.e($token).'</code> ',
                                OutreachProspect::TOKENS,
                            ))
                            .'<div style="margin-top:8px;">{first_name} and {company} are the two that carry the message. A cold email that opens generically is read as a mailshot.</div>'
                            .'</div>',
                        )),
                ]),

            Section::make('The rules currently in force')
                ->description('Set in config/outreach.php and the server environment. A campaign cannot exceed them.')
                ->collapsed()
                ->schema([
                    Placeholder::make('rules')
                        ->hiddenLabel()
                        ->content(fn (): HtmlString => new HtmlString(self::rulesList())),
                ]),
        ]);
    }

    private static function rulesList(): string
    {
        $closure = OutreachWindow::closure();

        $items = [
            'Sending window: '.config('outreach.window.start').'–'.config('outreach.window.end')
                .' ('.OutreachWindow::timezone().')'.((bool) config('outreach.window.weekdays_only') ? ', weekdays only' : ''),
            'Site-wide daily limit: '.OutreachQuota::dailyLimit().' messages',
            'Each run sends up to '.config('outreach.per_run').' messages, about '.config('outreach.interval_seconds').'s apart',
            'Cooldown before the same person can be approached again: '.config('outreach.cooldown_days').' days',
            'Right now: '.OutreachQuota::summary()
                .($closure === null ? ' — the window is open.' : ' — window shut ('.$closure.'), '.OutreachWindow::nextOpenLabel().'.'),
        ];

        $html = '<ul style="margin:0;padding-left:18px;font-size:0.85rem;line-height:1.8;color:#475569;">';

        foreach ($items as $item) {
            $html .= '<li>'.e($item).'</li>';
        }

        return $html.'</ul>';
    }

    /**
     * The copy check, shown while the copy is being written rather than
     * discovered later in a filter or a complaint.
     *
     * The step's position is not available from inside a repeater item, so the
     * position-dependent rule (a first email claiming to be a reply) is left to
     * the campaign-level check, which knows where each step sits in the order.
     */
    private static function copyCheck(string $subject, string $body): HtmlString
    {
        if (trim($subject) === '' && trim($body) === '') {
            return new HtmlString('');
        }

        $issues = OutreachLint::check($subject, $body);
        $colour = OutreachLint::hasWarnings($issues) ? '#b45309' : '#047857';

        if ($issues === []) {
            return new HtmlString(
                '<div style="font-size:0.8rem;line-height:1.6;color:'.$colour.';">✓ '.e(OutreachLint::summary($issues)).'</div>',
            );
        }

        $rows = '';

        foreach ($issues as $issue) {
            $warning = $issue['level'] === OutreachLint::LEVEL_WARNING;

            $rows .= '<li style="margin:0 0 4px 0;color:'.($warning ? '#b45309' : '#475569').';">'
                .($warning ? '⚠ ' : '· ').e($issue['message']).'</li>';
        }

        return new HtmlString(
            '<div style="font-size:0.8rem;line-height:1.6;">'
            .'<div style="font-weight:600;color:'.$colour.';">Pre-send check — '.e(OutreachLint::summary($issues)).'</div>'
            .'<ul style="margin:4px 0 0 0;padding-left:16px;">'.$rows.'</ul>'
            .'</div>',
        );
    }
}

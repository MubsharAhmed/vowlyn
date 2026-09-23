<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachCampaigns;

use App\Filament\Resources\OutreachCampaigns\Pages\CreateOutreachCampaign;
use App\Filament\Resources\OutreachCampaigns\Pages\EditOutreachCampaign;
use App\Filament\Resources\OutreachCampaigns\Pages\ListOutreachCampaigns;
use App\Filament\Resources\OutreachCampaigns\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Resources\OutreachCampaigns\Schemas\OutreachCampaignForm;
use App\Filament\Resources\OutreachCampaigns\Tables\OutreachCampaignsTable;
use App\Models\OutreachCampaign;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Cold outreach: the sequences the studio writes once and sends to a list.
 *
 * Kept beside the lead pipeline in the panel but in its own group, because the
 * two are fundamentally different work. A lead asked to be answered and a
 * prospect did not ask for anything — which is why this side carries limits,
 * an opt-out list and a send log that the lead side does not need.
 */
final class OutreachCampaignResource extends Resource
{
    protected static ?string $model = OutreachCampaign::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPaperAirplane;

    protected static string|UnitEnum|null $navigationGroup = 'Outreach';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Campaigns';

    protected static ?string $modelLabel = 'campaign';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return OutreachCampaignForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutreachCampaignsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EnrollmentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOutreachCampaigns::route('/'),
            'create' => CreateOutreachCampaign::route('/create'),
            'edit' => EditOutreachCampaign::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $active = OutreachCampaign::query()->sending()->count();

        return $active > 0 ? (string) $active : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
}

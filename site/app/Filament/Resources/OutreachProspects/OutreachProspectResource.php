<?php

declare(strict_types=1);

namespace App\Filament\Resources\OutreachProspects;

use App\Filament\Resources\OutreachProspects\Pages\CreateOutreachProspect;
use App\Filament\Resources\OutreachProspects\Pages\EditOutreachProspect;
use App\Filament\Resources\OutreachProspects\Pages\ListOutreachProspects;
use App\Filament\Resources\OutreachProspects\RelationManagers\EnrollmentsRelationManager;
use App\Filament\Resources\OutreachProspects\RelationManagers\MessagesRelationManager;
use App\Filament\Resources\OutreachProspects\Schemas\OutreachProspectForm;
use App\Filament\Resources\OutreachProspects\Tables\OutreachProspectsTable;
use App\Models\OutreachProspect;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * The people being approached.
 *
 * Separate from the leads pipeline on purpose. A lead is somebody who asked to
 * be contacted; a prospect is somebody we decided to contact. Mixing the two
 * would mean a single consent rule where there are really two, and the stricter
 * one would lose.
 */
final class OutreachProspectResource extends Resource
{
    protected static ?string $model = OutreachProspect::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Outreach';

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Prospects';

    protected static ?string $modelLabel = 'prospect';

    protected static ?string $recordTitleAttribute = 'company';

    public static function form(Schema $schema): Schema
    {
        return OutreachProspectForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutreachProspectsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            EnrollmentsRelationManager::class,
            MessagesRelationManager::class,
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var OutreachProspect $record */
        return [
            'Email' => $record->email,
            'Status' => $record->statusLabel(),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['company', 'contact_name', 'email'];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOutreachProspects::route('/'),
            'create' => CreateOutreachProspect::route('/create'),
            'edit' => EditOutreachProspect::route('/{record}/edit'),
        ];
    }
}

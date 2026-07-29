<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RaceResource\Pages;
use App\Models\Race;
use Filament\Actions\Action;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/**
 * 스키마 접두(review.races) 스모크용 목록 Resource.
 * 편집은 당분간 /races-admin 손제작 화면 사용.
 */
class RaceResource extends Resource
{
    protected static ?string $model = Race::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationLabel = '대회 목록';

    protected static ?string $modelLabel = '대회';

    protected static ?string $pluralModelLabel = '대회';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return '대회';
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('name')->label('대회명')->searchable()->sortable(),
                TextColumn::make('city')->label('도시')->toggleable(),
                IconColumn::make('is_domestic')->label('국내')->boolean(),
                IconColumn::make('is_published')->label('공개')->boolean(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                Action::make('legacyEdit')
                    ->label('레거시 수정')
                    ->url(fn (Race $record) => route('races-admin.races.edit', $record))
                    ->openUrlInNewTab(false),
            ])
            ->headerActions([
                Action::make('legacyAdmin')
                    ->label('레거시 대회관리')
                    ->url(route('races-admin.races.index')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRaces::route('/'),
        ];
    }
}

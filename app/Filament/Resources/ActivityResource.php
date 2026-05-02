<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Spatie\Activitylog\Models\Activity;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $navigationLabel = 'Auditoria';
    protected static ?string $modelLabel = 'Atividade';
    protected static ?string $pluralModelLabel = 'Auditoria';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Quando')
                    ->dateTime('d/m/Y H:i:s')
                    ->sortable()
                    ->size('sm'),
                Tables\Columns\TextColumn::make('causer.name')
                    ->label('Quem')
                    ->default('—')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('event')
                    ->label('Ação')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'created' => 'criou',
                        'updated' => 'editou',
                        'deleted' => 'excluiu',
                        default => $state,
                    }),
                Tables\Columns\TextColumn::make('log_name')
                    ->label('Tipo')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Modelo')
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subject_id')
                    ->label('ID')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Descrição')
                    ->limit(60)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('event')->options([
                    'created' => 'Criação',
                    'updated' => 'Edição',
                    'deleted' => 'Exclusão',
                ]),
                Tables\Filters\SelectFilter::make('log_name')->options(function () {
                    return Activity::query()->distinct()->pluck('log_name', 'log_name')->toArray();
                }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()->label('Detalhes')->modalHeading(fn ($record) => "Atividade #{$record->id}"),
            ]);
    }

    public static function canCreate(): bool { return false; }
    public static function canDelete($record): bool { return false; }
    public static function canEdit($record): bool { return false; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
        ];
    }
}

<?php

namespace App\Filament\Resources\IndicatorResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ValuesRelationManager extends RelationManager
{
    protected static string $relationship = 'values';
    protected static ?string $title = 'Histórico de valores';
    protected static ?string $modelLabel = 'Valor';
    protected static ?string $pluralModelLabel = 'Valores';
    protected static ?string $recordTitleAttribute = 'period';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('period')
                ->label('Período')
                ->placeholder('Ex.: 2026-04 ou 2026 ou 2026-04-15')
                ->helperText('Use formato YYYY-MM para mês, YYYY para anual, ou data completa.')
                ->required()
                ->maxLength(16),
            Forms\Components\TextInput::make('value')
                ->label('Valor')
                ->numeric()
                ->step('0.0001')
                ->required(),
            Forms\Components\TextInput::make('goal_value')
                ->label('Meta para este período (opcional)')
                ->numeric()
                ->step('0.0001')
                ->helperText('Se vazio, usa a meta padrão do indicador.'),
            Forms\Components\DateTimePicker::make('recorded_at')
                ->label('Registrado em')
                ->seconds(false)
                ->default(now())
                ->required(),
            Forms\Components\Textarea::make('notes')
                ->label('Observações')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('period')->label('Período')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('value')->label('Valor')->numeric(decimalPlaces: 2),
                Tables\Columns\TextColumn::make('goal_value')->label('Meta')->numeric(decimalPlaces: 2)->toggleable(),
                Tables\Columns\TextColumn::make('recorded_at')->label('Registrado')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('notes')->label('Observações')->limit(40)->toggleable(),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->defaultSort('recorded_at', 'desc');
    }
}

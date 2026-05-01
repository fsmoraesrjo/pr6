<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IndicatorResource\Pages;
use App\Models\Indicator;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class IndicatorResource extends Resource
{
    protected static ?string $model = Indicator::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar-square';
    protected static ?string $navigationGroup = 'Transparência';
    protected static ?string $navigationLabel = 'Indicadores';
    protected static ?string $modelLabel = 'Indicador';
    protected static ?string $pluralModelLabel = 'Indicadores';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Indicador')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id'))
                    ->required()->searchable(),
                Forms\Components\TextInput::make('name')->label('Nome')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->columnSpanFull(),
                Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull(),
                Forms\Components\TextInput::make('unit')->label('Unidade')->default('%')->maxLength(32),
                Forms\Components\TextInput::make('category')->label('Categoria'),
                Forms\Components\Select::make('chart_type')->label('Tipo de gráfico')->options([
                    'progress' => 'Progresso (barra)',
                    'line' => 'Linha temporal',
                    'bar' => 'Colunas',
                    'gauge' => 'Velocímetro',
                    'area' => 'Área',
                ])->default('progress'),
                Forms\Components\TextInput::make('goal_value')->label('Meta')->numeric()->step('0.01'),
                Forms\Components\Toggle::make('is_public')->label('Público')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Destaque'),
                Forms\Components\TextInput::make('order')->label('Ordem')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('tenant.short_name')->label('Diretoria')->badge(),
            Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->limit(50),
            Tables\Columns\TextColumn::make('chart_type')->label('Gráfico')->badge(),
            Tables\Columns\TextColumn::make('goal_value')->label('Meta')->numeric(),
            Tables\Columns\TextColumn::make('unit')->label('Unidade'),
            Tables\Columns\IconColumn::make('is_public')->label('Público')->boolean(),
            Tables\Columns\IconColumn::make('is_featured')->label('Destaque')->boolean(),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('tenant_id')->label('Diretoria')
                ->options(Tenant::orderBy('order')->pluck('short_name', 'id')),
        ])
        ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
        ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListIndicators::route('/'),
            'create' => Pages\CreateIndicator::route('/create'),
            'edit' => Pages\EditIndicator::route('/{record}/edit'),
        ];
    }
}

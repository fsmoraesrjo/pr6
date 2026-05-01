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
            Forms\Components\Section::make('Identificação')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id'))
                    ->required()->searchable(),
                Forms\Components\TextInput::make('category')->label('Categoria'),
                Forms\Components\TextInput::make('name')->label('Nome')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->columnSpanFull(),
                Forms\Components\Textarea::make('description')->label('Descrição')->rows(3)->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Apresentação')->columns(3)->schema([
                Forms\Components\Select::make('chart_type')->label('Tipo de gráfico')->options([
                    'progress' => 'Progresso (barra)',
                    'line' => 'Linha temporal',
                    'bar' => 'Colunas',
                    'area' => 'Área',
                    'gauge' => 'Velocímetro (radial)',
                ])->default('line')->required(),
                Forms\Components\TextInput::make('unit')->label('Unidade')->default('%')->maxLength(32),
                Forms\Components\TextInput::make('goal_value')->label('Meta')->numeric()->step('0.01'),
                Forms\Components\ColorPicker::make('color')->label('Cor (opcional)')
                    ->helperText('Sobrescreve a cor da diretoria. Deixe vazio para usar a cor padrão.'),
                Forms\Components\TextInput::make('icon')->label('Ícone (heroicon)')->maxLength(64),
                Forms\Components\TextInput::make('order')->label('Ordem')->numeric()->default(0),
            ]),

            Forms\Components\Section::make('Origem do dado')->columns(2)->schema([
                Forms\Components\Select::make('source')->label('Fonte')
                    ->options(Indicator::SOURCES)
                    ->default('manual')
                    ->required()
                    ->live(),
                Forms\Components\Placeholder::make('last_synced_at')
                    ->label('Última sincronização')
                    ->content(fn ($record) => $record?->last_synced_at?->translatedFormat('d/m/Y H:i') ?? 'Nunca')
                    ->visible(fn (Forms\Get $get) => $get('source') !== 'manual'),
                Forms\Components\KeyValue::make('source_config')
                    ->label('Configuração da fonte')
                    ->keyLabel('Chave')->valueLabel('Valor')
                    ->columnSpanFull()
                    ->helperText('Para HELP/CIC: endpoint, metric, token. Exemplo: endpoint=https://help.pr6.uerj.br/api/stats/sla')
                    ->visible(fn (Forms\Get $get) => $get('source') !== 'manual'),
            ]),

            Forms\Components\Section::make('Publicação')->columns(2)->schema([
                Forms\Components\Toggle::make('is_public')->label('Público')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Destaque na home'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('tenant.short_name')->label('Diretoria')->badge(),
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('chart_type')->label('Gráfico')->badge(),
                Tables\Columns\TextColumn::make('source')->label('Fonte')->badge()
                    ->formatStateUsing(fn ($state) => Indicator::SOURCES[$state] ?? $state)
                    ->color(fn ($state) => $state === 'manual' ? 'gray' : 'primary'),
                Tables\Columns\TextColumn::make('goal_value')->label('Meta')->numeric()->sortable(),
                Tables\Columns\TextColumn::make('unit')->label('Unidade'),
                Tables\Columns\IconColumn::make('is_public')->label('Público')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->label('Destaque')->boolean(),
                Tables\Columns\TextColumn::make('last_synced_at')->label('Sincronizado')->since()->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id')),
                Tables\Filters\SelectFilter::make('source')->options(Indicator::SOURCES),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('order');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\IndicatorResource\RelationManagers\ValuesRelationManager::class,
        ];
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

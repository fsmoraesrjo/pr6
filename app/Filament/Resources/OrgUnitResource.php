<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrgUnitResource\Pages;
use App\Models\OrgUnit;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class OrgUnitResource extends Resource
{
    protected static ?string $model = OrgUnit::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Estrutura';
    protected static ?string $navigationLabel = 'Organograma';
    protected static ?string $modelLabel = 'Unidade';
    protected static ?string $pluralModelLabel = 'Unidades';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Unidade organizacional')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id'))
                    ->required()->searchable()->live(),
                Forms\Components\Select::make('parent_id')->label('Unidade superior')
                    ->options(function (Forms\Get $get) {
                        $tenantId = $get('tenant_id');
                        if (! $tenantId) return [];
                        return OrgUnit::where('tenant_id', $tenantId)
                            ->withoutGlobalScopes()
                            ->orderBy('order')
                            ->pluck('name', 'id');
                    })
                    ->searchable()
                    ->placeholder('— Unidade raiz —'),
                Forms\Components\TextInput::make('name')->label('Nome')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->maxLength(160)->columnSpanFull(),
                Forms\Components\Select::make('type')->label('Tipo')->options([
                    'proreitoria' => 'Pró-reitoria',
                    'diretoria' => 'Diretoria',
                    'coordenacao' => 'Coordenação',
                    'gerencia' => 'Gerência',
                    'setor' => 'Setor',
                ])->required()->default('setor'),
                Forms\Components\TextInput::make('order')->label('Ordem')->numeric()->default(0),
                Forms\Components\Textarea::make('description')->label('Descrição')->rows(2)->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->label('Ativa')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.short_name')->label('Diretoria')->badge(),
                Tables\Columns\TextColumn::make('name')->label('Nome')->searchable()->limit(60)
                    ->formatStateUsing(function ($state, OrgUnit $record) {
                        $depth = 0; $cursor = $record;
                        while ($cursor->parent_id && $depth < 4) {
                            $cursor = OrgUnit::withoutGlobalScopes()->find($cursor->parent_id);
                            if (! $cursor) break;
                            $depth++;
                        }
                        return str_repeat('— ', $depth) . $state;
                    }),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('parent.name')->label('Unidade superior')->limit(30)->toggleable(),
                Tables\Columns\TextColumn::make('order')->label('Ordem')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativa')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id')),
                Tables\Filters\SelectFilter::make('type')->options([
                    'proreitoria' => 'Pró-reitoria', 'diretoria' => 'Diretoria',
                    'coordenacao' => 'Coordenação', 'gerencia' => 'Gerência', 'setor' => 'Setor',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrgUnits::route('/'),
            'create' => Pages\CreateOrgUnit::route('/create'),
            'edit' => Pages\EditOrgUnit::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TenantResource\Pages;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TenantResource extends Resource
{
    protected static ?string $model = Tenant::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';
    protected static ?string $navigationGroup = 'Sistema';
    protected static ?string $navigationLabel = 'Diretorias';
    protected static ?string $modelLabel = 'Diretoria';
    protected static ?string $pluralModelLabel = 'Diretorias';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificação')->columns(2)->schema([
                Forms\Components\TextInput::make('slug')->required()->maxLength(32),
                Forms\Components\TextInput::make('short_name')->label('Sigla')->required()->maxLength(32),
                Forms\Components\TextInput::make('full_name')->label('Nome completo')->required()->columnSpanFull(),
                Forms\Components\TextInput::make('tagline')->label('Subtítulo')->maxLength(200)->columnSpanFull(),
                Forms\Components\Textarea::make('description')->label('Descrição')->columnSpanFull()->rows(3),
            ]),
            Forms\Components\Section::make('Domínios')->columns(2)->schema([
                Forms\Components\TextInput::make('domain_dev')->label('Domínio dev'),
                Forms\Components\TextInput::make('domain_prod')->label('Domínio produção'),
            ]),
            Forms\Components\Section::make('Identidade visual')->columns(3)->schema([
                Forms\Components\ColorPicker::make('accent_color')->label('Cor accent'),
                Forms\Components\ColorPicker::make('accent_soft_color')->label('Accent soft'),
                Forms\Components\ColorPicker::make('accent_deep_color')->label('Accent deep'),
                Forms\Components\TextInput::make('icon')->label('Ícone (heroicon)')->maxLength(64),
                Forms\Components\FileUpload::make('logo_path')->label('Logo')->image()->directory('tenants/logos'),
            ]),
            Forms\Components\Section::make('Configuração')->columns(3)->schema([
                Forms\Components\Toggle::make('is_root')->label('Portal mãe'),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
                Forms\Components\TextInput::make('order')->label('Ordem')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')->label('#')->sortable(),
                Tables\Columns\ColorColumn::make('accent_color')->label('Cor'),
                Tables\Columns\TextColumn::make('short_name')->label('Sigla')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('full_name')->label('Nome completo')->limit(50),
                Tables\Columns\TextColumn::make('domain_dev')->label('Dev')->color('gray')->size('xs'),
                Tables\Columns\IconColumn::make('is_root')->label('Mãe')->boolean(),
                Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->defaultSort('order');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTenants::route('/'),
            'create' => Pages\CreateTenant::route('/create'),
            'edit' => Pages\EditTenant::route('/{record}/edit'),
        ];
    }
}

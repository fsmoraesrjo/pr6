<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Conteúdo';
    protected static ?string $navigationLabel = 'Serviços';
    protected static ?string $modelLabel = 'Serviço';
    protected static ?string $pluralModelLabel = 'Serviços';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Serviço')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::where('is_root', false)->orderBy('order')->pluck('short_name', 'id'))
                    ->required()->searchable(),
                Forms\Components\Select::make('category_id')->label('Categoria')
                    ->relationship('category', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('title')->label('Título')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->columnSpanFull(),
                Forms\Components\Textarea::make('summary')->label('Resumo')->rows(2)->maxLength(320)->columnSpanFull(),
                Forms\Components\RichEditor::make('description')->label('Descrição completa')->columnSpanFull(),
                Forms\Components\TextInput::make('audience')->label('Público-alvo')->columnSpanFull(),
            ]),
            Forms\Components\Section::make('Solicitação')->columns(2)->schema([
                Forms\Components\Select::make('request_type')->label('Como solicitar')->required()->options([
                    'internal_form' => 'Formulário interno',
                    'external_url' => 'Link externo',
                    'email' => 'E-mail',
                    'info_only' => 'Apenas informativo',
                ])->live(),
                Forms\Components\TextInput::make('request_url')->label('URL externa')->url()
                    ->visible(fn (Forms\Get $get) => $get('request_type') === 'external_url'),
                Forms\Components\TextInput::make('request_email')->label('E-mail de contato')->email()
                    ->visible(fn (Forms\Get $get) => $get('request_type') === 'email'),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Destaque'),
                Forms\Components\TextInput::make('order')->label('Ordem')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('tenant.short_name')->label('Diretoria')->badge(),
            Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->limit(60),
            Tables\Columns\TextColumn::make('audience')->label('Público')->limit(40)->toggleable(),
            Tables\Columns\TextColumn::make('request_type')->label('Tipo')->badge(),
            Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
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
            'index' => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit' => Pages\EditService::route('/{record}/edit'),
        ];
    }
}

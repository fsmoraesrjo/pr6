<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DocumentResource\Pages;
use App\Models\Document;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class DocumentResource extends Resource
{
    protected static ?string $model = Document::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Repositório';
    protected static ?string $navigationLabel = 'Documentos';
    protected static ?string $modelLabel = 'Documento';
    protected static ?string $pluralModelLabel = 'Documentos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Documento')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id'))
                    ->required()->searchable(),
                Forms\Components\Select::make('category_id')->label('Categoria')
                    ->relationship('category', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('title')->label('Título')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->columnSpanFull(),
                Forms\Components\Textarea::make('description')->label('Descrição')->rows(3)->columnSpanFull(),
                Forms\Components\Toggle::make('is_public')->label('Público')->default(true),
                Forms\Components\DateTimePicker::make('published_at')->label('Publicar em')->seconds(false)->default(now()),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('tenant.short_name')->label('Diretoria')->badge(),
            Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->limit(60),
            Tables\Columns\TextColumn::make('category.name')->label('Categoria'),
            Tables\Columns\TextColumn::make('downloads_count')->label('Downloads')->numeric(),
            Tables\Columns\IconColumn::make('is_public')->label('Público')->boolean(),
            Tables\Columns\TextColumn::make('published_at')->label('Publicação')->dateTime('d/m/Y'),
        ])
        ->filters([
            Tables\Filters\SelectFilter::make('tenant_id')->label('Diretoria')
                ->options(Tenant::orderBy('order')->pluck('short_name', 'id')),
        ])
        ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
        ->defaultSort('published_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\DocumentResource\RelationManagers\VersionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDocuments::route('/'),
            'create' => Pages\CreateDocument::route('/create'),
            'edit' => Pages\EditDocument::route('/{record}/edit'),
        ];
    }
}

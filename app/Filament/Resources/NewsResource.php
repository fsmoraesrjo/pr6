<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NewsResource\Pages;
use App\Models\News;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Conteúdo';
    protected static ?string $navigationLabel = 'Notícias';
    protected static ?string $modelLabel = 'Notícia';
    protected static ?string $pluralModelLabel = 'Notícias';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificação')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('tenant_id')
                        ->label('Diretoria')
                        ->options(Tenant::orderBy('order')->pluck('short_name', 'id'))
                        ->required()
                        ->searchable(),
                    Forms\Components\Select::make('category_id')
                        ->label('Categoria')
                        ->relationship('category', 'name')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('title')
                        ->label('Título')
                        ->required()
                        ->maxLength(200)
                        ->columnSpanFull()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(220)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('summary')
                        ->label('Resumo')
                        ->maxLength(320)
                        ->columnSpanFull()
                        ->rows(2),
                ]),

            Forms\Components\Section::make('Conteúdo')
                ->schema([
                    Forms\Components\RichEditor::make('body')
                        ->label('Texto')
                        ->columnSpanFull(),
                    Forms\Components\FileUpload::make('cover_path')
                        ->label('Imagem de capa')
                        ->image()
                        ->directory('news/covers')
                        ->maxSize(4096)
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Publicação')
                ->columns(3)
                ->schema([
                    Forms\Components\DateTimePicker::make('published_at')
                        ->label('Publicar em')
                        ->seconds(false)
                        ->default(now()),
                    Forms\Components\Toggle::make('is_featured')
                        ->label('Destaque'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.short_name')
                    ->label('Diretoria')
                    ->badge(),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título')
                    ->searchable()
                    ->limit(60),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Categoria')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Destaque')
                    ->boolean(),
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Publicação')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')
                    ->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id')),
                Tables\Filters\TernaryFilter::make('is_featured')
                    ->label('Destaque'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('published_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}

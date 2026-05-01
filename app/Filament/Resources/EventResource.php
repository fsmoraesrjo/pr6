<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EventResource\Pages;
use App\Models\Event;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Conteúdo';
    protected static ?string $navigationLabel = 'Agenda';
    protected static ?string $modelLabel = 'Evento';
    protected static ?string $pluralModelLabel = 'Eventos';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificação')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id'))
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('type')
                    ->label('Tipo')
                    ->options([
                        'reuniao' => 'Reunião',
                        'evento' => 'Evento',
                        'prazo' => 'Prazo',
                        'consulta' => 'Consulta pública',
                        'workshop' => 'Workshop',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('slug')->required()->columnSpanFull(),
                Forms\Components\Textarea::make('description')->label('Descrição')->columnSpanFull()->rows(3),
            ]),
            Forms\Components\Section::make('Quando e onde')->columns(2)->schema([
                Forms\Components\DateTimePicker::make('starts_at')->label('Início')->required()->seconds(false),
                Forms\Components\DateTimePicker::make('ends_at')->label('Término')->seconds(false),
                Forms\Components\TextInput::make('location')->label('Local')->maxLength(220)->columnSpanFull(),
                Forms\Components\Toggle::make('is_online')->label('Evento online')->live(),
                Forms\Components\TextInput::make('online_url')
                    ->label('URL da transmissão')
                    ->url()
                    ->visible(fn (Forms\Get $get) => $get('is_online')),
                Forms\Components\Toggle::make('is_public')->label('Público')->default(true),
                Forms\Components\Toggle::make('is_featured')->label('Destaque'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.short_name')->label('Diretoria')->badge(),
                Tables\Columns\TextColumn::make('title')->label('Título')->searchable()->limit(50),
                Tables\Columns\TextColumn::make('type')->label('Tipo')->badge(),
                Tables\Columns\TextColumn::make('starts_at')->label('Início')->dateTime('d/m/Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('location')->label('Local')->limit(30)->toggleable(),
                Tables\Columns\IconColumn::make('is_public')->label('Público')->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id')),
                Tables\Filters\SelectFilter::make('type')->options([
                    'reuniao' => 'Reunião', 'evento' => 'Evento', 'prazo' => 'Prazo',
                    'consulta' => 'Consulta', 'workshop' => 'Workshop',
                ]),
            ])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])])
            ->defaultSort('starts_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}

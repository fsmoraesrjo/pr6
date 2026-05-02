<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DataSubjectRequestResource\Pages;
use App\Models\DataSubjectRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class DataSubjectRequestResource extends Resource
{
    protected static ?string $model = DataSubjectRequest::class;
    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = 'LGPD';
    protected static ?string $navigationLabel = 'Direitos do titular';
    protected static ?string $modelLabel = 'Solicitação LGPD';
    protected static ?string $pluralModelLabel = 'Solicitações LGPD';

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', 'recebido')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Identificação do titular')->columns(2)->schema([
                Forms\Components\TextInput::make('requester_name')->label('Nome')->disabled(),
                Forms\Components\Placeholder::make('cpf_masked')
                    ->label('CPF')
                    ->content(fn (DataSubjectRequest $record) => $record->cpf_plain ?? '—'),
                Forms\Components\Placeholder::make('email')
                    ->label('E-mail')
                    ->content(fn (DataSubjectRequest $record) => $record->email_plain ?? '—'),
                Forms\Components\TextInput::make('request_type')
                    ->label('Tipo')
                    ->formatStateUsing(fn ($state) => DataSubjectRequest::REQUEST_TYPES[$state] ?? $state)
                    ->disabled(),
            ]),
            Forms\Components\Section::make('Solicitação')->schema([
                Forms\Components\Textarea::make('description')->label('Descrição original')->rows(5)->disabled(),
                Forms\Components\Placeholder::make('deadline')
                    ->label('Prazo legal')
                    ->content(fn (DataSubjectRequest $r) => $r->deadline_at?->format('d/m/Y') . ' (' . $r->deadline_at?->diffForHumans() . ')'),
            ]),
            Forms\Components\Section::make('Resposta da PR-6')->schema([
                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options(DataSubjectRequest::STATUSES)
                    ->required(),
                Forms\Components\Textarea::make('response')->label('Resposta ao titular')->rows(6),
                Forms\Components\DateTimePicker::make('completed_at')->label('Concluído em')->seconds(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('requester_name')->label('Solicitante')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('request_type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn ($state) => DataSubjectRequest::REQUEST_TYPES[$state] ?? $state),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'recebido' => 'warning',
                        'em_analise' => 'info',
                        'concluido' => 'success',
                        'rejeitado' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => DataSubjectRequest::STATUSES[$state] ?? $state),
                Tables\Columns\TextColumn::make('deadline_at')
                    ->label('Prazo')
                    ->dateTime('d/m/Y')
                    ->color(fn (DataSubjectRequest $r) => $r->isOverdue() ? 'danger' : ($r->daysRemaining() < 5 ? 'warning' : 'gray'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('Recebida')->dateTime('d/m/Y H:i')->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options(DataSubjectRequest::STATUSES),
                Tables\Filters\SelectFilter::make('request_type')->label('Tipo')->options(DataSubjectRequest::REQUEST_TYPES),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->defaultSort('deadline_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDataSubjectRequests::route('/'),
            'edit' => Pages\EditDataSubjectRequest::route('/{record}/edit'),
        ];
    }
}

<?php

namespace App\Filament\Resources\DocumentResource\RelationManagers;

use App\Models\DocumentVersion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class VersionsRelationManager extends RelationManager
{
    protected static string $relationship = 'versions';
    protected static ?string $title = 'Versões';
    protected static ?string $modelLabel = 'Versão';
    protected static ?string $pluralModelLabel = 'Versões';
    protected static ?string $recordTitleAttribute = 'version_label';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('version_label')
                ->label('Rótulo da versão')
                ->placeholder('ex.: v1.0, 2026.1, Edição revisada')
                ->required()
                ->maxLength(32),
            Forms\Components\FileUpload::make('file_path')
                ->label('Arquivo')
                ->disk('public')
                ->directory(fn ($livewire) => 'documents/' . $livewire->ownerRecord->tenant_id . '/' . $livewire->ownerRecord->id)
                ->preserveFilenames()
                ->maxSize(50 * 1024)
                ->required()
                ->columnSpanFull()
                ->afterStateUpdated(function ($state, callable $set) {
                    if ($state instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile) {
                        $set('original_name', $state->getClientOriginalName());
                        $set('size_bytes', $state->getSize());
                        $set('mime_type', $state->getMimeType());
                    }
                }),
            Forms\Components\TextInput::make('original_name')->label('Nome original')->columnSpanFull(),
            Forms\Components\Textarea::make('changelog')
                ->label('O que mudou nesta versão')
                ->rows(3)
                ->columnSpanFull(),
            Forms\Components\Toggle::make('is_current')
                ->label('Versão vigente')
                ->helperText('Marca esta como a versão atual e desativa as anteriores.')
                ->default(true),
            Forms\Components\Hidden::make('size_bytes'),
            Forms\Components\Hidden::make('mime_type'),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('version_label')->label('Versão')->weight('bold'),
            Tables\Columns\IconColumn::make('is_current')->label('Vigente')->boolean(),
            Tables\Columns\TextColumn::make('original_name')->label('Arquivo')->limit(40),
            Tables\Columns\TextColumn::make('size_bytes')
                ->label('Tamanho')
                ->formatStateUsing(fn ($state) => $state ? number_format($state / 1024, 0, ',', '.') . ' KB' : '—'),
            Tables\Columns\TextColumn::make('created_at')->label('Adicionada em')->dateTime('d/m/Y H:i')->sortable(),
        ])
        ->headerActions([
            Tables\Actions\CreateAction::make()
                ->after(fn (Model $record) => $record->is_current ? $this->syncCurrentVersion($record) : null),
        ])
        ->actions([
            Tables\Actions\Action::make('mark_current')
                ->label('Tornar vigente')
                ->icon('heroicon-o-check-badge')
                ->visible(fn (DocumentVersion $record) => ! $record->is_current)
                ->action(fn (DocumentVersion $record) => $this->syncCurrentVersion($record)),
            Tables\Actions\Action::make('download')
                ->label('Baixar')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (DocumentVersion $record) => Storage::disk('public')->url($record->file_path))
                ->openUrlInNewTab(),
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->defaultSort('created_at', 'desc');
    }

    protected function syncCurrentVersion(DocumentVersion $record): void
    {
        DocumentVersion::where('document_id', $record->document_id)
            ->where('id', '!=', $record->id)
            ->update(['is_current' => false]);
        $record->update(['is_current' => true]);
        $record->document->update(['current_version_id' => $record->id]);
    }
}

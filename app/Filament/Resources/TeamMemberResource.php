<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TeamMemberResource\Pages;
use App\Models\TeamMember;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Estrutura';
    protected static ?string $navigationLabel = 'Equipe';
    protected static ?string $modelLabel = 'Membro';
    protected static ?string $pluralModelLabel = 'Equipe';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Pessoa')->columns(2)->schema([
                Forms\Components\Select::make('tenant_id')->label('Diretoria')
                    ->options(Tenant::orderBy('order')->pluck('short_name', 'id'))
                    ->required()->searchable(),
                Forms\Components\Select::make('org_unit_id')->label('Unidade')
                    ->relationship('orgUnit', 'name')->searchable()->preload(),
                Forms\Components\TextInput::make('name')->label('Nome')->required()->columnSpanFull(),
                Forms\Components\TextInput::make('role_title')->label('Cargo')->required()->columnSpanFull(),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\TextInput::make('phone')->label('Telefone')->mask('(99) 9999-9999'),
                Forms\Components\FileUpload::make('photo_path')->label('Foto')->image()->avatar()->directory('team/photos')->columnSpanFull(),
                Forms\Components\Textarea::make('bio')->label('Bio')->rows(4)->columnSpanFull(),
                Forms\Components\Toggle::make('is_head')->label('Titular da unidade'),
                Forms\Components\Toggle::make('is_active')->label('Ativo')->default(true),
                Forms\Components\TextInput::make('order')->label('Ordem')->numeric()->default(0),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\ImageColumn::make('photo_path')->label('Foto')->circular(),
            Tables\Columns\TextColumn::make('tenant.short_name')->label('Diretoria')->badge(),
            Tables\Columns\TextColumn::make('name')->label('Nome')->searchable(),
            Tables\Columns\TextColumn::make('role_title')->label('Cargo')->limit(40),
            Tables\Columns\TextColumn::make('orgUnit.name')->label('Unidade')->toggleable(),
            Tables\Columns\IconColumn::make('is_head')->label('Titular')->boolean(),
            Tables\Columns\IconColumn::make('is_active')->label('Ativo')->boolean(),
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
            'index' => Pages\ListTeamMembers::route('/'),
            'create' => Pages\CreateTeamMember::route('/create'),
            'edit' => Pages\EditTeamMember::route('/{record}/edit'),
        ];
    }
}

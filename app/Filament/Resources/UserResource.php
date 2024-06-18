<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Collection;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Altwaireb\CountriesStatesCities\Models\City;
use Altwaireb\CountriesStatesCities\Models\State;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;
use Filament\Forms\Set;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'Employee';
    protected static ?string $navigationGroup = 'Employee Management';
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Personal Info')->columns(3)
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
               
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->hiddenOn('edit')
                    ->required(),
                ]),
                Section::make('Address Info')->columns(3)
                ->schema([
                   forms\Components\Select::make('country_id')
                   ->relationship(name : 'country', titleAttribute:'name')
                   ->searchable()
                   ->preload()
                   ->live()
                   ->afterStateUpdated(function(Set $set){
                    $set('state_id', 'null');
                    $set('city_id', 'null');
                   })
                   ->required(),
                   forms\Components\Select::make('state_id')
                        ->options(fn (Get $get): Collection => State::query()
                        ->where('country_id', $get('country_id'))
                        ->pluck('name','id'))
                   ->searchable()
                   ->preload()
                   ->live()
                   ->afterStateUpdated(function(Set $set){
                    $set('city_id', 'null');
                   })
                   ->required(),
                   forms\Components\Select::make('city_id')
                   ->options(fn (Get $get): Collection => City::query()
                        ->where('state_id', $get('state_id'))
                        ->pluck('name','id'))
                   ->searchable()
                   ->live()
                   ->preload()
                   ->required(),
                   forms\Components\TextInput::make('address')
                   ->required(),
                   forms\Components\TextInput::make('postal_code')
                   ->required(),
                   Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                ])

               
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('address')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('postal_code')
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),   
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}

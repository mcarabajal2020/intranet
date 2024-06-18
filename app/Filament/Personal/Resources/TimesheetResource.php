<?php

namespace App\Filament\Personal\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Timesheet;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Personal\Resources\TimesheetResource\Pages;
use App\Filament\Personal\Resources\TimesheetResource\RelationManagers;

class TimesheetResource extends Resource
{
    protected static ?string $model = Timesheet::class;

    protected static ?string $navigationIcon = 'heroicon-o-table-cells';
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id',Auth::user()->id )->orderBy('day_in','desc');
    }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('calendar_id')
                ->relationship(name: 'calendar', titleAttribute: 'name')
                ->required(),
                Forms\Components\Select::make('type')
                ->options([
                    'work' => 'Working',
                    'pause' => 'Pause',
                ])
                ->required(),
            Forms\Components\DateTimePicker::make('day_in')
                ->required(),
            Forms\Components\DateTimePicker::make('day_out')
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('calendar.name')->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('type')
                ->searchable(),
            Tables\Columns\TextColumn::make('day_in')->searchable()
                ->dateTime()
                ->sortable(),
            Tables\Columns\TextColumn::make('day_out')->searchable()
                ->dateTime()
                ->sortable(),
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
             SelectFilter::make('type')
             ->options([
                 'work' => 'Working',
                 'pause' => 'Pause',
            ])
        ])
        ->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])
        ->bulkActions([
            Tables\Actions\BulkActionGroup::make([
                Tables\Actions\DeleteBulkAction::make(),
                ExportBulkAction::make()->exports([
                ExcelExport::make('table')
                    ->fromTable()
                    ->withFilename('Timesheet_'.date('Y-m-d') . '_export')
                    ->withColumns([
                        Column::make('User'),
                        Column::make('created_at'),
                        Column::make('deleted_at'),
                    ]),
                ExcelExport::make('form')->fromForm()
                    ->askForFilename()
                    ->askForWriterType(),
                ]) 
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
            'index' => Pages\ListTimesheets::route('/'),
            'create' => Pages\CreateTimesheet::route('/create'),
            'edit' => Pages\EditTimesheet::route('/{record}/edit'),
        ];
    }
}

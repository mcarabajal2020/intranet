<?php

namespace App\Filament\Personal\Resources\TimesheetResource\Pages;

use Carbon\Carbon;
use Filament\Actions;
use App\Models\Timesheet;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Personal\Resources\TimesheetResource;

class ListTimesheets extends ListRecords
{
    protected static string $resource = TimesheetResource::class;

    protected function getHeaderActions(): array
    {
        $lastTimesheet = Timesheet::where('user_id', Auth::user()->id)->orderBy('id','desc')->first();
        if($lastTimesheet == null){
            return [
                Action::make('inWork')
            ->label('Entrar a trabajar')->color('success')->requiresConfirmation()
            ->action(function(){
                $user = Auth::user();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->type = 'work';
                $timesheet->save();

                Notification::make()
                ->title('Comienzas a trabajar')
                ->color('success')
                ->success()
                ->send();
            }),
            Actions\CreateAction::make(),
            ];
        }
        return [
           
            Action::make('inWork')
            ->label('Entrar a trabajar')->color('success')->requiresConfirmation()
            ->visible(!$lastTimesheet->day_out == null)
            ->disabled($lastTimesheet->day_out == null)
            ->action(function(){
                $user = Auth::user();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = $user->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->type = 'work';
                $timesheet->save();
                
                Notification::make()
                ->title('Comienzas a trabajar')
                ->body('Has comenzado a trabajar a las '.Carbon::now())
                ->color('success')
                ->success()
                ->send();
            }),
            Action::make('stopWork')
            ->label('Parar de trabajar')->color('success')->requiresConfirmation()
            ->visible($lastTimesheet->day_out == null && $lastTimesheet->type!='pause')
            ->disabled(!$lastTimesheet->day_out == null)
            ->action(function() use($lastTimesheet){
                $lastTimesheet->day_out = Carbon::now();
                $lastTimesheet->save();

                Notification::make()
                ->title('Paraste de Trabajar')
                ->success()
                ->send();
            }),
            Action::make('inPause')
            ->label('Comenzar Pausa')->color('info')->requiresConfirmation()
            ->visible($lastTimesheet->day_out == null && $lastTimesheet->type!='pause')
            ->disabled(!$lastTimesheet->day_out == null)
            ->action(function() use($lastTimesheet){
                $lastTimesheet->day_out = Carbon::now();
                $lastTimesheet->save();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = Auth::user()->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->type = 'pause';
                $timesheet->save();

                Notification::make()
                ->title('Comienzas la Pausa')
                ->color('info')
                ->info()
                ->send();
            }),
            Action::make('stopPause')
            ->label('Parar Pausa')->color('info')->requiresConfirmation()
            ->visible($lastTimesheet->day_out == null && $lastTimesheet->type=='pause')
            ->disabled(!$lastTimesheet->day_out == null)
            ->action(function() use($lastTimesheet){
                $lastTimesheet->day_out = Carbon::now();
                $lastTimesheet->save();
                $timesheet = new Timesheet();
                $timesheet->calendar_id = 1;
                $timesheet->user_id = Auth::user()->id;
                $timesheet->day_in = Carbon::now();
                $timesheet->type = 'work';
                $timesheet->save();

                Notification::make()
                ->title('Comienzas de nuevo a Trabajar')
                ->color('info')
                ->info()
                ->send();
            }),
            Actions\CreateAction::make(),
        ];
    }
}

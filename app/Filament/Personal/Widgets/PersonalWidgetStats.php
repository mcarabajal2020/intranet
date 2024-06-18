<?php

namespace App\Filament\Personal\Widgets;

use App\Models\User;
use App\Models\Holiday;
use App\Models\Timesheet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class PersonalWidgetStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Pending Holidays', $this->getPendingHoliday(Auth::user())),
            Stat::make('Approved Holidays', $this->getApprovedHoliday(Auth::user())),
            Stat::make('Total Work', $this->getTotalWork(Auth::user())),
            Stat::make('Total Pause', $this->getTotalPause(Auth::user())),
        ];
    }
    protected function getPendingHoliday(User $user){
        $totalPendingHildays = Holiday::where('user_id', $user->id)->where('type','pending')->get()->count();
        return $totalPendingHildays;
    }

    protected function getApprovedHoliday(User $user){
        $totalApprovedHolidays = Holiday::where('user_id', $user->id)->where('type','approved')->get()->count();
        return $totalApprovedHolidays;
    }

    protected function getTotalWork(User $user){
        $timesheets = Timesheet::where('user_id', $user->id)
        ->where('type', 'work')->whereDate('created_at', Carbon::today())->get();
       $sumHours = 0;
        foreach($timesheets as $timesheet){
            $startTime = Carbon::parse($timesheet->day_in);
            $finishTime = Carbon::parse($timesheet->day_out);

            $totalDuration = $finishTime->diffInSeconds($startTime);
            $sumHours = $sumHours + $totalDuration;
            //dd($sumHours);
        }
        $tiempoFormato = gmdate("H:i:s", $sumHours);
    
        return $tiempoFormato;
    }
        protected function getTotalPause(User $user){
            $timesheets = Timesheet::where('user_id', $user->id)
            ->where('type', 'pause')->whereDate('created_at', Carbon::today())->get();
           $sumHours = 0;
            foreach($timesheets as $timesheet){
                $startTime = Carbon::parse($timesheet->day_in);
                $finishTime = Carbon::parse($timesheet->day_out);
    
                $totalDuration = $finishTime->diffInSeconds($startTime);
                $sumHours = $sumHours + $totalDuration;
                //dd($sumHours);
            }
            $tiempoFormato = gmdate("H:i:s", $sumHours);
        
            return $tiempoFormato;
        }
}
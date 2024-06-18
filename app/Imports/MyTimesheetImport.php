<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Calendar;
use App\Models\Timesheet;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MyTimesheetImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) 
        {
            //dd($row);
            $calendar_id = Calendar::where('name', $row['calendario'])->first();
            if($calendar_id != null){
                Timesheet::create([
                    'calendar_id' => $calendar_id->id,
                    'user_id' => Auth::user()->id,
                    'type' => $row['tipo'],
                    'day_in' =>$row['entrada'],
                    'day_out' =>$row['salida'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }
    }
}

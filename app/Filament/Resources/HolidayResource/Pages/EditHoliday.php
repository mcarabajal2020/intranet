<?php

namespace App\Filament\Resources\HolidayResource\Pages;

use Filament\Actions;
use App\Mail\HolidayDecline;
use App\Mail\HolidayApproved;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\HolidayResource;

class EditHoliday extends EditRecord
{
    protected static string $resource = HolidayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
          // SEND EMAIL ONLY IF APPROVED
          if($record->type == 'approved'){
            $user = User::find($record->user_id);
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day,
            );
            Mail::to($user)->send(new HolidayApproved($data));
            $recipient =auth()->user();
            Notification::make()
                ->title('Solicitud de Vacaciones')
                ->body("El dia " .$data['day']. " esta aprobado")
                ->sendToDatabase($recipient);
          }
          elseif($record->type == 'decline'){
            $user = User::find($record->user_id);
            $data = array(
                'name' => $user->name,
                'email' => $user->email,
                'day' => $record->day,
            );
            Mail::to($user)->send(new HolidayDecline($data));
            $recipient =auth()->user();
            Notification::make()
                ->title('Solicitud de Vacaciones')
                ->body("El dia " .$data['day']. " esta rechazado")
                ->sendToDatabase($recipient);
          }
        return $record;
    }
}

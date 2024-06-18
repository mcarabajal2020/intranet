<?php

namespace App\Filament\Personal\Resources\HolidayResource\Pages;

use Filament\Actions;
use App\Mail\HolidayPending;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Personal\Resources\HolidayResource;
use Filament\Notifications\Notification;

class CreateHoliday extends CreateRecord
{
    protected static string $resource = HolidayResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
{
    $data['user_id'] = Auth::user()->id;
    $data['type'] = 'pending';
    $userAdmin = User::Find(1);
    $dataToSend = array(
        'day' => $data['day'],
        'name' => User::find($data['user_id'])->name,
        'email' => User::find($data['user_id'])->email,
    );
    Mail::to($userAdmin)->send(new HolidayPending($dataToSend));
    //Notification::make()
    //->title('Solicitud de vacaciones')
    //->body("El dia " .$data['day']. " esta pendiente de aprobar")
    //->warning()
    //->send();

    $recipient =auth()->user();
    Notification::make()
        ->title('Solicitud de Vacaciones')
        ->body("El dia " .$data['day']. " esta pendiente de aprobar")
        ->sendToDatabase($recipient);
    return $data;
}


}

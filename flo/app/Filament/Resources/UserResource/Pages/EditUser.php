<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use App\Filament\Resources\AgencyResource;
use App\Models\AgenciesUsers;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array {
        //  get count of users in each agency
        if (isset($data['agencies'])) {
            foreach ($data['agencies'] as $agency) {
                $agencyUserCount = AgenciesUsers::where('agency_id', $agency)->count();
                if ($agencyUserCount >= 1000) {
                    Notification::make()
                        ->warning()
                        ->title('Agency User Count is full!')
                        ->body('Choose a different agency to continue.')
                        ->persistent()
                        ->send();
                    
                    $this->halt();
                }
            }
        }

        $data['last_edited_by_id'] = auth()->id();
        $data['firstname'] = ucfirst($data['firstname']);
        $data['lastname'] = ucfirst($data['lastname']);
        $data['address'] = trim($data['address']);
    
        return $data;
    }
}

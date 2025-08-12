<?php

namespace App\Filament\Resources\InviteeResource\Pages;

use App\Filament\Resources\InviteeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Str;
use App\Support\SmsMessageBuilder;
use App\Jobs\SendSmsJob;

class ManageInvitees extends ManageRecords
{
    protected static string $resource = InviteeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Invitee')
                ->icon('heroicon-o-user-plus')
                ->createAnother(false)
                //modal create button to read Send Invitation
                ->modalHeading('Create Invitation')
                ->modalWidth('md')
                ->modalSubmitActionLabel('Send Invitation')
                ->mutateFormDataUsing(function (array $data): array {
                    $password = Str::random(6);
                    $data['password'] = Str::upper($password);
                    return $data;
                })
                ->after(function (\App\Models\Invitee $record): void {
                    $code = $record->password; // or $record->invite_code if you renamed it
                    $name = $record->name;
                    $message = SmsMessageBuilder::inviteLong($name, $code); // or inviteShort($name, $code)
                    SendSmsJob::dispatch($record->phone, $message);
                })
        ];
    }
}

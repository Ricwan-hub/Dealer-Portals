<?php

namespace App\Filament\Admin\Resources\InvitationResource\Pages;

use App\Filament\Admin\Resources\InvitationResource;
use Illuminate\Support\Facades\Notification as EmailNotification;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use App\Models\Invitation;
use App\Notifications\RegisterInviteNotification;
use Filament\Support\Enums\Alignment;
use App\Models\User;

class ManageInvitations extends ManageRecords
{
    protected static string $resource = InvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('inviteUser')
                ->modalWidth('md')
                ->modalHeading('Rigistration Invite')
                ->modalDescription('Send an invitation link to a dealer to register in the portal.')
                ->modalAlignment('Alignment::Start')
                ->label('Invite Dealer')
                ->form([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(table: Invitation::class)
                        ->unique(table: User::class)
                        ->validationMessages([
                            'unique' => 'This :attribute belongs to a registered user or is already invited. Please check',
                        ])
                        ->placeholder('Enter the dealer\'s email address'),
                ])
                ->action(function ($data){
                    $invitation = Invitation::create(['email' => $data['email']]);

                    //Send invitation link
                    EmailNotification::route('mail', $invitation->email)->notify(new RegisterInviteNotification());

                    Notification::make('invitedSuccess')
                        ->body('User invitation sent')
                        ->success()
                        ->send();
                }),
        ];
    }
}

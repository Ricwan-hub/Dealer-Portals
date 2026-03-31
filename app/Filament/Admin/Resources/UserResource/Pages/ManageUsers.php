<?php

namespace App\Filament\Admin\Resources\UserResource\Pages;

use Illuminate\Support\Facades\Notification as EmailNotification;
use App\Filament\Admin\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use App\Models\Invitation;
use App\Notifications\RegisterInviteNotification;
use Illuminate\Database\Eloquent\Model;
use Filament\Support\Enums\Alignment;
use App\Filament\Admin\Imports\UserImporter;
use App\Models\User;
use App\Models\DealerProfile;
use App\Notifications\WelcomeEmailNotification;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Create New User')
                ->modalHeading('Create New User')
                ->color('gray')
                ->modalWidth('md')
                ->mutateFormDataUsing(function (array $data): array {
                    $password = Str::random(16);

                    $data['password'] = Hash::make($password);
                    $data['plain_password'] = $password;

                    return $data;
                })
                ->after(function (Model $record, array $data) {

                    $record->markEmailAsVerified();

                    if($record->hasRole('dealer'))
                    {
                        $profile = DealerProfile::create([]);
                        $profile->user()->save(User::find($record->id));

                        $record->notify(new WelcomeEmailNotification($record, $data['plain_password']));

                        Notification::make()
                            ->title('Registration Successful!')
                            ->success()
                            ->body('Welcome! You have now been registered to the dealers\' portal. Please use this account to stay updated on the latest parts prices and availability.')
                            ->sendToDatabase($record);
                    } else {
                        Notification::make()
                        ->title('New Admin User Created')
                        ->success()
                        ->body('Copy Password: '.$data['plain_password']. ' Delete this message after copying.')
                        ->sendToDatabase(auth()->user());
                    };

                }),
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

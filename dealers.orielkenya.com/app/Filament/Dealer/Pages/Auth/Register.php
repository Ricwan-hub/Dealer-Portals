<?php

namespace App\Filament\Dealer\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Pages\Auth\Register as BaseRegister;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Auth\VerifyEmail;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Facades\Filament;
use App\Models\Invitation;
use App\Models\User;
use App\Models\DealerProfile;
use Filament\Actions\Action;
use Filament\Support\Facades\FilamentIcon;

class Register extends BaseRegister 
{
    protected static string $view = 'filament.dealer.pages.auth.register';
    public string $invitation;
    private Invitation $invitationModel;
    public function mount(): void
    {
        // Verify the URL signature
        if (!request()->hasValidSignature()) {
            abort(404);
        }
        
        parent::mount();

        $this->invitation = request()->query('email', '');
        
        $this->invitationModel = Invitation::where('email', $this->invitation)->firstOrFail();
       
        $this->form->fill([
            'email' => $this->invitationModel->email,
        ]);
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getNameFormComponent(),
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        $this->getPasswordConfirmationFormComponent(),
                    ])
                    ->statePath('data'),
            ),
        ];
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            Notification::make()
                ->title(__('filament-panels::pages/auth/register.notifications.throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]))
                ->body(array_key_exists('body', __('filament-panels::pages/auth/register.notifications.throttled') ?: []) ? __('filament-panels::pages/auth/register.notifications.throttled.body', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => ceil($exception->secondsUntilAvailable / 60),
                ]) : null)
                ->danger()
                ->send();

            return null;
        }

        $this->invitationModel = Invitation::where('email', $this->invitation)->firstOrFail();

        $data = $this->form->getState();

        $data['email'] = $this->invitationModel->email;
        $data['active'] = true;

        $user = $this->getUserModel()::create($data);

        $user->markEmailAsVerified();

        $user->assignRole('dealer');

        $profile = DealerProfile::create([]);

        $profile->user()->save(User::find($user->id));

        $this->sendWelcomeNotification($user);

        Filament::auth()->login($user);

        $user->update([
            'last_login_at' => \Illuminate\Support\Carbon::now()->toDateTimeString(),
            'last_login_ip' => request()->getClientIp()
        ]);
        
        

        session()->regenerate();

        $updateInvite = $this->invitationModel;
        $updateInvite->accepted = true;
        $updateInvite->accepted_at = now();
        $updateInvite->save();
 
        return app(RegistrationResponse::class);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->readonly();
    }

    protected function sendWelcomeNotification(Model $user): void
    {
        Notification::make()
            ->title('Registration Successful!')
            ->success()
            ->body('Welcome! You have now been registered to the dealers\' portal. Please use this account to stay updated on the latest parts prices and availability.')
            ->sendToDatabase($user);

    }

    public function getHeading(): string
    {
        return __('Accept Invitation');
    }
    
}

<?php

namespace App\Filament\Dealer\Pages;

use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Contracts\Auth\Authenticatable;
use Filament\Notifications\Notification;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\Tabs;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Pages\Concerns;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Exception;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $slug = 'account/profile';
    protected static string $view = 'filament.dealer.pages.edit-profile';
    protected static ?string $title = 'My Profile';
    protected static ?int $navigationSort = 2;
    protected static bool $shouldRegisterNavigation = true;
    public ?array $businessData = [];
    public ?array $profileData = [];
    public ?array $passwordData = [];

    public function mount(): void
    {
        $this->fillForms();
    }

    protected function getForms(): array
    {
        return [
            'editBusinessForm',
            'editProfileForm',
            'editPasswordForm',
        ];
    }

    public function editBusinessForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Business Information')
                ->aside()
                ->description('Update your business\'s profile information')
                ->relationship('profile')
                ->schema([
                    Forms\Components\TextInput::make('business_name')
                        ->required(),
                    Forms\Components\TextInput::make('phone_no')
                        ->required()
                        ->unique(ignoreRecord: true),
                    Forms\Components\TextInput::make('address_street')
                        ->label('Street')
                        ->required(),
                    Forms\Components\TextInput::make('address_city')
                        ->label('City')
                        ->required(),
                    Forms\Components\TextInput::make('address_county')
                        ->label('County')
                        ->required(),
                ])->columns(2),
            ])
            ->model($this->getUser())
            ->statePath('businessData');
    }

    public function editProfileForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile Information')
                ->aside()
                ->description('Update your account\'s profile information and email address.')
                ->schema([
                    Forms\Components\TextInput::make('name')
                    ->required(),
                    Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),
                ]),
            ])
            ->model($this->getUser())
            ->statePath('profileData');
    }

    public function editPasswordForm(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Update Password')
                ->aside()
                ->description('Ensure your account is using long, random password to stay secure.')
                ->schema([
                Forms\Components\TextInput::make('Current password')
                ->password()
                ->required()
                ->currentPassword(),
                Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->rule(Password::default())
                ->autocomplete('new-password')
                ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                ->live(debounce: 500)
                ->same('passwordConfirmation'),
                Forms\Components\TextInput::make('passwordConfirmation')
                ->password()
                ->required()
                ->dehydrated(false),
                ]),
            ])
            ->model($this->getUser())
            ->statePath('passwordData');
    }

    protected function getUser(): Authenticatable & Model
    {
        $user = Filament::auth()->user();

        if (! $user instanceof Model) {
            throw new Exception('The authenticated user object must be an Eloquent model to allow the profile page to update it.');
        }

        return $user;
    }
    protected function fillForms(): void
    {
        $data = $this->getUser()->attributesToArray();
        
        $this->editBusinessForm->fill($data);
        
        $this->editProfileForm->fill($data);

        $this->editPasswordForm->fill();
    }

    protected function getUpdateBusinessFormActions(): array
    {
        return [
            Action::make('updateBusinessAction')
                ->extraAttributes(['class' => 'ms-auto'])
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editBusinessForm'),
        ];
    }

    protected function getUpdateProfileFormActions(): array
    {
        return [
            Action::make('updateProfileAction')
                ->extraAttributes(['class' => 'ms-auto'])
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editProfileForm'),
        ];
    }
    protected function getUpdatePasswordFormActions(): array
    {
        return [
            Action::make('updatePasswordAction')
                ->extraAttributes(['class' => 'ms-auto'])
                ->label(__('filament-panels::pages/auth/edit-profile.form.actions.save.label'))
                ->submit('editPasswordForm'),
        ];
    }

    public function updateBusiness(): void
    {
        $data = $this->editBusinessForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);
        $this->sendSuccessNotification(); 
    }

    public function updateProfile(): void
    {
        $data = $this->editProfileForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);
        $this->sendSuccessNotification(); 
    }
    public function updatePassword(): void
    {
        $data = $this->editPasswordForm->getState();
        $this->handleRecordUpdate($this->getUser(), $data);
        if (request()->hasSession() && array_key_exists('password', $data)) {
            request()->session()->put(['password_hash_' . Filament::getAuthGuard() => $data['password']]);
        }
        $this->editPasswordForm->fill();
        $this->sendSuccessNotification(); 
    }
    private function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        return $record;
    }

    private function sendSuccessNotification(): void
    {
        Notification::make()
            ->success()
            ->title(__('filament-panels::pages/auth/edit-profile.notifications.saved.title'))
            ->send();
    }
    
}

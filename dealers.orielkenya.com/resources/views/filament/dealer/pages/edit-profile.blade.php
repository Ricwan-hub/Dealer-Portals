<x-filament-panels::page>

    <x-filament-panels::form wire:submit.prevent="updateBusiness"> 

        {{ $this->editBusinessForm }}

        <x-filament-panels::form.actions
            :actions="$this->getUpdateProfileFormActions()"
            type="submit"
            wire:click="updateBusiness"
        />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit.prevent="updateProfile"> 

            {{ $this->editProfileForm }}

            <x-filament-panels::form.actions
                :actions="$this->getUpdateProfileFormActions()"
                type="submit"
                wire:click="updateProfile"
            />
    </x-filament-panels::form>

    <x-filament-panels::form wire:submit.prevent="updatePassword">

        {{ $this->editPasswordForm }}

        <x-filament-panels::form.actions 
            :actions="$this->getUpdatePasswordFormActions()"
            type="submit"
            wire:target="updateProfile"
        />

    </x-filament-panels::form>

</x-filament-panels::page>
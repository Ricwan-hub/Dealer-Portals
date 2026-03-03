<?php
 
namespace App\Http\Responses;
 
use Filament\Http\Responses\Auth\Contracts\LogoutResponse as Responsable;
use Illuminate\Http\RedirectResponse;
use Filament\Facades\Filament;
 
class FilamentLogoutResponse implements Responsable
{
    public function toResponse($request): RedirectResponse
    {

        if (Filament::getCurrentPanel()->getId() === 'admin') {
            return redirect()->route('filament.admin.auth.login');
        }
        
        if (Filament::getCurrentPanel()->getId() === 'customer') {
            return redirect()->url('/');
        }
    }
}
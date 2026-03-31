<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Route;
use Filament\Notifications\Notification;

class ProfileCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Route::currentRouteName() == 'filament.dealer.pages.account.profile' || Route::currentRouteName() == 'filament.dealer.auth.logout') {
            return $next($request);
        }
        
        $user = $request->user();
        //dd($user);
        if ($user && !$user->hasCompletedProfile()) {

            Notification::make('incompleteProfile')
                ->title('Notice')
                ->icon('heroicon-m-exclamation-triangle')
                ->iconColor('danger')
                ->body('Please complete your business profile to proceed.')
                ->color('warning')
                ->send();

            return redirect()->route('filament.dealer.pages.account.profile');
        }
        
        return $next($request);
    }
}

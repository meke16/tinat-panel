<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Notifications\Notification;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class LoginPage extends BaseLogin
{
    // 1. Update the return type to the required Filament contract
    public function authenticate(): ?LoginResponse 
    {
        try {
            $response = parent::authenticate();
            
            if ($response) {
                /** @var \App\Models\User|null $user */
                $user = Auth::user();

                if ($user) {
                    Notification::make()
                        ->title('Welcome back, ' . $user->name . '!')
                        ->body('You have successfully logged in.' . $user->email)
                        ->success()
                        ->duration(5000)
                        ->send();
                }
            }

            return $response;
        } catch (ValidationException $e) {
            throw $e;
        }
    }
}
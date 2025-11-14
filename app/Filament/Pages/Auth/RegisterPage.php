<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\HtmlString;

class RegisterPage extends BaseRegister
{
    public function register(): ?RegistrationResponse
    {
        // Check a global user limit before attempting registration.
        // If the limit is reached, show a danger notification and redirect to `/`.
        $maxUsers = 3; // make configurable if you like
        if (User::count() >= $maxUsers) {
            Notification::make()
                ->title('Registration closed')
                ->body('We are not accepting new registrations right now.')
                ->danger()
                ->persistent()
                ->duration(3000)
                ->send();

             $this->redirect(filament()->getLoginUrl());

            return null;
        }

        return parent::register();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function handleRegistration(array $data): Model
    {
        // Validate a strong password server-side using Laravel's Password rule.
        Validator::make($data, [
            'password' => [
                'required',
                'string',
                Password::min(8)->mixedCase()->numbers()->symbols(),
            ],
        ], [
            'password.required' => 'A password is required.',
        ])->validate();

        $user = parent::handleRegistration($data);

        Notification::make()
            ->title('Welcome, ' . $user->name . '!')
            ->body('Your account has been successfully created: ' . $user->email)
            ->success()
            ->send();

        return $user;
    }

    protected function getPasswordFormComponent(): \Filament\Schemas\Components\Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::auth/pages/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->helperText(new HtmlString('At least 8 character.<br>include upper & lower case letter.<br> a number and a symbol.'));
    }
}

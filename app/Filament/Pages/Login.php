<?php

namespace App\Filament\Pages;

use Filament\Schemas\Components\Component;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Exception;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Illuminate\Validation\ValidationException;
use Filament\Schemas\Schema;

class Login extends \Filament\Auth\Pages\Login
{
    public function getHeading(): string
    {
        return config('app.name');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getLoginFormComponent(),
                $this->getPasswordFormComponent(),
                $this->getRememberFormComponent(),
            ]);
    }

    protected function getLoginFormComponent(): Component
    {
        return TextInput::make('login')
            ->label('Email / Username / Initial')
            ->required()
            ->autocomplete()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        $login = $data['login'] ?? $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$login) {
            throw ValidationException::withMessages([
                'data.login' => 'The login field is required.',
            ]);
        }

        if (!$password) {
            throw ValidationException::withMessages([
                'data.password' => 'The password field is required.',
            ]);
        }

        $loginType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Check if it's an initial (single character or short identifier)
        if (strlen($login) <= 4 && !filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $loginType = 'initial';
        }

        return [
            $loginType => $login,
            'password' => $password,
        ];
    }

    protected function throwFailureValidationException(): never
    {
        throw ValidationException::withMessages([
            'data.login' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }

    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (Exception $e) {
            // Check if user exists but is inactive
            $data = $this->form->getState();
            $credentials = $this->getCredentialsFromFormData($data);

            // Remove password from credentials to search for user
            $searchCredentials = $credentials;
            unset($searchCredentials['password']);

            // Make sure we have valid search credentials
            if (!empty($searchCredentials)) {
                $user = User::where($searchCredentials)->first();

                if ($user && !$user->isActive()) {
                    throw ValidationException::withMessages([
                        'data.login' => __('Your account is deactivated. Please contact administrator.'),
                    ]);
                }
            }

            // Re-throw the original exception
            throw $e;
        }
    }
}
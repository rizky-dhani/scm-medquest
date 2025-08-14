<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Illuminate\Validation\Rules\Password;

class FirstTimePasswordChange extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static string $view = 'filament.pages.first-time-password-change';
    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => Auth::user()->name,
            'email' => Auth::user()->email,
        ]);
    }

    public function getTitle(): string
    {
        return 'Change Password Required';
    }

    public function getSubheading(): ?string
    {
        return 'Welcome! You must change your password before continuing to use the system.';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Welcome!')
                    ->description('For security reasons, you must change your password before accessing the system.')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Name')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->disabled()
                            ->dehydrated(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Set New Password')
                    ->description('Please choose a strong password for your account.')
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->required()
                            ->rule(Password::default())
                            ->confirmed()
                            ->revealable()
                            ->helperText('Your password must be at least 8 characters long.')
                            ->validationAttribute('password'),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->required()
                            ->dehydrated(false),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('changePassword')
                ->label('Change Password & Continue')
                ->submit('changePassword')
                ->color('primary'),
        ];
    }

    public function changePassword()
    {
        try {
            $data = $this->form->getState();
            
            $user = Auth::user();
            
            // Update password and mark as changed in a single transaction
            $user->update([
                'password' => Hash::make($data['password']),
                'password_change_required' => false,
                'password_changed_at' => now(),
            ]);
            
            // Clear the session flag
            session()->forget('password_change_required');

            Notification::make()
                ->success()
                ->title('Password Changed Successfully')
                ->body('Your password has been updated. Redirecting to dashboard...')
                ->send();

            // Use JavaScript redirect to the correct application path
            $this->js('setTimeout(() => { window.location.href = "' . url('/') . '"; }, 1500);');
            
        } catch (Halt $exception) {
            return;
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error Changing Password')
                ->body('An error occurred while changing your password. Please try again.')
                ->send();
                
            // Log the error for debugging
            \Log::error('Password change error: ' . $e->getMessage());
        }
    }

    public static function getSlug(): string
    {
        return 'first-time-password-change';
    }

    public static function getRouteName(?string $panel = null): string
    {
        return 'filament.dashboard.pages.first-time-password-change';
    }
}

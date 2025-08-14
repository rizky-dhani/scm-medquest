<x-filament-panels::page>
    @if (session('password_change_required'))
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-700">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-s-information-circle class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        First Time Setup
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>{{ session('password_change_required') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg dark:bg-blue-900/20 dark:border-blue-700">
            <div class="flex">
                <div class="flex-shrink-0">
                    <x-heroicon-s-information-circle class="h-5 w-5 text-blue-400" />
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        First Time Setup
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>This is your first time logging in. Please change your password to continue using the system securely.</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <x-filament-panels::form wire:submit="changePassword">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page>

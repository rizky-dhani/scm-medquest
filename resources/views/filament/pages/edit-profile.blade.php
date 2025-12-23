<x-filament-panels::page>
    <form wire:submit="save" id="form" class="grid gap-y-6">
        {{ $this->form }}

        <x-filament::actions
            :actions="$this->getFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </form>
</x-filament-panels::page>

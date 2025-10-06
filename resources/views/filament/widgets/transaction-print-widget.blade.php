<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        <form wire:submit.prevent='printTransactions'>
            {{ $this->form }}
        </form>
    </x-filament::section>
</x-filament-widgets::widget>

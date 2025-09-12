<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Widget content --}}
        {{-- <div class="text-center space-y-1" x-data="{ time: '{{ now()->setTimezone('Asia/Makassar')->format('H:i:s') }}' }" x-init="setInterval(() => time = new Date().toLocaleTimeString('id-ID', { hour12: false, timeZone: 'Asia/Makassar' }), 1000)">
            <div class="text-2xl font-bold" x-text="time"></div>
            <div class="text-sm text-gray-600">
                {{ $currentDate }}
            </div>
        </div> --}}
        <div class="text-center space-y-1" x-data="{
            time: new Date(),
            formatTime(date) {
                const h = String(date.getHours()).padStart(2, '0');
                const m = String(date.getMinutes()).padStart(2, '0');
                const s = String(date.getSeconds()).padStart(2, '0');
                return `${h}:${m}:${s}`;
            }
        }" x-init="setInterval(() => time = new Date(), 1000)">
            <div class="text-2xl font-bold" x-text="formatTime(time)"></div>
            <div class="text-sm text-gray-600">
                {{ $currentDate }}
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

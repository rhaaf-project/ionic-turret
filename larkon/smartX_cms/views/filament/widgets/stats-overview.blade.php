<x-filament-widgets::widget>
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px;">
        @foreach($this->getStats() as $stat)
        <div style="background: #1f2937; border: 1px solid #374151; border-radius: 8px; overflow: hidden;">
            <div style="text-align: center; font-size: 12px; font-weight: 600; color: white; background: #374151; padding: 8px;">
                {{ $stat['label'] }}
            </div>
            <div style="display: flex; justify-content: center; align-items: center; gap: 24px; padding: 12px;">
                <span style="font-size: 20px; font-weight: bold; color: #22c55e;">{{ $stat['active'] }}</span>
                <span style="font-size: 20px; font-weight: bold; color: #ef4444;">{{ $stat['inactive'] }}</span>
            </div>
        </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
<x-filament-widgets::widget>
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 12px;">
        @foreach($this->getPrivateWires() as $pw)
            <div style="background: #1f2937; border: 1px solid {{ $pw['is_active'] ? '#22c55e' : '#ef4444' }}; border-radius: 8px; overflow: hidden; cursor: pointer;"
                onclick="window.location.href='/admin/private-wires/{{ $pw['id'] }}/edit'">
                <div
                    style="text-align: center; font-size: 12px; font-weight: 600; color: white; background: #374151; padding: 8px;">
                    {{ $pw['label'] }}
                </div>
                <div style="display: flex; justify-content: center; align-items: center; padding: 16px;">
                    @if($pw['is_active'])
                        <span style="font-size: 14px; font-weight: bold; color: #22c55e;">● In Use</span>
                    @else
                        <span style="font-size: 14px; font-weight: bold; color: #ef4444;">● Not In Use</span>
                    @endif
                </div>
                <div style="text-align: center; font-size: 11px; color: #9ca3af; padding-bottom: 8px;">
                    {{ $pw['number'] }}
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>
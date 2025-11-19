@php
    // Partial: avatar-proyecto
    // Expects: $proyecto (App\Models\Proyecto)
    $met = strtolower($proyecto->metodologia->nombre ?? '');
    $innerBgClass = $met === 'scrum' ? 'bg-indigo-50 text-indigo-800' : ($met === 'cascada' ? 'bg-green-50 text-green-800' : 'bg-gray-50 text-gray-900');
    $ringClass = $met === 'scrum' ? 'ring-2 ring-indigo-100' : ($met === 'cascada' ? 'ring-2 ring-green-100' : 'ring-2 ring-gray-100');
@endphp

<div class="avatar">
    <div class="relative">
        <!-- Thin colored ring + soft-tinted inner circle with initials -->
        <div class="{{ $ringClass }} rounded-full p-0.5 inline-block">
            <div class="w-16 h-16 rounded-full {{ $innerBgClass }} border border-gray-200 flex items-center justify-center text-xl font-semibold shadow-sm">
                <span class="select-none" aria-hidden="true">{{ strtoupper(substr($proyecto->codigo ?? $proyecto->nombre, 0, 2)) }}</span>
            </div>
        </div>

        <!-- Small SVG badge for consistent iconography -->
        <div class="absolute -bottom-0.5 -right-0.5 w-6 h-6 bg-white rounded-full flex items-center justify-center border border-gray-200 shadow-sm" aria-hidden="true">
            @if($met === 'scrum')
                <!-- target/goal icon (thin strokes) -->
                <svg class="w-4 h-4 text-indigo-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="9"></circle>
                    <circle cx="12" cy="12" r="4"></circle>
                    <path d="M12 8v4l2 2"></path>
                </svg>
            @elseif($met === 'cascada' || $met === 'waterfall')
                <!-- flow/river icon -->
                <svg class="w-4 h-4 text-green-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 7c4 1 6 1 10 0s6-1 8 0" />
                    <path d="M3 12c4 1 6 1 10 0s6-1 8 0" />
                </svg>
            @else
                <!-- simple dot icon for neutral -->
                <svg class="w-4 h-4 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="3"></circle>
                </svg>
            @endif
        </div>
    </div>
</div>

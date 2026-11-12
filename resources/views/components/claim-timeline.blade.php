@props(['activities', 'iconClass' => 'text-blue-500'])

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
            <i class="fas fa-history {{ $iconClass }}"></i> Activity Timeline
        </h3>
    </div>

    <div class="p-5 max-h-96 overflow-y-auto">
        @forelse ($activities as $activity)
            @php $meta = \App\Enums\ClaimActivityAction::meta($activity->action); @endphp

            <div class="flex gap-3 pb-5 last:pb-0 relative">
                @if (!$loop->last)
                    <div class="absolute left-4 top-8 bottom-0 w-px bg-gray-100"></div>
                @endif

                <div class="shrink-0 w-8 h-8 rounded-full {{ $meta['color'] }} flex items-center justify-center z-10">
                    <i class="fas {{ $meta['icon'] }} text-xs"></i>
                </div>

                <div class="flex-1 min-w-0 pt-0.5">
                    <div class="flex items-center justify-between gap-2">
                        <p class="text-sm font-medium text-gray-800">
                            {{ \App\Enums\ClaimActivityAction::label($activity->action) }}
                        </p>
                        <span class="text-xs text-gray-400 whitespace-nowrap">
                            {{ $activity->created_at->diffForHumans() }}
                        </span>
                    </div>
                    @if ($activity->note)
                        <p class="text-sm text-gray-600 mt-0.5">{{ $activity->note }}</p>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">by {{ $activity->actor_name }}</p>
                </div>
            </div>
        @empty
            <p class="text-sm text-gray-400 italic">No activity recorded yet.</p>
        @endforelse
    </div>
</div>

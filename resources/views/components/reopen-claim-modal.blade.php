@props(['claim'])

<div x-data="{
    open: {{ $errors->has('note') ? 'true' : 'false' }},
    submitting: false,
    note: @js(old('note', ''))
}" x-init="$watch('open', v => v && $nextTick(() => $refs.note.focus()))" @keydown.escape.window="open = false">

    {{-- Trigger --}}
    <button type="button" @click="open = true"
        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 inline-flex items-center gap-2">
        <i class="fas fa-undo text-xs"></i> Reopen Claim
    </button>

    {{-- Modal --}}
    <template x-teleport="body">
        <div x-show="open" x-cloak x-transition.opacity
            class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 p-4" @click.self="open = false">

            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md">
                <form method="POST" action="{{ route('staff.claims.reopen', $claim) }}" @submit="submitting = true">
                    @csrf

                    <div class="px-6 pt-6 pb-4">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center">
                                <i class="fas fa-undo text-indigo-600"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-900">Reopen this claim?</h3>
                        </div>

                        <p class="text-sm text-gray-500 mb-4">
                            This claim is currently cancelled. Reopening it will bring it back into the active workflow.
                        </p>

                        <label for="reopen-note" class="block text-sm font-medium text-gray-700 mb-1">
                            Reason for reopening <span class="text-red-500">*</span>
                        </label>
                        <textarea id="reopen-note" name="note" x-ref="note" x-model="note" rows="3" maxlength="500" required
                            placeholder="Explain why this claim is being reopened..."
                            class="w-full text-sm border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-indigo-300"></textarea>

                        <div class="flex justify-between mt-1">
                            @error('note')
                                <p class="text-xs text-red-600">{{ $message }}</p>
                            @else
                                <span></span>
                            @enderror
                            <span class="text-xs text-gray-400" x-text="note.length + '/500'"></span>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 rounded-b-2xl flex justify-end gap-2">
                        <button type="button" @click="open = false"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" :disabled="submitting"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-50">
                            <span x-show="!submitting">Reopen Claim</span>
                            <span x-show="submitting" x-cloak>Reopening...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>

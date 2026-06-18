<x-layouts.staff>
    <div class="mb-6">
        <a href="{{ route('committee.claims.index') }}"
            class="text-sm text-gray-500 hover:text-gray-700 inline-flex items-center gap-2 mb-3">
            <i class="fas fa-arrow-left"></i> Back to Committee Queue
        </a>
        <h2 class="text-2xl font-bold text-gray-800">Claim {{ $claim->claim_number }}</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $claim->customer->name }} · {{ $claim->policy->policy_number }}</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Claim Details</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400 block text-xs">Product</span>
                        <span class="text-gray-800 font-medium">{{ $claim->policy->product_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-xs">Claim Amount</span>
                        <span class="text-gray-800 font-medium">GH₵ {{ number_format($claim->amount) }}</span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-xs">Policy Period</span>
                        <span class="text-gray-800 font-medium">
                            {{ \Carbon\Carbon::parse($claim->policy->start_date)->format('M d, Y') }} –
                            {{ \Carbon\Carbon::parse($claim->policy->end_date)->format('M d, Y') }}
                        </span>
                    </div>
                    <div>
                        <span class="text-gray-400 block text-xs">Escalated</span>
                        <span
                            class="text-gray-800 font-medium">{{ $claim->committee_review_at?->format('M d, Y') ?? '—' }}</span>
                    </div>
                </div>

                @if ($claim->documents->isNotEmpty())
                    <div class="pt-2">
                        <span class="text-gray-400 block text-xs mb-2">Supporting Documents</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($claim->documents as $doc)
                                <a href="{{ route('staff.documents.preview', $doc->id) }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-xs bg-gray-50 border border-gray-200 rounded-lg text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-alt text-gray-400"></i> {{ $doc->original_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            @if ($claim->survey_notes)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-3">Survey Findings</h3>
                    <p class="text-xs text-gray-400 mb-2">
                        Submitted by {{ $claim->surveyor->name ?? 'Unknown' }}
                        on {{ $claim->survey_completed_at?->format('M d, Y') ?? '—' }}
                    </p>
                    <p class="text-sm text-gray-700 whitespace-pre-line">{{ $claim->survey_notes }}</p>
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide mb-4">Committee Decision</h3>
            <form action="{{ route('committee.claims.decide', $claim->id) }}" method="POST">
                @csrf
                <textarea name="notes" rows="6" maxlength="2000" placeholder="Reasoning for the decision (optional)..."
                    class="w-full border border-gray-300 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-orange-300"></textarea>
                @error('notes')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror

                <div class="flex gap-2 mt-4">
                    <button type="submit" name="decision" value="approved"
                        class="flex-1 bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-check text-xs"></i> Approve
                    </button>
                    <button type="submit" name="decision" value="rejected"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <i class="fas fa-times text-xs"></i> Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.staff>

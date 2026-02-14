<div class="lg:col-span-1">
    <div class="bg-white rounded-lg shadow p-6 lg:sticky lg:top-22">
        <h2 class="text-lg font-semibold text-gray-800 mb-6 border-b border-b-gray-300 pb-3">
            Claimant Information
        </h2>

        <div class="space-y-5">
            <div class="flex items-start">
                <div class="shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                        Name
                    </p>
                    <p class="text-sm font-medium text-gray-900">{{ $customer->name }}</p>
                </div>
            </div>

            {{-- <div class="flex items-start">
                <div class="shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                        Address
                    </p>
                    <p class="text-sm text-gray-900">
                        Cantonments, Accra, Ghana.
                    </p>
                </div>
            </div> --}}

            {{-- <div class="flex items-start">
                <div class="shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                        Occupation
                    </p>
                    <p class="text-sm font-medium text-gray-900">
                        Head Of IT
                    </p>
                </div>
            </div> --}}

            <div class="flex items-start">
                <div class="shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                        Telephone
                    </p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $customer->phone }}
                    </p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                        Policy Number
                    </p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ $policy->policy_number }}
                    </p>
                </div>
            </div>

            <div class="flex items-start">
                <div class="shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3 mt-0.5">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-xs text-gray-500 uppercase tracking-wide mb-1">
                        Renewal Date
                    </p>
                    <p class="text-sm font-medium text-gray-900">
                        {{ \Carbon\Carbon::parse($policy->renewal_date)->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

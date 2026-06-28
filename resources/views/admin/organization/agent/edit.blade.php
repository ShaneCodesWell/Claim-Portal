<x-layouts.staff>
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-users text-blue-500 text-2xl"></i>
                Intermediary Management
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                Edit the Intermediary details below. To manage all Intermediaries, go back to the
                <a href="{{ route('organization') }}?tab=tab-agents" class="text-blue-600 hover:underline">
                    Agents List
                </a>.
            </p>
        </div>
        {{-- Back Button --}}
        <div class="sm:ml-auto flex items-center gap-2">
            <a href="{{ route('organization') }}?tab=tab-agents"
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>
    </div>

    <!-- Two column layout: left form, right bulk upload -->
    <div class="grid grid-cols-1 lg:grid-cols-1 gap-6 mb-8">
        <!-- Single Staff Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-edit text-blue-500"></i> Edit an Intermediary
            </h3>
            <form action="{{ route('agents.update', $agent->id) }}" id="singleStaffForm" class="space-y-4"
                method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    {{-- Fullname --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" value="{{ $agent->name }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
                        <input type="email" name="email" value="{{ $agent->email }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Phone Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                        <input type="text" name="phone" value="{{ $agent->phone }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Gender --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Gender
                        </label>
                        <select name="gender" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender', $agent->gender) === 'male' ? 'selected' : '' }}>
                                Male
                            </option>
                            <option value="female" {{ old('gender', $agent->gender) === 'female' ? 'selected' : '' }}>
                                Female
                            </option>
                            <option value="other" {{ old('gender', $agent->gender) === 'other' ? 'selected' : '' }}>
                                Other
                            </option>
                        </select>
                    </div>
                    {{-- Date of Birth --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Date of Birth
                        </label>
                        <input type="date" name="date_of_birth"
                            value="{{ old('date_of_birth', optional($agent->date_of_birth)->format('Y-m-d')) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- Agent Code --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Agent Code
                        </label>
                        <input type="text" name="partner_code"
                            value="{{ old('partner_code', $agent->partner_code) }}" placeholder="Enter Agent Code"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    {{-- League --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            League
                        </label>
                        <select name="league" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select League</option>
                            <option value="champions"
                                {{ old('league', $agent->league) === 'champions' ? 'selected' : '' }}>
                                Champions League
                            </option>
                            <option value="regional"
                                {{ old('league', $agent->league) === 'regional' ? 'selected' : '' }}>
                                Regional
                            </option>
                            <option value="auto_house"
                                {{ old('league', $agent->league) === 'auto_house' ? 'selected' : '' }}>
                                Auto House
                            </option>
                        </select>
                    </div>
                    {{-- Branch --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Branch
                        </label>
                        <select name="branch_id"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ old('branch_id', $agent->branch_id) == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                            <p class="text-red-500 text-sm mt-1">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                    {{-- Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Category
                        </label>
                        <select name="user_category" id="categorySelect"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Category</option>
                            @foreach (['Service Providers', 'Reinsurance', 'Agent', 'Broker', 'Bancassurance', 'Protocol'] as $category)
                                <option value="{{ $category }}"
                                    {{ old('user_category', $agent->user_category) === $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    {{-- Sub Category --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Sub Category
                        </label>
                        <select name="sub_user_category" id="subCategorySelect"
                            data-selected="{{ old('sub_user_category', $agent->sub_user_category) }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                            <option value="">Select Sub Category</option>
                        </select>
                    </div>
                    {{-- Password --}}
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            New Password
                        </label>
                        <input type="password" name="password"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div> --}}
                    {{-- Confirm Password --}}
                    {{-- <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm New Password
                        </label>
                        <input type="password" name="password_confirmation"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    </div> --}}
                </div>
                <div class="flex justify-end pt-2 gap-3">
                    <a href="{{ route('organization') }}?tab=tab-agents"
                        class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 rounded-lg text-sm text-gray-700 bg-white hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Back
                    </a>
                    <button type="submit"
                        class="px-5 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-save mr-2"></i>Update Agent
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset('js/agent-category.js') }}"></script>
</x-layouts.staff>

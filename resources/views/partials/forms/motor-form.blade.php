@php
    $f = $formData ?? [];
    $isStaff = ($context ?? 'customer') === 'staff';
    $isEdit = !is_null($claim ?? null);

    // Array Data
    $yourVehicleInjured = json_decode($f['your_vehicle_injured'] ?? '[]', true);
    $otherVehicleInjured = json_decode($f['other_vehicle_injured'] ?? '[]', true);

    $yourVehicleInjured = is_array($yourVehicleInjured) ? $yourVehicleInjured : [];
    $otherVehicleInjured = is_array($otherVehicleInjured) ? $otherVehicleInjured : [];
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
    <div class="overflow-hidden border border-gray-200">

        {{-- Header --}}
        <div class="px-8 pt-6 pb-0 bg-white">
            <div class="grid grid-cols-[160px_1fr_auto] items-start gap-6">
                <div class="pt-1">
                    <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo"
                        class="w-36 h-12 object-contain" />
                </div>
                <div class="text-center pt-1">
                    <p class="text-[15px] font-bold text-gray-800 tracking-wide mb-2 border-b border-b-gray-300 pb-2">
                        Vanguard Assurance Company Ltd
                    </p>
                    <p class="text-[10px] text-gray-500 mt-0.5 tracking-widest uppercase">
                        We always stand by you
                    </p>
                </div>
                <div class="text-right text-[11px] text-gray-500 leading-relaxed pt-1">
                    <p>vacmmails@vanguardassurance.com</p>
                    <p>claimsdepartment@vanguardassurance.com</p>
                    <p>030 266 6485 / 6486 / 6487</p>
                    <p>P.O. Box 1868, Accra</p>
                </div>
            </div>
            <div class="border-t border-gray-200 mt-5"></div>
        </div>

        {{-- Title band --}}
        <div class="bg-[#0b529d] px-8 py-2.5 flex items-center justify-center gap-4">
            <div class="flex-1 border-t border-white/20"></div>
            <p class="text-[13px] font-medium tracking-widest uppercase text-white whitespace-nowrap">
                Motor Accident Report Form{{ $isEdit ? ' — Edit' : '' }}
            </p>
            <div class="flex-1 border-t border-white/20"></div>
        </div>

        {{-- Context banner --}}
        @if ($isEdit && $isStaff)
            <div class="bg-indigo-50 border-b border-indigo-200 px-8 py-2 text-center">
                <p class="text-[11.5px] text-indigo-700 font-medium">
                    <i class="fas fa-user-shield mr-1"></i>
                    Editing as staff — all changes will be logged with your name and timestamp.
                </p>
            </div>
        @elseif($isEdit)
            <div class="bg-amber-50 border-b border-amber-200 px-8 py-2 text-center">
                <p class="text-[11.5px] text-amber-700 font-medium">
                    <i class="fas fa-edit mr-1"></i>
                    Editing claim <strong>{{ $claim->claim_number }}</strong> — fields are pre-filled with your original
                    submission.
                </p>
            </div>
        @else
            <div class="bg-gray-50 border-b border-gray-200 px-8 py-2 text-center">
                <p class="text-[11.5px] text-gray-500">
                    Please complete all sections accurately. Fields marked * are required.
                </p>
            </div>
        @endif

    </div>

    <div class="py-6 px-12">

        {{-- Note box --}}
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-lg">
            <p class="text-xs text-gray-700 leading-relaxed">
                Please note, it is necessary that great care should be taken in completing this form and
                the information given therein should be strictly accurate, whether it is in your favor
                or otherwise. You should not make any payment, offer or promise of any payment or admit
                liability in any way, as by so doing you may prejudice your position and make settlement
                of the claim difficult.
            </p>
        </div>

        <form id="motorForm" data-action="{{ $action }}">
            @csrf
            @if ($method === 'PUT')
                @method('PUT')
            @endif
            <input type="hidden" name="claim_type" value="motor" />
            <input type="hidden" name="policy_id" value="{{ $policy->external_policy_id ?? $policy->id }}">

            {{-- ── SECTION 1: VEHICLE PARTICULARS ── --}}
            <section class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                    PARTICULARS OF MOTOR VEHICLE CONCERNED
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="registration_no" value="{{ $f['registration_no'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Make <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="make" value="{{ $f['make'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Model <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="model" value="{{ $f['model'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year of Make <span
                                class="text-red-500">*</span></label>
                        <input type="text" name="year_of_make" value="{{ $f['year_of_make'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Is the vehicle the subject of a hire
                        purchase or loan agreement? <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="hire_purchase" value="yes" class="conditional-radio mr-2"
                                data-target="financeCompanySection"
                                {{ ($f['hire_purchase'] ?? '') === 'yes' ? 'checked' : '' }}>
                            <span>Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="hire_purchase" value="no" class="conditional-radio mr-2"
                                data-target="financeCompanySection"
                                {{ ($f['hire_purchase'] ?? '') === 'no' ? 'checked' : '' }}>
                            <span>No</span>
                        </label>
                    </div>
                    <div id="financeCompanySection"
                        class="{{ ($f['hire_purchase'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Name of finance company or lending
                            organization <span class="text-red-500">*</span></label>
                        <input type="text" name="finance_company" value="{{ $f['finance_company'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">State fully the purpose for which the
                        vehicle was being used. <span class="text-red-500">*</span></label>
                    <textarea name="vehicle_purpose" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition"
                        placeholder="It is not sufficient to state 'Business' or 'Private'">{{ $f['vehicle_purpose'] ?? '' }}</textarea>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Was the vehicle being used with your
                        consent? <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" name="vehicleConsent" value="yes" class="mr-2"
                                {{ ($f['vehicle_consent'] ?? '') === 'yes' ? 'checked' : '' }}> Yes
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="vehicleConsent" value="no" class="mr-2"
                                {{ ($f['vehicle_consent'] ?? '') === 'no' ? 'checked' : '' }}> No
                        </label>
                    </div>
                </div>
            </section>

            {{-- ── SECTION 2: DRIVER PARTICULARS ── --}}
            <section class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                    PARTICULARS OF PERSON DRIVING AT THE TIME OF ACCIDENT
                </h3>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Who was driving at the time of
                        accident? <span class="text-red-500">*</span></label>
                    <select id="driverSelect"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none bg-white">
                        <option value="">Select driver</option>
                        @foreach (['self' => 'Myself', 'spouse' => 'Spouse', 'family_member' => 'Family Member', 'employee' => 'Employee', 'friend' => 'Friend', 'other' => 'Other Person'] as $val => $label)
                            <option value="{{ $val }}"
                                {{ ($f['driver_type'] ?? '') === $val ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Driver self --}}
                <div id="driverDetailsSelf" class="{{ ($f['driver_type'] ?? '') === 'self' ? '' : 'hidden' }} mb-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-3">DRIVER DETAILS (from profile)</h4>
                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div><label class="block text-xs font-medium text-gray-600">Full Name</label>
                                <input type="text" readonly value="{{ $customer->name }}"
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded text-gray-700">
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600">Address</label>
                                <input type="text"  value="{{ $customer->email ?? ' ' }}"
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded text-gray-700">
                            </div>
                            <div><label class="block text-xs font-medium text-gray-600">Telephone</label>
                                <input type="text" readonly value="{{ $customer->phone }}"
                                    class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded text-gray-700">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Driver other --}}
                <div id="driverDetailsOther"
                    class="{{ !in_array($f['driver_type'] ?? '', ['', 'self']) ? '' : 'hidden' }} mb-6">
                    <h4 class="text-md font-semibold text-gray-800 mb-3">DRIVER DETAILS</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="driver_fullname" value="{{ $f['driver_fullname'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="driver_address" value="{{ $f['driver_address'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Age <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="driver_age" value="{{ $f['driver_age'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Occupation <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="driver_occupation"
                                value="{{ $f['driver_occupation'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Telephone <span
                                    class="text-red-500">*</span></label>
                            <input type="tel" name="driver_phone" value="{{ $f['driver_phone'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Driving License No. <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="driver_license" value="{{ $f['driver_license'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of Issue <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="driver_license_date"
                                value="{{ $f['driver_license_date'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>
                </div>

                <div id="driverInsuranceSection"
                    class="{{ !empty($f['driver_insurance_details']) || !in_array($f['driver_type'] ?? '', ['', '']) ? '' : 'hidden' }} mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">State name and address of the insurer
                        of the person driving and number of the motor vehicle policy held by him/her <span
                            class="text-red-500">*</span></label>
                    <textarea name="driver_insurance_details" rows="3"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">{{ $f['driver_insurance_details'] ?? '' }}</textarea>
                </div>
            </section>

            {{-- ── SECTION 3: ACCIDENT CIRCUMSTANCES ── --}}
            <section class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                    CIRCUMSTANCES OF ACCIDENT
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span
                                class="text-red-500">*</span></label>
                        <input type="date" name="accident_date" value="{{ $f['accident_date'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Time <span
                                class="text-red-500">*</span></label>
                        <input type="time" name="accident_time" value="{{ $f['accident_time'] ?? '' }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Exact location of incident <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="exact_location" value="{{ $f['exact_location'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">How many people were in your vehicle at
                        the time? <span class="text-red-500">*</span></label>
                    <input type="number" name="people_in_vehicle" value="{{ $f['people_in_vehicle'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">If you were not in the vehicle, when
                        was the accident reported to you?</label>
                    <input type="text" name="report_date" value="{{ $f['report_date'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Give full description of how the
                        accident happened <span class="text-red-500">*</span></label>
                    <textarea name="accident_description" rows="4"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">{{ $f['accident_description'] ?? '' }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">In your opinion, was the accident
                        caused by you or the other driver? If not, by whom? <span class="text-red-500">*</span></label>
                    <input type="text" name="fault_person" value="{{ $f['fault_person'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Describe the damage to your vehicle
                        <span class="text-red-500">*</span></label>
                    <textarea name="vehicle_damage" rows="2"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">{{ $f['vehicle_damage'] ?? '' }}</textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">State exact current location of the
                        damaged vehicle <span class="text-red-500">*</span></label>
                    <input type="text" name="damaged_vehicle_location"
                        value="{{ $f['damaged_vehicle_location'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Workshop Information</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="repairer_fullname"
                                value="{{ $f['repairer_fullname'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Contact <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="repairer_address" value="{{ $f['repairer_address'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>
                </div>
            </section>

            {{-- ── SECTION 4: THIRD PARTIES ── --}}
            <section class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                    THIRD PARTIES INVOLVED IN ACCIDENT
                </h3>

                {{-- Injured in your vehicle --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-semibold text-gray-700">Injured Persons in Your Vehicle</h4>
                        <button type="button" onclick="addInjuredPerson('yourVehicle')"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-1"></i> Add
                        </button>
                    </div>
                    <div id="yourVehicleInjuredPersons" class="space-y-4">
                        @php
                            $injuredPersons = json_decode($f['your_vehicle_injured'] ?? '[]', true) ?? [];
                        @endphp

                        @forelse($injuredPersons as $i => $person)
                            <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" name="your_vehicle_injured[{{ $i }}][name]"
                                            value="{{ $person['name'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                        <input type="number" name="your_vehicle_injured[{{ $i }}][age]"
                                            value="{{ $person['age'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text"
                                            name="your_vehicle_injured[{{ $i }}][address]"
                                            value="{{ $person['address'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                        Injuries</label>
                                    <textarea name="your_vehicle_injured[{{ $i }}][injuries]" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $person['injuries'] ?? '' }}</textarea>
                                </div>
                                <div class="mt-2 flex justify-end">
                                    <button type="button" onclick="removeInjuredPerson(this)"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" name="your_vehicle_injured[0][name]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                        <input type="number" name="your_vehicle_injured[0][age]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text" name="your_vehicle_injured[0][address]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                        Injuries</label>
                                    <textarea name="your_vehicle_injured[0][injuries]" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Injured in other vehicle --}}
                {{-- <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <h4 class="text-md font-semibold text-gray-700">Injured Persons in Other Vehicle</h4>
                        <button type="button" onclick="addInjuredPerson('otherVehicle')"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-1"></i> Add
                        </button>
                    </div>
                    <div id="otherVehicleInjuredPersons" class="space-y-4">
                        @php
                            $otherInjuredPersons = json_decode($f['other_vehicle_injured'] ?? '[]', true);
                            if (!is_array($otherInjuredPersons)) {
                                $otherInjuredPersons = [];
                            }
                        @endphp
                        @forelse($otherInjuredPersons as $i => $person)
                            <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" name="other_vehicle_injured[{{ $i }}][name]"
                                            value="{{ $person['name'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                        <input type="number" name="other_vehicle_injured[{{ $i }}][age]"
                                            value="{{ $person['age'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text"
                                            name="other_vehicle_injured[{{ $i }}][address]"
                                            value="{{ $person['address'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                        Injuries</label>
                                    <textarea name="other_vehicle_injured[{{ $i }}][injuries]" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $person['injuries'] ?? '' }}</textarea>
                                </div>
                                <div class="mt-2 flex justify-end">
                                    <button type="button" onclick="removeInjuredPerson(this)"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                        <input type="text" name="other_vehicle_injured[0][name]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                        <input type="number" name="other_vehicle_injured[0][age]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                        <input type="text" name="other_vehicle_injured[0][address]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                        Injuries</label>
                                    <textarea name="other_vehicle_injured[0][injuries]" rows="2"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div> --}}

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <label class="block text-sm font-medium text-gray-700">Details of vehicles involved</label>
                        <button type="button" onclick="addVehicle()"
                            class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-plus mr-1"></i> Add Vehicle
                        </button>
                    </div>
                    <div id="vehiclesContainer" class="space-y-4">
                        @php
                            $vehicles = json_decode($f['involved_vehicles'] ?? '[]', true) ?? [];
                        @endphp

                        @forelse($vehicles as $i => $vehicle)
                            <div class="vehicle-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div><label class="block text-xs font-medium text-gray-600">Registration
                                            Number</label>
                                        <input type="text" name="involved_vehicles[{{ $i }}][reg]"
                                            value="{{ $vehicle['reg'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-xs font-medium text-gray-600">Make</label>
                                        <input type="text" name="involved_vehicles[{{ $i }}][make]"
                                            value="{{ $vehicle['make'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-xs font-medium text-gray-600">Model</label>
                                        <input type="text" name="involved_vehicles[{{ $i }}][model]"
                                            value="{{ $vehicle['model'] ?? '' }}"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                                <div class="mt-2 flex justify-end">
                                    <button type="button" onclick="removeVehicle(this)"
                                        class="text-red-600 hover:text-red-800 text-sm">
                                        <i class="fas fa-trash mr-1"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="vehicle-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div><label class="block text-xs font-medium text-gray-600">Registration
                                            Number</label>
                                        <input type="text" name="involved_vehicles[0][reg]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-xs font-medium text-gray-600">Make</label>
                                        <input type="text" name="involved_vehicles[0][make]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                    <div><label class="block text-xs font-medium text-gray-600">Model</label>
                                        <input type="text" name="involved_vehicles[0][model]"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Owner of Accident Vehicle
                        Information</label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name </label>
                            <input type="text" name="owner_fullname" value="{{ $f['owner_fullname'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Address </label>
                            <input type="text" name="owner_address" value="{{ $f['owner_address'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Telephone </label>
                            <input type="tel" name="owner_telephone" value="{{ $f['owner_telephone'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Policy No. </label>
                            <input type="text" name="owner_policy" value="{{ $f['owner_policy'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Details of damage to this vehicle <span
                            class="text-red-500">*</span></label>
                    <textarea name="other_vehicle_damage" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $f['other_vehicle_damage'] ?? '' }}</textarea>
                </div>

                {{-- Claim made --}}
                {{-- <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Has any claim been made upon you? <span
                            class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center"><input type="radio" name="claim_made" value="yes"
                                class="conditional-radio mr-2" data-target="claimMadeSection"
                                {{ ($f['claim_made'] ?? '') === 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="flex items-center"><input type="radio" name="claim_made" value="no"
                                class="conditional-radio mr-2" data-target="claimMadeSection"
                                {{ ($f['claim_made'] ?? '') === 'no' ? 'checked' : '' }}> No</label>
                    </div>
                    <div id="claimMadeSection"
                        class="{{ ($f['claim_made'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">State particulars below <span
                                class="text-red-500">*</span></label>
                        <textarea name="claim_made_details" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $f['claim_made_details'] ?? '' }}</textarea>
                    </div>
                </div> --}}

                {{-- Police report --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Was the accident reported to the
                        police? <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center"><input type="radio" name="police_report" value="yes"
                                class="conditional-radio mr-2" data-target="policeReportSection"
                                {{ ($f['police_report'] ?? '') === 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="flex items-center"><input type="radio" name="police_report" value="no"
                                class="conditional-radio mr-2" data-target="policeReportSection"
                                {{ ($f['police_report'] ?? '') === 'no' ? 'checked' : '' }}> No</label>
                    </div>
                    <div id="policeReportSection"
                        class="{{ ($f['police_report'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">State when and where reported <span
                                class="text-red-500">*</span></label>
                        <textarea name="police_report_details" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $f['police_report_details'] ?? '' }}</textarea>
                    </div>
                </div>

                {{-- <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name Police Officer who took
                        particulars <span class="text-red-500">*</span></label>
                    <input type="text" name="officer_details" value="{{ $f['officer_details'] ?? '' }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div> --}}

                {{-- Indemnifying policy --}}
                {{-- <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Do you hold more than one policy
                        indemnifying you in respect of this accident? <span class="text-red-500">*</span></label>
                    <div class="flex gap-4">
                        <label class="flex items-center"><input type="radio" name="indem_policy" value="yes"
                                class="conditional-radio mr-2" data-target="indemPolicySection"
                                {{ ($f['indem_policy'] ?? '') === 'yes' ? 'checked' : '' }}> Yes</label>
                        <label class="flex items-center"><input type="radio" name="indem_policy" value="no"
                                class="conditional-radio mr-2" data-target="indemPolicySection"
                                {{ ($f['indem_policy'] ?? '') === 'no' ? 'checked' : '' }}> No</label>
                    </div>
                    <div id="indemPolicySection"
                        class="{{ ($f['indem_policy'] ?? '') === 'yes' ? '' : 'hidden' }} mt-3 pl-4 border-l-2 border-blue-200">
                        <label class="block text-sm font-medium text-gray-700 mb-1">State the details of the other
                            policy.</label>
                        <textarea name="indem_policy_details" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg">{{ $f['indem_policy_details'] ?? '' }}</textarea>
                    </div>
                </div> --}}
            </section>

            {{-- ── SECTION 5: DOCUMENTS ── --}}
            <section class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">
                    Supporting Documents
                </h3>

                {{-- Existing documents (edit mode only) --}}
                @if ($isEdit && $claim->documents->isNotEmpty())
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Previously uploaded:</p>
                        <div class="space-y-2">
                            @foreach ($claim->documents as $doc)
                                <div
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <div class="flex items-center gap-2">
                                        <i
                                            class="fas {{ str_contains($doc->mime_type, 'pdf') ? 'fa-file-pdf text-red-400' : 'fa-image text-blue-400' }} text-sm"></i>
                                        <span class="text-sm text-gray-700">{{ $doc->original_name }}</span>
                                        <span
                                            class="text-xs text-gray-400">{{ number_format($doc->file_size / 1024, 1) }}
                                            KB</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <button type="button"
                                            onclick="openDocPreview('{{ route('staff.documents.preview', $doc->id) }}', '{{ $doc->original_name }}', '{{ $doc->mime_type }}')"
                                            class="text-xs text-blue-600 hover:underline">View</button>
                                        <button type="button"
                                            onclick="markDocumentForDeletion({{ $doc->id }}, this)"
                                            class="text-xs text-red-500 hover:underline">Remove</button>
                                        <input type="hidden" name="delete_documents[]" value=""
                                            id="delete-doc-{{ $doc->id }}" disabled>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- New file upload --}}
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                    id="dropzone">
                    <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                    <p class="text-gray-600">Drag & drop files here or <span
                            class="text-blue-600 font-medium">browse</span></p>
                    <p class="text-xs text-gray-400 mt-1">Supports: JPG, PNG, PDF (max 5MB each)</p>
                    <input type="file" id="imageUpload" accept="image/jpeg,image/png,image/gif,application/pdf"
                        multiple class="hidden">
                </div>
                <div id="imagePreviewContainer" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
            </section>

            {{-- ── SECTION 6: STAFF NOTE (staff edit only) ── --}}
            @if ($isStaff)
                <section class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                        <i class="fas fa-sticky-note text-indigo-500"></i> Edit Note
                    </h3>
                    <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Reason for edit <span class="text-xs text-gray-400">(logged in activity timeline)</span>
                        </label>
                        <input type="text" name="note"
                            placeholder="e.g. Customer called to correct registration number"
                            class="w-full px-3 py-2 border border-indigo-200 rounded-lg focus:ring-2 focus:ring-indigo-400 outline-none bg-white">
                    </div>
                </section>
            @endif

            {{-- ── SECTION 6/7: DECLARATION (customer only) ── --}}
            @if (!$isStaff)
                <section class="mb-8">
                    <h3
                        class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                        <i class="fas fa-file-signature text-blue-500"></i> DECLARATION
                    </h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <p class="text-xs text-gray-700 leading-relaxed mb-4">
                            I declare that the above statement is true in all respects to the best of my knowledge and
                            belief
                            and I hereby leave in the hands of the Company in accordance with the conditions of the
                            Policy
                            the conduct of all claims and litigation arising out of this accident and to which the
                            Policy applies,
                            to deal with, to prosecute and/or settle as they deem fit without further reference to me;
                            and I
                            undertake to give all such information and assistance as the Company may require.
                        </p>
                        <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                            <p class="text-sm text-gray-700"><span class="font-semibold">Note:</span> The Company does
                                not admit liability by the issue of this form.</p>
                        </div>
                    </div>
                    <div class="bg-white border-2 border-blue-200 rounded-lg p-4 mb-6">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" name="declaration_agreement" required
                                class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"
                                {{ !empty($f['declaration_agreement']) ? 'checked' : '' }}>
                            <span class="ml-3 text-xs text-gray-700">I have read and understood the declaration above.
                                I confirm that all information provided in this form is true and accurate to the best of
                                my knowledge.
                                <span class="text-red-500">*</span></span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Declaration <span
                                    class="text-red-500">*</span></label>
                            <input type="date" name="declaration_date" value="{{ $f['declaration_date'] ?? '' }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Digital Signature
                                <span class="text-xs text-gray-500">(Type your full name)</span>
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="digital_signature"
                                value="{{ $f['digital_signature'] ?? '' }}"
                                placeholder="Type your full name as your digital signature"
                                class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-lg"
                                style="font-family: 'Brush Script MT', cursive;">
                        </div>
                    </div>
                </section>
            @endif

            {{-- ── ACTION BUTTONS ── --}}
            <div class="mt-8 pt-4 border-t border-gray-200 flex items-center gap-3">
                <button type="submit"
                    class="px-6 py-2 {{ $isStaff ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white font-medium rounded-lg transition flex items-center gap-2">
                    @if (!$isEdit)
                        <span>Submit Claim</span><i class="fas fa-paper-plane"></i>
                    @else
                        <span>Save Changes</span><i class="fas fa-save"></i>
                    @endif
                </button>
                @if ($isEdit)
                    <a href="{{ $isStaff ? route('staff.claims.show', $claim) : route('claims.show', $claim) }}"
                        class="px-6 py-2 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition">
                        Cancel
                    </a>
                @endif
            </div>

            @if ($isEdit && $isStaff)
                <script>
                    document.addEventListener('DOMContentLoaded', () => {
                        @if (!$isAssignedToMe && !$isAssignedToOther)
                            {{-- CASE 1: Unassigned — prompt to self-assign --}}
                            Swal.fire({
                                title: 'Claim is unassigned',
                                html: `
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        Nobody is currently assigned to claim
                                        <strong>{{ $claim->claim_number }}</strong>.<br><br>
                                        Would you like to assign it to yourself before editing?
                                    </p>`,
                                icon: 'question',
                                showCancelButton: true,
                                confirmButtonText: 'Yes, assign to me',
                                cancelButtonText: 'No, just edit',
                                confirmButtonColor: '#4f46e5',
                                cancelButtonColor: '#6b7280',
                                reverseButtons: true,
                            }).then(result => {
                                if (result.isConfirmed) {
                                    fetch('{{ route('staff.claims.assign', $claim) }}', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/json',
                                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                                'Accept': 'application/json',
                                            },
                                            body: JSON.stringify({
                                                assigned_to: {{ Auth::id() }},
                                                note: 'Self-assigned before editing form.',
                                            }),
                                        })
                                        .then(res => res.json())
                                        .then(() => {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'success',
                                                title: 'Assigned to you',
                                                showConfirmButton: false,
                                                timer: 2500,
                                                timerProgressBar: true,
                                            });
                                        })
                                        .catch(() => {
                                            Swal.fire({
                                                toast: true,
                                                position: 'top-end',
                                                icon: 'warning',
                                                title: 'Could not assign — edits will still be logged',
                                                showConfirmButton: false,
                                                timer: 3000,
                                            });
                                        });
                                }
                            });
                        @elseif ($isAssignedToOther)
                            {{-- CASE 2: Assigned to someone else — warn and give option to go back --}}
                            Swal.fire({
                                title: 'Claim already assigned',
                                html: `
                                    <p class="text-sm text-gray-600 leading-relaxed">
                                        This claim is currently assigned to
                                        <strong>{{ $assignee->name }}</strong>.<br><br>
                                        You can still make edits — everything will be logged with your name
                                        and <strong>{{ $assignee->name }}</strong> will be able to see your changes
                                        in the activity log.
                                    </p>`,
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonText: 'Proceed with edits',
                                cancelButtonText: 'Go back',
                                confirmButtonColor: '#4f46e5',
                                cancelButtonColor: '#6b7280',
                                reverseButtons: true,
                            }).then(result => {
                                if (!result.isConfirmed) {
                                    window.location.href = '{{ route('staff.claims.show', $claim) }}';
                                }
                            });
                        @endif
                    });
                </script>
            @endif
        </form>
    </div>
</div>

{{-- Shared document preview modal --}}
<x-documents-modal />
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const isEdit = {{ $isEdit ? 'true' : 'false' }};
        const isStaff = {{ $isStaff ? 'true' : 'false' }};

        // ── Conditional radio toggles ──────────────────────────────────────────
        document.querySelectorAll('.conditional-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                const target = document.getElementById(this.getAttribute('data-target'));
                if (!target) return;
                const isYes = this.value === 'yes';
                target.classList.toggle('hidden', !isYes);
                target.querySelectorAll('input, textarea, select').forEach(input => {
                    input.required = isYes;
                    if (!isYes) input.value = '';
                });
            });
        });

        // ── Driver selection ───────────────────────────────────────────────────
        const driverSelect = document.getElementById('driverSelect');
        const selfDiv = document.getElementById('driverDetailsSelf');
        const otherDiv = document.getElementById('driverDetailsOther');
        const insuranceDiv = document.getElementById('driverInsuranceSection');
        const insuranceField = document.querySelector('[name="driver_insurance_details"]');

        function resetDriverSections() {
            [selfDiv, otherDiv, insuranceDiv].forEach(div => {
                if (!div) return;
                div.classList.add('hidden');
                div.querySelectorAll('input, textarea, select').forEach(f => {
                    f.required = false;
                });
            });
        }

        driverSelect?.addEventListener('change', function() {
            resetDriverSections();
            if (this.value === 'self') {
                selfDiv?.classList.remove('hidden');
                insuranceDiv?.classList.remove('hidden');
                if (insuranceField) insuranceField.required = true;
            } else if (this.value !== '') {
                otherDiv?.classList.remove('hidden');
                insuranceDiv?.classList.remove('hidden');
                otherDiv?.querySelectorAll('input, textarea').forEach(f => f.required = true);
                if (insuranceField) insuranceField.required = true;
            }
        });

        // ── Injured persons ────────────────────────────────────────────────────
        let yourVehicleCounter = {{ count($yourVehicleInjured) }};
        let otherVehicleCounter = {{ count($otherVehicleInjured) }};

        window.addInjuredPerson = function(type) {
            const isYours = type === 'yourVehicle';
            const container = document.getElementById(isYours ? 'yourVehicleInjuredPersons' :
                'otherVehicleInjuredPersons');
            const counter = isYours ? yourVehicleCounter++ : otherVehicleCounter++;
            const prefix = isYours ? 'your_vehicle_injured' : 'other_vehicle_injured';
            const row = document.createElement('div');
            row.className = 'injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50';
            row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="${prefix}[${counter}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                    <input type="number" name="${prefix}[${counter}][age]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <input type="text" name="${prefix}[${counter}][address]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
            </div>
            <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of Injuries</label>
                <textarea name="${prefix}[${counter}][injuries]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea></div>
            <div class="mt-2 flex justify-end">
                <button type="button" onclick="removeInjuredPerson(this)" class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash mr-1"></i> Remove
                </button>
            </div>`;
            container.appendChild(row);
        };

        window.removeInjuredPerson = function(btn) {
            const row = btn.closest('.injured-person-row');
            const container = row.parentElement;
            if (container.querySelectorAll('.injured-person-row').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input, textarea').forEach(f => f.value = '');
            }
        };

        function collectVehicles(containerId) {
            const container = document.getElementById(containerId);
            if (!container) return [];
            const rows = container.querySelectorAll('.vehicle-row');
            return Array.from(rows).map(row => {
                const reg = row.querySelector('[name$="[reg]"]')?.value || '';
                const make = row.querySelector('[name$="[make]"]')?.value || '';
                const model = row.querySelector('[name$="[model]"]')?.value || '';
                return {
                    reg,
                    make,
                    model
                };
            }).filter(v => v.reg || v.make || v.model); // keep only non-empty
        }

        // ── Vehicles involved ────────────────────────────────────────────────────
        let vehicleCounter = {{ count($vehicles) }};

        window.addVehicle = function() {
            const container = document.getElementById('vehiclesContainer');
            const counter = vehicleCounter++;
            const row = document.createElement('div');
            row.className = 'vehicle-row border border-gray-200 rounded-lg p-4 bg-gray-50';
            row.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div><label class="block text-xs font-medium text-gray-600">Registration Number</label>
                    <input type="text" name="involved_vehicles[${counter}][reg]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                <div><label class="block text-xs font-medium text-gray-600">Make</label>
                    <input type="text" name="involved_vehicles[${counter}][make]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                <div><label class="block text-xs font-medium text-gray-600">Model</label>
                    <input type="text" name="involved_vehicles[${counter}][model]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
            </div>
            <div class="mt-2 flex justify-end">
                <button type="button" onclick="removeVehicle(this)" class="text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash mr-1"></i> Remove
                </button>
            </div>`;
            container.appendChild(row);
        };

        window.removeVehicle = function(btn) {
            const row = btn.closest('.vehicle-row');
            const container = row.parentElement;
            if (container.querySelectorAll('.vehicle-row').length > 1) {
                row.remove();
            } else {
                row.querySelectorAll('input').forEach(f => f.value = '');
            }
        };

        // ── Document deletion marking ──────────────────────────────────────────
        window.markDocumentForDeletion = function(docId, btn) {
            const input = document.getElementById(`delete-doc-${docId}`);
            const card = btn.closest('.flex.items-center.justify-between');
            if (input) {
                input.value = docId;
                input.disabled = false;
            }
            card?.classList.add('opacity-40', 'line-through');
            btn.textContent = 'Undo';
            btn.onclick = () => undoDocumentDeletion(docId, btn, card);
        };

        window.undoDocumentDeletion = function(docId, btn, card) {
            const input = document.getElementById(`delete-doc-${docId}`);
            if (input) {
                input.value = '';
                input.disabled = true;
            }
            card?.classList.remove('opacity-40', 'line-through');
            btn.textContent = 'Remove';
            btn.onclick = () => markDocumentForDeletion(docId, btn);
        };

        // ── File upload ────────────────────────────────────────────────────────
        const dropzone = document.getElementById('dropzone');
        const fileInput = document.getElementById('imageUpload');
        const previewContainer = document.getElementById('imagePreviewContainer');
        let uploadedFiles = [];

        function renderPreviews() {
            previewContainer.innerHTML = '';
            uploadedFiles.forEach((file, index) => {
                const div = document.createElement('div');
                div.className =
                    'relative group border border-gray-200 rounded-lg overflow-hidden bg-gray-50';
                if (file.type === 'application/pdf') {
                    div.innerHTML =
                        `
                    <div class="w-full h-24 flex flex-col items-center justify-center gap-1 bg-red-50">
                        <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                        <span class="text-xs text-gray-500 truncate px-2 w-full text-center">${file.name}</span>
                    </div>
                    <button type="button" onclick="removeFile(${index})"
                        class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>`;
                } else {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        div.innerHTML =
                            `
                        <img src="${e.target.result}" class="w-full h-24 object-cover">
                        <button type="button" onclick="removeFile(${index})"
                            class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>`;
                    };
                    reader.readAsDataURL(file);
                }
                previewContainer.appendChild(div);
            });
        }

        window.removeFile = function(index) {
            uploadedFiles.splice(index, 1);
            renderPreviews();
        };

        dropzone?.addEventListener('click', () => fileInput.click());
        dropzone?.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropzone.classList.add('border-blue-500', 'bg-blue-50');
        });
        dropzone?.addEventListener('dragleave', () => {
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
        });
        dropzone?.addEventListener('drop', (e) => {
            e.preventDefault();
            dropzone.classList.remove('border-blue-500', 'bg-blue-50');
            uploadedFiles.push(...Array.from(e.dataTransfer.files));
            renderPreviews();
        });
        fileInput?.addEventListener('change', (e) => {
            uploadedFiles.push(...Array.from(e.target.files));
            renderPreviews();
        });

        // ── Form submission ────────────────────────────────────────────────────
        document.getElementById('motorForm')?.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Customer requires declaration checkbox
            if (!isStaff && !isChecked('declaration_agreement')) {
                showClaimError('Please read and accept the declaration before submitting.');
                return;
            }

            const formData = new FormData();
            if (isEdit) formData.append('_method', 'PUT');
            formData.append('claim_type', 'motor');
            formData.append('_token', document.querySelector('meta[name="csrf-token"]')
                .getAttribute('content'));

            // Add this — read from the hidden input
            formData.append('policy_id', document.querySelector('[name="policy_id"]')?.value ?? '');

            const claimFields = {
                registration_no: val('registration_no'),
                make: val('make'),
                model: val('model'),
                year_of_make: val('year_of_make'),
                hire_purchase: checked('hire_purchase'),
                finance_company: val('finance_company'),
                vehicle_purpose: val('vehicle_purpose'),
                vehicle_consent: checked('vehicleConsent'),
                driver_type: driverSelect?.value ?? '',
                fullname: val('fullname'),
                address: val('address'),
                age: val('age'),
                occupation: val('occupation'),
                phone: val('phone'),
                drivers_license: val('drivers_license'),
                license_issue_date: val('license_issue_date'),
                driver_fullname: val('driver_fullname'),
                driver_address: val('driver_address'),
                driver_age: val('driver_age'),
                driver_occupation: val('driver_occupation'),
                driver_phone: val('driver_phone'),
                driver_license: val('driver_license'),
                driver_license_date: val('driver_license_date'),
                driver_insurance_details: val('driver_insurance_details'),
                accident_date: val('accident_date'),
                accident_time: val('accident_time'),
                exact_location: val('exact_location'),
                people_in_vehicle: val('people_in_vehicle'),
                report_date: val('report_date'),
                accident_description: val('accident_description'),
                fault_person: val('fault_person'),
                vehicle_damage: val('vehicle_damage'),
                damaged_vehicle_location: val('damaged_vehicle_location'),
                repairer_fullname: val('repairer_fullname'),
                repairer_address: val('repairer_address'),
                your_vehicle_injured: collectInjuredPersons('yourVehicleInjuredPersons'),
                other_vehicle_injured: collectInjuredPersons('otherVehicleInjuredPersons'),
                involved_vehicles: collectVehicles('vehiclesContainer'),
                owner_fullname: val('owner_fullname'),
                owner_address: val('owner_address'),
                owner_telephone: val('owner_telephone'),
                owner_policy: val('owner_policy'),
                other_vehicle_damage: val('other_vehicle_damage'),
                claim_made: checked('claim_made'),
                claim_made_details: val('claim_made_details'),
                police_report: checked('police_report'),
                police_report_details: val('police_report_details'),
                officer_details: val('officer_details'),
                indem_policy: checked('indem_policy'),
                indem_policy_details: val('indem_policy_details'),
                declaration_date: val('declaration_date'),
                digital_signature: val('digital_signature'),
                declaration_agreement: isChecked('declaration_agreement'),
            };

            Object.entries(claimFields).forEach(([key, value]) => {
                if (value !== null && value !== undefined) {
                    formData.append(
                        `form_data[${key}]`,
                        typeof value === 'object' ? JSON.stringify(value) : value
                    );
                }
            });

            // Staff note
            if (isStaff) {
                const note = val('note');
                if (note) formData.append('note', note);
            }

            // New files
            uploadedFiles.forEach((file, index) => {
                formData.append(`documents[${index}]`, file, file.name);
            });

            // Documents marked for deletion
            document.querySelectorAll('[id^="delete-doc-"]:not([disabled])').forEach(input => {
                formData.append('delete_documents[]', input.value);
            });

            const action = document.getElementById('motorForm').dataset.action;
            await submitClaimWithFiles('motorForm', formData, action);
        });
    });
</script>

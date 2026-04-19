<x-layouts.staff>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left sidebar (optional) – you can add claimant info or keep empty -->
        <x-claimant-info />

        <!-- Main form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-hidden border border-gray-200">

                    {{-- Top accent bar --}}
                    {{-- <div class="h-1 bg-[#1a3a5c]"></div> --}}

                    {{-- Main header --}}
                    <div class="px-8 pt-6 pb-0 bg-white">
                        <div class="grid grid-cols-[160px_1fr_auto] items-start gap-6">

                            {{-- Logo --}}
                            <div class="pt-1">
                                <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo"
                                    class="w-36 h-12 object-contain" />
                            </div>

                            {{-- Company name --}}
                            <div class="text-center pt-1">
                                <p class="text-[15px] font-bold text-gray-800 tracking-wide mb-2 border-b border-b-gray-300 pb-2">
                                    Vanguard Assurance Company Ltd
                                </p>
                                <p class="text-[10px] text-gray-500 mt-0.5 tracking-widest uppercase">
                                    We always stand by you
                                </p>
                            </div>

                            {{-- Contact info --}}
                            <div class="text-right text-[11px] text-gray-500 leading-relaxed pt-1">
                                <p>vacmmails@vanguardassurance.com</p>
                                <p>claimsdepartment@vanguardassurance.com</p>
                                <p>030 266 6485 / 6486 / 6487</p>
                                <p>P.O. Box 1868, Accra</p>
                            </div>

                        </div>

                        <div class="border-t border-gray-200 mt-5"></div>
                    </div>

                    {{-- Document title band --}}
                    <div class="bg-[#0b529d] px-8 py-2.5 flex items-center justify-center gap-4">
                        <div class="flex-1 border-t border-white/20"></div>
                        <p class="text-[13px] font-medium tracking-widest uppercase text-white whitespace-nowrap">
                            Motor Accident Report Form
                        </p>
                        <div class="flex-1 border-t border-white/20"></div>
                    </div>

                    {{-- Subtitle --}}
                    <div class="bg-gray-50 border-b border-gray-200 px-8 py-2 text-center">
                        <p class="text-[11.5px] text-gray-500">
                            Please complete all sections accurately. Fields marked * are required.
                        </p>
                    </div>

                </div>

                <!-- Note box remains the same -->
                <div class="py-6 px-12">
                    <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-lg">
                        <p class="text-xs text-gray-700 leading-relaxed">
                            Please note, it is necessary that great care should be taken in completing this form and
                            the information given therein should be strictly accurate, whether it is in your favor
                            or otherwise. You should not make any payment, offer or promise of any payment or admit
                            liability in any way, as by so doing you may prejudice your position and make settlement
                            of the claim difficult.
                        </p>
                    </div>

                    <form id="motorForm">
                        @csrf

                        {{-- SECTION 1: VEHICLE PARTICULARS --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-truck text-blue-500"></i>  --}}
                                PARTICULARS OF MOTOR VEHICLE CONCERNED
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Registration Number
                                        <span class="text-red-500">*</span></label><input type="text"
                                        name="registration_no"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Make <span
                                            class="text-red-500">*</span></label><input type="text" name="make"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Model <span
                                            class="text-red-500">*</span></label><input type="text" name="model"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Year of Make <span
                                            class="text-red-500">*</span></label><input type="date"
                                        name="year_of_make"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                            </div>

                            <!-- Hire Purchase / Loan Agreement -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Is the vehicle the
                                    subject of a hire purchase or loan agreement? <span
                                        class="text-red-500">*</span></label>
                                <div class="flex gap-4">
                                    <label class="flex items-center"><input type="radio" name="hire_purchase"
                                            value="yes" class="conditional-radio mr-2"
                                            data-target="financeCompanySection"> <span>Yes</span></label>
                                    <label class="flex items-center"><input type="radio" name="hire_purchase"
                                            value="no" class="conditional-radio mr-2"
                                            data-target="financeCompanySection"> <span>No</span></label>
                                </div>
                                <div id="financeCompanySection"
                                    class="hidden mt-3 pl-4 border-l-2 border-blue-200 transition-all duration-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Name of finance
                                        company or lending organization <span class="text-red-500">*</span></label>
                                    <input type="text" name="finance_company"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">State fully the purpose
                                    for which the vehicle was being used. <span class="text-red-500">*</span></label>
                                <textarea name="vehicle_purpose" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    placeholder="It is not sufficient to state 'Business' or 'Private'"></textarea>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Was the vehicle being
                                    used with your consent? <span class="text-red-500">*</span></label>
                                <div class="flex gap-4"><label class="flex items-center"><input type="radio"
                                            name="vehicleConsent" value="yes" class="mr-2"> Yes</label><label
                                        class="flex items-center"><input type="radio" name="vehicleConsent"
                                            value="no" class="mr-2"> No</label></div>
                            </div>
                        </section>

                        {{-- SECTION 2: DRIVER PARTICULARS --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                {{-- <i class="fas fa-id-card text-blue-500"></i>  --}}
                                PARTICULARS OF PERSON DRIVING AT THE
                                TIME OF ACCIDENT
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                                            class="text-red-500">*</span></label><input type="text" name="fullname"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                            class="text-red-500">*</span></label><input type="text" name="address"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Age <span
                                            class="text-red-500">*</span></label><input type="number" name="age"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Occupation <span
                                            class="text-red-500">*</span></label><input type="text"
                                        name="occupation"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Telephone <span
                                            class="text-red-500">*</span></label><input type="tel" name="phone"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Driving License
                                        No. <span class="text-red-500">*</span></label><input type="text"
                                        name="drivers_license"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of Issue
                                        <span class="text-red-500">*</span></label><input type="date"
                                        name="license_issue_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Who was driving at the
                                    time of accident? <span class="text-red-500">*</span></label>
                                <select id="driverSelect"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition bg-white">
                                    <option value="">Select driver</option>
                                    <option value="self">Myself</option>
                                    <option value="spouse">Spouse</option>
                                    <option value="family_member">Family Member</option>
                                    <option value="employee">Employee</option>
                                    <option value="friend">Friend</option>
                                    <option value="other">Other Person</option>
                                </select>
                            </div>

                            <!-- Driver Details - Self (readonly) -->
                            <div id="driverDetailsSelf" class="hidden mb-6 transition-all duration-200">
                                <h4 class="text-md font-semibold text-gray-800 mb-3">DRIVER DETAILS (from your
                                    profile)</h4>
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div><label class="block text-xs font-medium text-gray-600">Full
                                                Name</label><input type="text" value="John Doe" readonly
                                                class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded text-gray-700">
                                        </div>
                                        <div><label
                                                class="block text-xs font-medium text-gray-600">Address</label><input
                                                type="text" value="Old Ashongman, Accra Ghana" readonly
                                                class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded text-gray-700">
                                        </div>
                                        <div><label
                                                class="block text-xs font-medium text-gray-600">Telephone</label><input
                                                type="text" value="+233 50 354 5965" readonly
                                                class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded text-gray-700">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Driver Details - Other (editable) -->
                            <div id="driverDetailsOther" class="hidden mb-6 transition-all duration-200">
                                <h4 class="text-md font-semibold text-gray-800 mb-3">DRIVER DETAILS</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                            <span class="text-red-500">*</span></label><input type="text"
                                            name="driver_fullname"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                                class="text-red-500">*</span></label><input type="text"
                                            name="driver_address"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Age <span
                                                class="text-red-500">*</span></label><input type="number"
                                            name="driver_age"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Occupation
                                            <span class="text-red-500">*</span></label><input type="text"
                                            name="driver_occupation"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Telephone
                                            <span class="text-red-500">*</span></label><input type="tel"
                                            name="driver_phone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Driving
                                            License No. <span class="text-red-500">*</span></label><input
                                            type="text" name="driver_license"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of Issue
                                            <span class="text-red-500">*</span></label><input type="date"
                                            name="driver_license_date"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                </div>
                            </div>

                            <!-- Driver Insurance Details -->
                            <div id="driverInsuranceSection" class="hidden mb-6 transition-all duration-200">
                                <label class="block text-sm font-medium text-gray-700 mb-1">State name and address
                                    of the insurer of the person driving and number of the motor vehicle Policy held
                                    by him/her <span class="text-red-500">*</span></label>
                                <textarea name="driver_insurance_details" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    placeholder="e.g., Insurer: XYZ Insurance, Address: 123 Main St, Policy No: ABC123"></textarea>
                            </div>
                        </section>

                        {{-- SECTION 3: ACCIDENT CIRCUMSTANCES --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-blue-500"></i> CIRCUMSTANCES OF ACCIDENT
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date <span
                                            class="text-red-500">*</span></label><input type="date"
                                        name="accident_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Time <span
                                            class="text-red-500">*</span></label><input type="time"
                                        name="accident_time"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                </div>
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">Exact
                                    location of incident <span class="text-red-500">*</span></label><input
                                    type="text" name="exact_location"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">How
                                    many people were in your vehicle at the time of the accident? <span
                                        class="text-red-500">*</span></label><input type="number"
                                    name="people_in_vehicle"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">If you
                                    were not in the vehicle, when was the accident reported to you? <span
                                        class="text-red-500">*</span></label><input type="text" name="report_date"
                                    placeholder="e.g., 2024-11-15"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">Give
                                    full description of how the accident happened <span
                                        class="text-red-500">*</span></label>
                                <textarea name="accident_description" rows="4"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"
                                    placeholder="Provide a detailed description..."></textarea>
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">In
                                    your opinion, was the accident caused by you or your driver? If not, by whom?
                                    <span class="text-red-500">*</span></label><input type="text"
                                    name="fault_person"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">Describe
                                    the damage to
                                    your vehicle <span class="text-red-500">*</span></label>
                                <textarea name="vehicle_damage" rows="2"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
                            </div>
                            <div class="mb-4"><label class="block text-sm font-medium text-gray-700 mb-1">State
                                    exact current location of the damaged vehicle <span
                                        class="text-red-500">*</span></label><input type="text"
                                    name="damaged_vehicle_location"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Repairer
                                    Information</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                            <span class="text-red-500">*</span></label><input type="text"
                                            name="repairer_fullname"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                                class="text-red-500">*</span></label><input type="text"
                                            name="repairer_address"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- SECTION 4: THIRD PARTIES & INJURIES --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-users text-blue-500"></i> THIRD PARTIES INVOLVED IN ACCIDENT
                            </h3>

                            <!-- Injured Persons in Your Vehicle -->
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-md font-semibold text-gray-700">Injured Persons in Your Vehicle
                                    </h4><button type="button" onclick="addInjuredPerson('yourVehicle')"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"><i
                                            class="fas fa-plus mr-1"></i> Add Injured Person</button>
                                </div>
                                <div id="yourVehicleInjuredPersons" class="space-y-4">
                                    <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Full
                                                    Name</label><input type="text"
                                                    name="your_vehicle_injured[0][name]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div><label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Age</label><input
                                                    type="number" name="your_vehicle_injured[0][age]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div><label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Address</label><input
                                                    type="text" name="your_vehicle_injured[0][address]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                        </div>
                                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                                Injuries</label>
                                            <textarea name="your_vehicle_injured[0][injuries]" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                        </div>
                                        <div class="mt-2 flex justify-end"><button type="button"
                                                onclick="removeInjuredPerson(this)"
                                                class="text-red-600 hover:text-red-800 text-sm"><i
                                                    class="fas fa-trash mr-1"></i> Remove</button></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Injured Persons in Other Vehicle -->
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-md font-semibold text-gray-700">Injured Persons in Other
                                        Vehicle</h4><button type="button" onclick="addInjuredPerson('otherVehicle')"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors"><i
                                            class="fas fa-plus mr-1"></i> Add Injured Person</button>
                                </div>
                                <div id="otherVehicleInjuredPersons" class="space-y-4">
                                    <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                            <div><label class="block text-sm font-medium text-gray-700 mb-1">Full
                                                    Name</label><input type="text"
                                                    name="other_vehicle_injured[0][name]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div><label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Age</label><input
                                                    type="number" name="other_vehicle_injured[0][age]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                            <div><label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Address</label><input
                                                    type="text" name="other_vehicle_injured[0][address]"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                                            </div>
                                        </div>
                                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                                Injuries</label>
                                            <textarea name="other_vehicle_injured[0][injuries]" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                        </div>
                                        <div class="mt-2 flex justify-end"><button type="button"
                                                onclick="removeInjuredPerson(this)"
                                                class="text-red-600 hover:text-red-800 text-sm"><i
                                                    class="fas fa-trash mr-1"></i> Remove</button></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Details of vehicle
                                    involved</label>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div><label class="block text-xs font-medium text-gray-600">Registration
                                            Number</label><input type="text" name="involved_vehicle_reg"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                    <div><label class="block text-xs font-medium text-gray-600">Make</label><input
                                            type="text" name="involved_vehicle_make"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                    <div><label class="block text-xs font-medium text-gray-600">Model</label><input
                                            type="text" name="involved_vehicle_model"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                </div>
                            </div>

                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Owner of Accident
                                    Vehicle Information</label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                            <span class="text-red-500">*</span></label><input type="text"
                                            name="owner_fullname"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                                class="text-red-500">*</span></label><input type="text"
                                            name="owner_address"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Telephone
                                            <span class="text-red-500">*</span></label><input type="tel"
                                            name="owner_telephone"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Policy No.
                                            <span class="text-red-500">*</span></label><input type="text"
                                            name="owner_policy"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                </div>
                            </div>

                            <div class="mb-6"><label class="block text-sm font-medium text-gray-700 mb-1">Details of
                                    damage to this
                                    vehicle <span class="text-red-500">*</span></label>
                                <textarea name="other_vehicle_damage" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                            </div>

                            <!-- Conditional: Claim made upon you -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Has any claim been made
                                    upon you? <span class="text-red-500">*</span></label>
                                <div class="flex gap-4"><label class="flex items-center"><input type="radio"
                                            name="claim_made" value="yes" class="conditional-radio mr-2"
                                            data-target="claimMadeSection"> Yes</label><label
                                        class="flex items-center"><input type="radio" name="claim_made"
                                            value="no" class="conditional-radio mr-2"
                                            data-target="claimMadeSection"> No</label></div>
                                <div id="claimMadeSection" class="hidden mt-3 pl-4 border-l-2 border-blue-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State particulars
                                        below and note that any letter or communication received by you must be
                                        forwarded immediately unanswered, to this company. <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="claim_made_details" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                </div>
                            </div>

                            <!-- Conditional: Police report -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Was the accident
                                    reported to the police? <span class="text-red-500">*</span></label>
                                <div class="flex gap-4"><label class="flex items-center"><input type="radio"
                                            name="police_report" value="yes" class="conditional-radio mr-2"
                                            data-target="policeReportSection"> Yes</label><label
                                        class="flex items-center"><input type="radio" name="police_report"
                                            value="no" class="conditional-radio mr-2"
                                            data-target="policeReportSection"> No</label></div>
                                <div id="policeReportSection" class="hidden mt-3 pl-4 border-l-2 border-blue-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State when it was
                                        reported and at which Police Station. <span
                                            class="text-red-500">*</span></label>
                                    <textarea name="police_report_details" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                </div>
                            </div>

                            <div class="mb-6"><label class="block text-sm font-medium text-gray-700 mb-1">Name
                                    Police Officer who took particulars <span
                                        class="text-red-500">*</span></label><input type="text"
                                    name="officer_details" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                            </div>

                            <!-- Conditional: Indemnifying policy -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Do you hold more than
                                    one policy indemnifying you in respect of this accident? <span
                                        class="text-red-500">*</span></label>
                                <div class="flex gap-4"><label class="flex items-center"><input type="radio"
                                            name="indem_policy" value="yes" class="conditional-radio mr-2"
                                            data-target="indemPolicySection"> Yes</label><label
                                        class="flex items-center"><input type="radio" name="indem_policy"
                                            value="no" class="conditional-radio mr-2"
                                            data-target="indemPolicySection"> No</label></div>
                                <div id="indemPolicySection" class="hidden mt-3 pl-4 border-l-2 border-blue-200">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">State the details of
                                        the other policy.</label>
                                    <textarea name="indem_policy_details" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea>
                                </div>
                            </div>
                        </section>

                        {{-- SECTION 5: Image Upload Section --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-camera text-blue-500"></i> Add Images
                            </h3>
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition cursor-pointer"
                                id="dropzone">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                                <p class="text-gray-600">Drag & drop images here or <span
                                        class="text-blue-600 font-medium">browse</span></p>
                                <p class="text-xs text-gray-400 mt-1">Supports: JPG, PNG, GIF (max 5MB each)</p>
                                <input type="file" id="imageUpload" accept="image/jpeg,image/png,image/gif"
                                    multiple class="hidden">
                            </div>
                            <div id="imagePreviewContainer" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3"></div>
                        </section>

                        {{-- SECTION 6: DECLARATION --}}
                        <section class="mb-8">
                            <h3
                                class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2 flex items-center gap-2">
                                <i class="fas fa-file-signature text-blue-500"></i> DECLARATION
                            </h3>
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                                <p class="text-sm text-gray-700 leading-relaxed mb-4">I declare that the above
                                    statement is true in all respects to the best of my knowledge and belief and I
                                    hereby leave in the hands of the Company in accordance with the conditions of
                                    the Policy the conduct of all claims and litigation arising out of this accident
                                    and to which the Policy applies, to deal with, to prosecute and/or settle as
                                    they deem fit without further reference to me; and I undertake to give all such
                                    information and assistance as the Company may require.</p>
                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                                    <p class="text-sm text-gray-700"><span class="font-semibold">Note:</span> The
                                        Company does not admit liability by the issue of this form.</p>
                                </div>
                            </div>
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 mb-6">
                                <label class="flex items-start cursor-pointer"><input type="checkbox"
                                        name="declaration_agreement" required
                                        class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500"><span
                                        class="ml-3 text-sm text-gray-700">I have read and understood the
                                        declaration above. I confirm that all information provided in this form is
                                        true and accurate to the best of my knowledge. <span
                                            class="text-red-500">*</span></span></label>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Date of
                                        Declaration <span class="text-red-500">*</span></label><input type="date"
                                        name="declaration_date"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                                <div><label class="block text-sm font-medium text-gray-700 mb-1">Digital Signature
                                        <span class="text-xs text-gray-500">(Type your full name)</span> <span
                                            class="text-red-500">*</span></label><input type="text"
                                        name="digital_signature"
                                        placeholder="Type your full name as your digital signature"
                                        class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-cursive text-lg"
                                        style="font-family: 'Brush Script MT', cursive;">
                                    <p class="text-xs text-gray-500 mt-1">By typing your name above, you are
                                        providing a legal digital signature for this declaration.</p>
                                </div>
                            </div>
                        </section>

                        <div class="mt-8 pt-4 border-t border-gray-200"><button type="submit"
                                class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors flex items-center justify-center gap-2"><span>Submit
                                    Claim</span><i class="fas fa-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    @verbatim
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Conditional sections toggle
                document.querySelectorAll('.conditional-radio').forEach(radio => {
                    radio.addEventListener('change', function() {
                        const targetId = this.getAttribute('data-target');
                        const target = document.getElementById(targetId);
                        const isYes = this.value === 'yes';
                        if (isYes) {
                            target.classList.remove('hidden');
                            target.querySelectorAll('input, textarea, select').forEach(input => input
                                .required = true);
                        } else {
                            target.classList.add('hidden');
                            target.querySelectorAll('input, textarea, select').forEach(input => {
                                input.required = false;
                                input.value = '';
                            });
                        }
                    });
                });

                // Driver selection logic
                const driverSelect = document.getElementById('driverSelect');
                const selfDiv = document.getElementById('driverDetailsSelf');
                const otherDiv = document.getElementById('driverDetailsOther');
                const insuranceDiv = document.getElementById('driverInsuranceSection');
                const otherInputs = otherDiv.querySelectorAll('input, textarea');
                const insuranceTextarea = document.getElementById('driver_insurance_details');

                function resetDriverSections() {
                    [selfDiv, otherDiv, insuranceDiv].forEach(div => {
                        if (!div.classList.contains('hidden')) {
                            div.classList.add('hidden');
                            div.querySelectorAll('input, textarea, select').forEach(field => {
                                field.required = false;
                                if (field !== insuranceTextarea) field.value = '';
                            });
                        }
                    });
                    if (insuranceTextarea) {
                        insuranceTextarea.required = false;
                        insuranceTextarea.value = '';
                    }
                }

                driverSelect.addEventListener('change', function() {
                    resetDriverSections();
                    const val = this.value;
                    if (val === 'self') {
                        selfDiv.classList.remove('hidden');
                        insuranceDiv.classList.remove('hidden');
                        insuranceTextarea.required = true;
                    } else if (val !== '') {
                        otherDiv.classList.remove('hidden');
                        insuranceDiv.classList.remove('hidden');
                        otherInputs.forEach(i => i.required = true);
                        insuranceTextarea.required = true;
                    }
                });

                // Injured persons dynamic rows
                window.yourVehicleCounter = 1;
                window.otherVehicleCounter = 1;

                window.addInjuredPerson = function(type) {
                    const container = type === 'yourVehicle' ? document.getElementById(
                        'yourVehicleInjuredPersons') : document.getElementById('otherVehicleInjuredPersons');
                    const counter = type === 'yourVehicle' ? window.yourVehicleCounter++ : window
                        .otherVehicleCounter++;
                    const prefix = type === 'yourVehicle' ? 'your_vehicle_injured' : 'other_vehicle_injured';
                    const newRow = document.createElement('div');
                    newRow.className = 'injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50';
                    newRow.innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label><input type="text" name="${prefix}[${counter}][name]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Age</label><input type="number" name="${prefix}[${counter}][age]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                        <div><label class="block text-sm font-medium text-gray-700 mb-1">Address</label><input type="text" name="${prefix}[${counter}][address]" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></div>
                    </div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-1">Extent of Injuries</label><textarea name="${prefix}[${counter}][injuries]" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-lg"></textarea></div>
                    <div class="mt-2 flex justify-end"><button type="button" onclick="removeInjuredPerson(this)" class="text-red-600 hover:text-red-800 text-sm"><i class="fas fa-trash mr-1"></i> Remove</button></div>
                `;
                    container.appendChild(newRow);
                };

                window.removeInjuredPerson = function(btn) {
                    const row = btn.closest('.injured-person-row');
                    const container = row.parentElement;
                    if (container.querySelectorAll('.injured-person-row').length > 1) row.remove();
                    else alert('You need at least one injured person section. You can clear the fields instead.');
                };

                // Form submission demo
                document.getElementById('motorForm').addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'success',
                        title: 'Claim Submitted Successfully',
                        text: 'Your claim has been submitted. Our team will review it shortly.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#4f46e5'
                    });
                });

                // Image upload handling
                const dropzone = document.getElementById('dropzone');
                const fileInput = document.getElementById('imageUpload');
                const previewContainer = document.getElementById('imagePreviewContainer');
                let uploadedFiles = []; // store files for later submission

                function renderPreviews() {
                    previewContainer.innerHTML = '';
                    uploadedFiles.forEach((file, index) => {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            const div = document.createElement('div');
                            div.className = 'relative group';
                            div.innerHTML = `
                <img src="${e.target.result}" class="w-full h-24 object-cover rounded-lg border border-gray-200 shadow-sm">
                <button type="button" onclick="removeImage(${index})" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs opacity-0 group-hover:opacity-100 transition">✕</button>
            `;
                            previewContainer.appendChild(div);
                        };
                        if (file) reader.readAsDataURL(file);
                    });
                }

                window.removeImage = (index) => {
                    uploadedFiles.splice(index, 1);
                    renderPreviews();
                    // Update file input's FileList (optional: you can recreate a new DataTransfer)
                    const dataTransfer = new DataTransfer();
                    uploadedFiles.forEach(f => dataTransfer.items.add(f));
                    fileInput.files = dataTransfer.files;
                };

                dropzone.addEventListener('click', () => fileInput.click());
                dropzone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropzone.classList.add('border-blue-500', 'bg-blue-50');
                });
                dropzone.addEventListener('dragleave', () => {
                    dropzone.classList.remove('border-blue-500', 'bg-blue-50');
                });
                dropzone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropzone.classList.remove('border-blue-500', 'bg-blue-50');
                    const files = Array.from(e.dataTransfer.files).filter(f => f.type.startsWith('image/'));
                    uploadedFiles.push(...files);
                    renderPreviews();
                    // Sync file input
                    const dataTransfer = new DataTransfer();
                    uploadedFiles.forEach(f => dataTransfer.items.add(f));
                    fileInput.files = dataTransfer.files;
                });
                fileInput.addEventListener('change', (e) => {
                    const newFiles = Array.from(e.target.files);
                    uploadedFiles.push(...newFiles);
                    renderPreviews();
                });
            });
        </script>
    @endverbatim
</x-layouts.staff>

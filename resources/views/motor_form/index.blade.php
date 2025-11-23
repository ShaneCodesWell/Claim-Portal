<x-layouts.app>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Section - Claimant Information -->
        <x-claimant-info />

        <!-- Right Section - Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow px-10 py-6">
                <h1 class="text-2xl font-bold text-gray-900 text-center mb-3 pb-3 border-b border-gray-600 mx-auto"
                    style="max-width: 400px;">
                    MOTOR ACCIDENT REPORT FORM
                </h1>

                <div class="pb-4 mb-6">
                    <p class="text-sm text-gray-700 leading-relaxed">
                        Please note, it is necessary that great care should be taken in
                        completing this form and the information given therein should be
                        strictly accurate, whether it is in your favor or otherwise. You
                        should not make any payment, offer or promise of any payment or
                        admit liability in any way, as by so doing you may prejudice
                        your position and make settlement of the claim difficult.
                    </p>
                </div>

                <form id="motorForm">
                    {{-- Section 1 --}}
                    <section>
                        <!-- Particulars of Motor Vehicle Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                PARTICULARS OF MOTOR VEHICLE CONCERNED:
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="registration_no" id="registration_no" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Make <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="make" id="make" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Model <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="model" id="model" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Year of Make <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Hire Purchase/Loan Agreement Section -->
                        <x-conditional-section
                            question="Is the vehicle the subject of a hire purchase or loan agreement?"
                            name="hire_purchase" yes-section-id="financeCompanySection" required="true">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                If so, state the name of the finance company or lending organization
                                <span class="text-red-500">*</span>
                            </label>
                            <x-input name="finance_company" required />
                        </x-conditional-section>

                        <!-- Purpose of Vehicle Use -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State fully the purpose for which the vehicle was being used.
                                <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 block mt-1">(It is not sufficient to state "Business"
                                    or
                                    "Private")</span>
                            </label>
                            <x-textarea name="vehicle_purpose" required rows="4"
                                placeholder="Please provide detailed description of vehicle usage..." />
                        </div>

                        <!-- Vehicle Consent Section -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Was the vehicle being used with your consent?
                                <span class="text-red-500">*</span>
                            </label>
                            <div class="flex gap-4">
                                <label class="flex items-center">
                                    <input type="radio" name="vehicleConsent" value="yes" required
                                        class="mr-2" />
                                    <span class="text-sm text-gray-700">Yes</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="vehicleConsent" value="no" required
                                        class="mr-2" />
                                    <span class="text-sm text-gray-700">No</span>
                                </label>
                            </div>
                        </div>
                    </section>

                    {{-- Section 2 --}}
                    <section>
                        <!-- Particulars of Person Driving Vehicle Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                PARTICULARS OF PERSON DRIVING AT THE TIME OF ACCIDENT:
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="fullname" id="fullname" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address<span
                                            class="text-red-500">*</span></label>
                                    <x-input name="address" id="address" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Age<span
                                            class="text-red-500">*</span></label>
                                    <x-input name="age" id="age" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Occupation <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="age" id="age" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telephone <span
                                            class="text-red-500">*</span></label>
                                    <x-input name="phone" id="phone" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Driving License No.
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="drivers_license" id="drivers_license" required />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Issue <span
                                            class="text-red-500">*</span></label>
                                    <input type="date" required
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Who was driving? -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Who was driving at the time of accident? <span class="text-red-500">*</span>
                            </label>
                            <select id="driverSelect" required
                                class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none">
                                <option value="">Select driver</option>
                                <option value="self">Myself</option>
                                <option value="spouse">Spouse</option>
                                <option value="family_member">Family Member</option>
                                <option value="employee">Employee</option>
                                <option value="friend">Friend</option>
                                <option value="other">Other Person</option>
                            </select>
                        </div>

                        <!-- Driver Details - Shows for "Myself" (Readonly) -->
                        <div id="driverDetailsSelf" class="hidden overflow-hidden mb-6">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                DRIVER DETAILS:
                            </h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Full Name</label>
                                        <input type="text" value="Shane Mensah" readonly
                                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm text-gray-900 cursor-not-allowed">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Address</label>
                                        <input type="text" value="Old Ashongman, Accra Ghana." readonly
                                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm text-gray-900 cursor-not-allowed">
                                    </div>

                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 mb-1">Telephone</label>
                                        <input type="text" value="+233 50 354 5965" readonly
                                            class="w-full px-3 py-2 bg-white border border-gray-300 rounded text-sm text-gray-900 cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Driver Details - Shows for Other Persons (Input Fields) -->
                        <div id="driverDetailsOther" class="hidden overflow-hidden mb-6 px-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                DRIVER DETAILS:
                            </h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="driver_fullname" id="driver_fullname" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="driver_address" id="driver_address" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Age
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="driver_age" id="driver_age" type="number" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Occupation
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="driver_occupation" id="driver_occupation" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telephone
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="driver_phone" id="driver_phone" type="tel" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Driving License No.
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="driver_license" id="driver_license" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date of Issue
                                        <span class="text-red-500">*</span></label>
                                    <input type="date" name="driver_license_date" id="driver_license_date"
                                        class="w-full px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Driver Insurance Details -->
                        <div id="driverInsuranceSection" class="hidden overflow-hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State name and address of the insurer of the person driving and number of the motor
                                vehicle Policy held by him/her <span class="text-red-500">*</span>
                            </label>
                            <div class="px-2">
                                <x-textarea name="driver_insurance_details" id="driver_insurance_details"
                                    rows="4"
                                    placeholder="Please provide insurer name, address, and policy number..." />
                            </div>
                        </div>

                    </section>

                    {{-- Section 3 --}}
                    <section>
                        <!-- Particulars of Motor Vehicle Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                CIRCUMSTANCES OF ACCIDENT:
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Date
                                        <span class="text-red-500">*</span></label>
                                    <input type="date" name="accident_date" id="accident_date" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Time
                                        <span class="text-red-500">*</span></label>
                                    <input type="time" name="accident_time" id="accident_time" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Exact Location -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Exact location of incident
                                <span class="text-red-500">*</span>
                            </label>
                            <x-input name="exact_location" id="exact_location" required />
                        </div>

                        <!-- People in the vehicle -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                How many people were in your vehicle at the time of the accident?
                                <span class="text-red-500">*</span>
                            </label>
                            <x-input name="people_in_vehicle" id="people_in_vehicle" required />
                        </div>

                        <!-- Vehicle Report Date -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                If you were not in the vehicle, when was the accident reported to you?
                                <span class="text-red-500">*</span>
                            </label>
                            <x-input name="report_date" id="report_date" required />
                        </div>

                        <!-- Full Accident Description -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Give full description of how the accident happened
                                <span class="text-red-500">*</span>
                            </label>
                            <x-textarea name="vehicle_purpose" required rows="4"
                                placeholder="Please provide a detailed description of how the accident happened..." />
                        </div>

                        <!-- Who caused the accident -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                In your opinion, was the accident caused by you or your driver? If not, by whom?
                                <span class="text-red-500">*</span>
                            </label>
                            <x-input name="fault_person" id="fault_person" required />
                        </div>

                        <!-- Damage to Vehicle -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Describe the damage to your vehicle
                                <span class="text-red-500">*</span>
                            </label>
                            <x-textarea name="vehicle_purpose" required rows="2"
                                placeholder="Please provide a detailed description of the damage to the vehicle..." />
                        </div>

                        <!-- Accident Location-->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State exact current location of the damaged vehicle
                                <span class="text-red-500">*</span>
                            </label>
                            <x-input name="accident_location" id="accident_location" required />
                        </div>

                        <!-- Name and Address of Repairer -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Repairer Information
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="repairer_fullname" id="repairer_fullname" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="repairer_address" id="repairer_address" />
                                </div>
                            </div>
                        </div>
                    </section>

                    {{-- Section 4 --}}
                    <section>
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                THIRD PARTIES INVOLVED IN ACCIDENT:
                            </h3>

                            <!-- Injured Persons in Your Vehicle -->
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-md font-semibold text-gray-700">Injured Persons in Your Vehicle
                                    </h4>
                                    <button type="button" onclick="addInjuredPerson('yourVehicle')"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-1"></i> Add Injured Person
                                    </button>
                                </div>

                                <div id="yourVehicleInjuredPersons" class="space-y-4">
                                    <!-- Initial row -->
                                    <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Full
                                                    Name</label>
                                                <x-input name="your_vehicle_injured[0][name]"
                                                    placeholder="Full name of injured person" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                                <x-input type="number" name="your_vehicle_injured[0][age]"
                                                    placeholder="Age" min="1" max="120" />
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                                <x-input name="your_vehicle_injured[0][address]"
                                                    placeholder="Full address" />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                                    Injuries</label>
                                                <x-textarea name="your_vehicle_injured[0][injuries]" rows="2"
                                                    placeholder="Describe the nature and extent of injuries..." />
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-end">
                                            <button type="button" onclick="removeInjuredPerson(this)"
                                                class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                                <i class="fas fa-trash mr-1"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Injured Persons in Other Vehicle -->
                            <div class="mb-8">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-md font-semibold text-gray-700">Injured Persons in Other Vehicle
                                    </h4>
                                    <button type="button" onclick="addInjuredPerson('otherVehicle')"
                                        class="px-4 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700 transition-colors">
                                        <i class="fas fa-plus mr-1"></i> Add Injured Person
                                    </button>
                                </div>

                                <div id="otherVehicleInjuredPersons" class="space-y-4">
                                    <!-- Initial row -->
                                    <div class="injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50">
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Full
                                                    Name</label>
                                                <x-input name="other_vehicle_injured[0][name]"
                                                    placeholder="Full name of injured person" />
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                                                <x-input type="number" name="other_vehicle_injured[0][age]"
                                                    placeholder="Age" min="1" max="120" />
                                            </div>
                                            <div>
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                                <x-input name="other_vehicle_injured[0][address]"
                                                    placeholder="Full address" />
                                            </div>
                                        </div>
                                        <div class="grid grid-cols-1 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Extent of
                                                    Injuries</label>
                                                <x-textarea name="other_vehicle_injured[0][injuries]" rows="2"
                                                    placeholder="Describe the nature and extent of injuries..." />
                                            </div>
                                        </div>
                                        <div class="mt-2 flex justify-end">
                                            <button type="button" onclick="removeInjuredPerson(this)"
                                                class="text-red-600 hover:text-red-800 text-sm flex items-center">
                                                <i class="fas fa-trash mr-1"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Involved Details -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Details of vehicle involved
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Registration
                                        Number</label>
                                    <x-input name="involved_vehicle_reg" id="involved_vehicle_reg" required
                                        placeholder="Registration No." />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Make</label>
                                    <x-input name="involved_vehicle_make" id="involved_vehicle_make" required
                                        placeholder="Vehicle Make" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Model</label>
                                    <x-input name="involved_vehicle_model" id="involved_vehicle_model" required
                                        placeholder="Vehicle Model" />
                                </div>
                            </div>
                        </div>

                        <!-- Owner of Accident Vehicle Information -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Owner of Accident Vehicle Information
                            </label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="owner_fullname" id="owner_fullname" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="owner_address" id="owner_address" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Telephone
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="owner_telephone" id="owner_telephone" />
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Policy No.
                                        <span class="text-red-500">*</span></label>
                                    <x-input name="owner_policy" id="owner_policy" />
                                </div>
                            </div>
                        </div>

                        <!-- Damage to Vehicle Details -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Details of damaged to this vehicle
                                <span class="text-red-500">*</span>
                            </label>
                            <x-textarea name="vehicle_purpose" required rows="2"
                                placeholder="Please provide a detailed description of the damage to the vehicle..." />
                        </div>

                        <!-- Claim Made -->
                        <x-conditional-section question="Has any claim been made upon you?" name="claim_made"
                            yes-section-id="claim_made" required="true">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State particulars below and note that any letter or communication
                                received by you must be forwarded immediately unanswered, to this company.
                                <span class="text-red-500">*</span>
                            </label>
                            <x-textarea name="claim_made_details" required rows="3"
                                placeholder="Describe the terms and conditions of the claim..." />
                        </x-conditional-section>

                        <!-- Accident Reported -->
                        <x-conditional-section question="Was the accident reported to the police?"
                            name="police_report" yes-section-id="police_report" required="true">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State when it was reported and at which Police Station.
                                <span class="text-red-500">*</span>
                            </label>
                            <x-textarea name="claim_made_details" required rows="2"
                                placeholder="Describe the details of the reports..." />
                        </x-conditional-section>

                        <!-- Police Officer Details -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Name Police Officer who took particulars
                                <span class="text-red-500">*</span>
                            </label>
                            <x-input name="officer_details" id="officer_details" required />
                        </div>

                        <!-- Indemnifying Policy -->
                        <x-conditional-section
                            question="Do you hold more than one policy indemnifying you in respect of this accident?"
                            name="indem_policy" yes-section-id="indem_policy" required="true">

                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                State the details of the other policy.
                            </label>
                            <x-textarea name="claim_made_details" required rows="2"
                                placeholder="Describe the details of the policy..." />
                        </x-conditional-section>
                    </section>

                    {{-- Section 5 --}}
                    <section>
                        <!-- Declaration -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-b-gray-300 pb-2">
                                DECLARATION:
                            </h3>

                            <!-- Declaration Text -->
                            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                                <p class="text-sm text-gray-700 leading-relaxed mb-4">
                                    I declare that the above statement is true in all respects to the best of my
                                    knowledge and belief and I hereby leave in the hands of the
                                    Company in accordance with the conditions of the Policy the conduct of all claims
                                    and litigation arising out of this accident and to
                                    which the Policy applies, to deal with, to prosecute and/or settle as they deem fit
                                    without further reference to me; and I undertake to
                                    give all such information and assistance as the Company may require.
                                </p>

                                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-3 rounded">
                                    <p class="text-sm text-gray-700">
                                        <span class="font-semibold text-gray-900">Note:</span> The Company does not
                                        admit liability by the
                                        issue of this form.
                                    </p>
                                </div>
                            </div>

                            <!-- Agreement Checkbox -->
                            <div class="bg-white border-2 border-blue-200 rounded-lg p-4 mb-6">
                                <label class="flex items-start cursor-pointer group">
                                    <input type="checkbox" name="declaration_agreement" id="declaration_agreement"
                                        required
                                        class="mt-1 w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-2 focus:ring-blue-500" />
                                    <span class="ml-3 text-sm text-gray-700 select-none">
                                        I have read and understood the declaration above. I confirm that all information
                                        provided in this form is true and accurate to the best of my knowledge.
                                        <span class="text-red-500">*</span>
                                    </span>
                                </label>
                            </div>

                            <!-- Date and Claimant Name -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Date of Declaration <span class="text-red-500">*</span>
                                    </label>
                                    <input type="date" name="declaration_date" id="declaration_date" required
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div>

                                {{-- <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Full Name (Claimant) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="claimant_full_name" id="claimant_full_name" required
                                        placeholder="Type your full name as confirmation"
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none" />
                                </div> --}}
                            </div>

                            <!-- Optional: Digital Signature (Simple text input) -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Digital Signature <span class="text-xs text-gray-500">(Type your full name)</span>
                                    <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" name="digital_signature" id="digital_signature" required
                                        placeholder="Type your full name as your digital signature"
                                        class="w-full px-3 py-2 border-2 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none font-cursive text-lg"
                                        style="font-family: 'Brush Script MT', cursive;" />
                                    <div class="absolute bottom-2 left-3 right-3 border-b border-gray-300"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">
                                    By typing your name above, you are providing a legal digital signature for this
                                    declaration.
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Submit Button -->
                    <div class="mt-8 pt-4 border-t border-t-gray-300">
                        <button type="submit"
                            class="w-full md:w-auto px-6 py-2 bg-blue-600 text-white font-medium rounded hover:bg-blue-700 transition-colors flex items-center justify-center gap-2">
                            <span>Submit Claim</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Generic conditional section toggle
            document.querySelectorAll('.conditional-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const targetId = this.getAttribute('data-target');
                    const targetSection = document.getElementById(targetId);
                    const isYes = this.value === 'yes';

                    if (isYes) {
                        targetSection.classList.remove('hidden');
                        void targetSection.offsetWidth;
                        targetSection.classList.remove('slide-up');
                        targetSection.classList.add('slide-down');

                        // Make all inputs in the section required
                        targetSection.querySelectorAll('input, textarea, select').forEach(input => {
                            input.required = true;
                        });
                    } else {
                        targetSection.classList.remove('slide-down');
                        targetSection.classList.add('slide-up');
                        setTimeout(() => {
                            targetSection.classList.add('hidden');
                        }, 300);

                        // Remove required and clear values
                        targetSection.querySelectorAll('input, textarea, select').forEach(input => {
                            input.required = false;
                            input.value = '';
                        });
                    }
                });
            });

            // Driver selection logic
            const driverSelect = document.getElementById("driverSelect");
            const driverDetailsSelf = document.getElementById("driverDetailsSelf");
            const driverDetailsOther = document.getElementById("driverDetailsOther");
            const driverInsuranceSection = document.getElementById("driverInsuranceSection");

            // Get all input fields in the "other driver" section
            const otherDriverInputs = driverDetailsOther.querySelectorAll('input, textarea');
            const insuranceTextarea = document.getElementById("driver_insurance_details");

            driverSelect.addEventListener("change", function() {
                const selectedValue = this.value;

                // Hide all sections first with animation
                if (!driverDetailsSelf.classList.contains("hidden")) {
                    driverDetailsSelf.classList.remove("slide-down");
                    driverDetailsSelf.classList.add("slide-up");
                    setTimeout(() => driverDetailsSelf.classList.add("hidden"), 300);
                }

                if (!driverDetailsOther.classList.contains("hidden")) {
                    driverDetailsOther.classList.remove("slide-down");
                    driverDetailsOther.classList.add("slide-up");
                    setTimeout(() => driverDetailsOther.classList.add("hidden"), 300);
                    // Remove required from other driver inputs
                    otherDriverInputs.forEach(input => {
                        input.required = false;
                        input.value = "";
                    });
                }

                if (!driverInsuranceSection.classList.contains("hidden")) {
                    driverInsuranceSection.classList.remove("slide-down");
                    driverInsuranceSection.classList.add("slide-up");
                    setTimeout(() => driverInsuranceSection.classList.add("hidden"), 300);
                    insuranceTextarea.required = false;
                    insuranceTextarea.value = "";
                }

                // Show appropriate section based on selection
                if (selectedValue === "self") {
                    // Show readonly section for "Myself"
                    setTimeout(() => {
                        driverDetailsSelf.classList.remove("hidden");
                        void driverDetailsSelf.offsetWidth;
                        driverDetailsSelf.classList.remove("slide-up");
                        driverDetailsSelf.classList.add("slide-down");
                    }, 100);

                    // Show insurance section
                    setTimeout(() => {
                        driverInsuranceSection.classList.remove("hidden");
                        void driverInsuranceSection.offsetWidth;
                        driverInsuranceSection.classList.remove("slide-up");
                        driverInsuranceSection.classList.add("slide-down");
                        insuranceTextarea.required = true;
                    }, 200);

                } else if (selectedValue !== "") {
                    // Show input fields for other persons
                    setTimeout(() => {
                        driverDetailsOther.classList.remove("hidden");
                        void driverDetailsOther.offsetWidth;
                        driverDetailsOther.classList.remove("slide-up");
                        driverDetailsOther.classList.add("slide-down");
                        // Make other driver inputs required
                        otherDriverInputs.forEach(input => input.required = true);
                    }, 100);

                    // Show insurance section
                    setTimeout(() => {
                        driverInsuranceSection.classList.remove("hidden");
                        void driverInsuranceSection.offsetWidth;
                        driverInsuranceSection.classList.remove("slide-up");
                        driverInsuranceSection.classList.add("slide-down");
                        insuranceTextarea.required = true;
                    }, 200);
                }
            });

        });

        let yourVehicleCounter = 1;
        let otherVehicleCounter = 1;

        function addInjuredPerson(type) {
            const container = type === 'yourVehicle' ?
                document.getElementById('yourVehicleInjuredPersons') :
                document.getElementById('otherVehicleInjuredPersons');

            const counter = type === 'yourVehicle' ? yourVehicleCounter++ : otherVehicleCounter++;
            const prefix = type === 'yourVehicle' ? 'your_vehicle_injured' : 'other_vehicle_injured';

            const newRow = document.createElement('div');
            newRow.className = 'injured-person-row border border-gray-200 rounded-lg p-4 bg-gray-50';
            newRow.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <x-input name="${prefix}[${counter}][name]" placeholder="Full name of injured person" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Age</label>
                        <x-input type="number" name="${prefix}[${counter}][age]" placeholder="Age" min="1" max="120" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                        <x-input name="${prefix}[${counter}][address]" placeholder="Full address" />
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Extent of Injuries</label>
                        <x-textarea name="${prefix}[${counter}][injuries]" rows="2" 
                                placeholder="Describe the nature and extent of injuries..." />
                    </div>
                </div>
                <div class="mt-2 flex justify-end">
                    <button type="button" onclick="removeInjuredPerson(this)" 
                            class="text-red-600 hover:text-red-800 text-sm flex items-center">
                        <i class="fas fa-trash mr-1"></i> Remove
                    </button>
                </div>
            `;

            container.appendChild(newRow);
        }

        function removeInjuredPerson(button) {
            const row = button.closest('.injured-person-row');
            // Don't remove if it's the last row
            const container = row.parentElement;
            if (container.querySelectorAll('.injured-person-row').length > 1) {
                row.remove();
            } else {
                alert('You need at least one injured person section. You can clear the fields instead.');
            }
        }
    </script>
</x-layouts.app>

<?php

// Builders local to this file. Only the resulting arrays are returned.
$field = fn (string $label, string $type, array $rules, bool $required = false, array $extra = []) => array_merge(
    ['label' => $label, 'type' => $type, 'required' => $required, 'rules' => $rules],
    $extra
);
$str   = fn (string $l, bool $r = false, int $max = 255) => $field($l, 'string', ['string', "max:$max"], $r);
$text  = fn (string $l, bool $r = false) => $field($l, 'text', ['string', 'max:5000'], $r);
$date  = fn (string $l, bool $r = false, array $rules = []) => $field($l, 'date', array_merge(['date'], $rules), $r);
$int   = fn (string $l, int $max = 120) => $field($l, 'integer', ['integer', "between:0,$max"]);
$num   = fn (string $l) => $field($l, 'number', ['numeric', 'min:0']);
$yn    = fn (string $l) => $field($l, 'enum', ['in:yes,no'], false, ['options' => ['yes', 'no']]);
$enum  = fn (string $l, array $o, bool $r = false) => $field($l, 'enum', ['in:' . implode(',', $o)], $r, ['options' => $o]);
$list  = fn (string $l, array $items) => $field($l, 'list', ['array', 'max:50'], false, ['items' => $items]);
$when  = fn (array $f, string $on, string $values) => array_merge($f, ['conditional' => "required_if:form_data.$on,$values"]);

$declaration = [
    'declaration_date'      => $date('Declaration date (defaults to today)'),
    'digital_signature'     => $str('Digital signature (full name)', true),
    'declaration_agreement' => $field('Declaration accepted', 'bool', ['accepted'], true),
];

$person   = ['name' => ['string', 'max:255'], 'age' => ['integer', 'between:0,120'], 'address' => ['string', 'max:255'], 'injuries' => ['string', 'max:1000']];
$vehicle  = ['reg' => ['string', 'max:30'], 'make' => ['string', 'max:100'], 'model' => ['string', 'max:100']];
$property = ['qty' => ['numeric', 'min:0'], 'description' => ['string', 'max:255'], 'price_paid' => ['numeric', 'min:0'],
             'depreciation' => ['numeric', 'min:0'], 'claim_amount' => ['numeric', 'min:0']];

$otherDriver = 'spouse,family_member,employee,friend,other';
$anyDriver   = 'self,' . $otherDriver;

return [
    'motor' => [
        'label'         => 'Motor',
        'event_field'   => 'accident_date',
        'server_fields' => ['registration_no', 'make', 'model', 'year_of_make'],
        'notes'         => 'Vehicle details come from the policy (and risk_id, the plate number, for fleet policies). Do not send them.',
        'fields'        => [
            'vehicle_purpose'          => $text('Purpose the vehicle is used for'),
            'vehicle_consent'          => $yn('Driver had the owner\'s consent'),
            'driver_type'              => $enum('Who was driving', explode(',', $anyDriver), true),
            'third_party_involved'     => $yn('Third party involved'),
            'age'                      => $int('Claimant age'),
            'occupation'               => $str('Claimant occupation'),
            'claimant_ghana_card'      => $str('Ghana Card number', false, 30),
            'claimant_tin'             => $str('TIN', false, 30),
            'driver_fullname'          => $when($str('Driver full name'), 'driver_type', $otherDriver),
            'driver_address'           => $when($str('Driver address'), 'driver_type', $otherDriver),
            'driver_occupation'        => $when($str('Driver occupation'), 'driver_type', $otherDriver),
            'driver_phone'             => $when($str('Driver phone', false, 30), 'driver_type', $otherDriver),
            'driver_age'               => $when($int('Driver age'), 'driver_type', $anyDriver),
            'driver_license'           => $when($str('Driver licence number', false, 50), 'driver_type', $anyDriver),
            'driver_license_date'      => $when($date('Licence issue date'), 'driver_type', $anyDriver),
            'driver_insurance_details' => $when($text('Driver\'s own insurance details'), 'driver_type', $anyDriver),
            'accident_date'            => $date('Date of accident', true, ['before_or_equal:today']),
            'exact_location'           => $str('Exact location', true),
            'people_in_vehicle'        => $int('People in the vehicle', 100),
            'report_date'              => $str('Date the accident was reported', false, 100),
            'accident_description'     => $text('Description of the accident', true),
            'fault_person'             => $yn('Someone else at fault'),
            'fault_person_details'     => $when($text('Details of the person at fault'), 'fault_person', 'yes'),
            'vehicle_damage'           => $text('Damage to the vehicle'),
            'damaged_vehicle_location' => $str('Where the vehicle is now'),
            'repairer_fullname'        => $str('Repairer name'),
            'repairer_address'         => $str('Repairer address'),
            'your_vehicle_injured'     => $list('Injured persons in your vehicle', $person),
            'other_vehicle_injured'    => $list('Injured persons in other vehicles', $person),
            'involved_vehicles'        => $list('Other vehicles involved', $vehicle),
            'police_report'            => $yn('Police report made'),
            'police_report_details'    => $when($text('Police report details'), 'police_report', 'yes'),
            'payment_type'             => $enum('Payment method', ['bank', 'mobile_money', 'other'], true),
            'payment_account'          => $str('Account / mobile money number', true, 100),
            'payment_provider'         => $when($str('Bank or network name', false, 100), 'payment_type', 'bank,mobile_money'),
            'damage_type'              => $enum('Type of damage', ['collision', 'fire', 'theft', 'vandalism', 'flood', 'natural_disaster', 'windscreen', 'other'], true),
            'damage_severity'          => $enum('Damage severity', ['minor', 'moderate', 'severe', 'total_loss']),
            'estimated_repair_cost'    => $num('Estimated repair cost (GHS)'),
            'damage_notes'             => $text('Damage notes'),
        ] + $declaration,
    ],

    'fire' => [
        'label'         => 'Fire',
        'event_field'   => 'incident_datetime',
        'server_fields' => ['policy_no', 'renewal_date'],
        'notes'         => 'policy_no and renewal_date come from the policy. Do not send them.',
        'fields'        => [
            'address'              => $str('Address'),
            'nature_of_business'   => $str('Nature of business', true),
            'claimant_ghana_card'  => $str('Ghana Card number', false, 30),
            'claimant_tin'         => $str('TIN', false, 30),
            'incident_datetime'    => $field('Date and time of incident', 'datetime', ['date', 'before_or_equal:now'], true),
            'exact_location'       => $str('Exact location', true),
            'incident_description' => $text('Description of the incident', true),
            'damage_nature'        => $text('Nature of damage / loss to property', true),
            'property_items'       => $list('Property lost or damaged', $property),
            'injured_persons'      => $text('Injured persons (free text)'),
            'police_reported'      => $field('Reported to the police', 'enum', ['in:yes,no'], true, ['options' => ['yes', 'no']]),
            'police_evidence'      => $when($text('Police report details'), 'police_reported', 'yes'),
            'additional_info'      => $text('Additional information'),
        ] + $declaration,
    ],

    // Vanguard's current form is travel-oriented but is used for every General Accident product,
    // so almost everything is optional until Vanguard supplies product-specific forms.
    'general_accident' => [
        'label'         => 'General Accident',
        'event_field'   => 'illness_date',
        'server_fields' => [],
        'notes'         => 'One form for all General Accident products. Travel and medical fields are optional and apply only where relevant.',
        'fields'        => [
            'agent_broker'               => $str('Agent / broker'),
            'departure_date'             => $date('Departure date'),
            'return_date'                => $date('Return date'),
            'surname'                    => $str('Surname', true),
            'firstname'                  => $str('First name', true),
            'insured_age'                => $int('Age'),
            'postal_address'             => $str('Postal address'),
            'postal_code'                => $str('Postal code', false, 30),
            'physical_address'           => $str('Physical address'),
            'address_code'               => $str('Address code', false, 30),
            'business'                   => $str('Business phone', false, 30),
            'fax'                        => $str('Fax', false, 30),
            'res_cell'                   => $str('Residence / cell phone', false, 30),
            'email'                      => $field('Email (defaults to the customer\'s)', 'string', ['email', 'max:255']),
            'claimant_ghana_card'        => $str('Ghana Card number', false, 30),
            'claimant_tin'               => $str('TIN', false, 30),
            'contacted_aafiya'           => $yn('Contacted Aafiya'),
            'contacted_aafiya_details'   => $when($text('Aafiya contact details'), 'contacted_aafiya', 'yes'),
            'police_report'              => $yn('Police report made'),
            'police_report_details'      => $when($text('Police report details'), 'police_report', 'yes'),
            'illness_date'               => $date('Date of illness, injury or loss', true, ['before_or_equal:today']),
            'place_of_illness'           => $str('Place of illness / loss'),
            'cause_of_illness'           => $str('Cause'),
            'diagnosis_sec1'             => $str('Diagnosis'),
            'doctor_fullname_sec1'       => $str('Doctor full name'),
            'doctor_telephone'           => $str('Doctor telephone', false, 30),
            'hospital_name'              => $str('Hospital name'),
            'total_amount_claimed'       => $num('Total amount claimed'),
            'currency'                   => $str('Currency (e.g. GHS)', false, 10),
            'treatment_received'         => $yn('Treatment received'),
            'treatment_received_details' => $when($text('Treatment details'), 'treatment_received', 'yes'),
            'submitted_accounts_yn'      => $yn('Accounts submitted'),
            'claim_subject_name'         => $str('Name of the person the claim is for'),
            'dob_claim'                  => $date('Date of birth'),
            'illness_date_sec2'          => $date('Date of illness / injury (section 2)'),
            'doctor_fullname_sec2'       => $str('Doctor full name (section 2)'),
            'diagnosis_sec2'             => $str('Diagnosis (section 2)'),
            'previous_injury'            => $yn('Previous injury or illness'),
            'previous_injury_details'    => $when($text('Previous injury details'), 'previous_injury', 'yes'),
            'account_holder_name'        => $str('Account holder name'),
            'account_number'             => $str('Account number', false, 50),
            'bank_name'                  => $str('Bank name'),
            'account_type'               => $str('Account type', false, 50),
            'branch_name'                => $str('Branch name'),
            'branch_code'                => $str('Branch code', false, 30),
            'general_section'            => $text('Description of the claim', true),
        ] + $declaration,
    ],

    'documents' => [
        'accepted'    => ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx', 'csv'],
        'max_size_kb' => 5120,
        'max_files'   => 10,
    ],
];
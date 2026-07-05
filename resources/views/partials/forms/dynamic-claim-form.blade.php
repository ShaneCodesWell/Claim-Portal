@php
    // $template  = App\Models\FormTemplate (has ->schema array, ->product_type)
    // $values    = flat prefill + existing-claim values, same shape as before
    // $context   = 'customer' | 'staff'
    // $isEdit    = bool
    // $claim     = Claim|null
    $isStaff = ($context ?? 'customer') === 'staff';
    $isEdit = !is_null($claim ?? null);
    $productLabel = ucfirst(str_replace('_', ' ', $template->product_type));
@endphp

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    {{-- Header — identical across products, not schema-driven --}}
    <div class="px-4 sm:px-6 md:px-8 pt-6 pb-0 bg-white">
        <div class="grid grid-cols-1 md:grid-cols-[160px_1fr_auto] items-center md:items-start gap-4 md:gap-6">
            <img src="{{ asset('images/Vanguard.png') }}" alt="Vanguard Assurance Logo" class="w-36 h-12 object-contain">
            <div class="text-center pt-1">
                <p class="text-[15px] font-bold text-gray-800 tracking-wide mb-2 border-b border-b-gray-300 pb-2">
                    Vanguard Assurance Company Ltd
                </p>
            </div>
            <div class="text-center md:text-right text-[11px] text-gray-500 leading-relaxed pt-1">
                <p>claimsdepartment@vanguardassurance.com</p>
                <p>030 266 6485 / 6486 / 6487</p>
            </div>
        </div>
        <div class="border-t border-gray-200 mt-5"></div>
    </div>

    <div class="bg-[#0b529d] px-4 sm:px-6 md:px-8 py-2.5 text-center">
        <p class="text-[13px] font-medium tracking-widest uppercase text-white">
            {{ $productLabel }} Claim Form{{ $isEdit ? ' — Edit' : '' }}
        </p>
    </div>

    <div class="py-4 px-4 sm:px-6 md:px-8 lg:px-12">

        <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6 rounded-lg">
            <p class="text-xs text-gray-700 leading-relaxed">
                Please note, it is necessary that great care should be taken in completing this form and the
                information given therein should be strictly accurate, whether it is in your favor or otherwise.
            </p>
        </div>

        {{-- ── Schema-driven sections ── --}}
        <x-dynamic-form
            :schema="$template->schema"
            :values="$values"
            :context="$context"
            :is-edit="$isEdit"
            form-id="claimForm"
            :action="$action"
            :method="$method"
        >
            {{-- ── Fixed sections: same for every product, not admin-editable ── --}}

            <input type="hidden" name="form_template_id" value="{{ $template->id }}">
            <input type="hidden" name="claim_type" value="{{ $template->product_type }}">
            <input type="hidden" name="policy_id" value="{{ $policy->external_policy_id ?? $policy->id }}">

            <section class="mb-8">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Supporting Documents</h3>
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center" id="dropzone">
                    <p class="text-gray-600">Drag & drop files here or <span class="text-blue-600 font-medium">browse</span></p>
                    <input type="file" id="documentUpload" multiple class="hidden">
                </div>
                <div id="imagePreviewContainer" class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3"></div>
            </section>

            @if ($isStaff)
                <section class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Edit Note</h3>
                    <input type="text" name="note" placeholder="Reason for edit"
                        class="w-full px-3 py-2 border border-indigo-200 rounded-lg bg-white">
                </section>
            @else
                <section class="mb-8">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b border-gray-200 pb-2">Declaration</h3>
                    <label class="flex items-start cursor-pointer mb-4">
                        <input type="checkbox" name="declaration_agreement" required class="mt-1 mr-3">
                        <span class="text-xs text-gray-700">I confirm all information provided is true and accurate. <span class="text-red-500">*</span></span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date of Declaration <span class="text-red-500">*</span></label>
                            <input type="date" name="form_data[declaration_date]" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Digital Signature <span class="text-red-500">*</span></label>
                            <input type="text" name="form_data[digital_signature]" class="w-full px-3 py-2 border-2 border-gray-300 rounded-lg">
                        </div>
                    </div>
                </section>
            @endif

            <div class="mt-8 pt-4 border-t border-gray-200">
                <button type="submit"
                    class="px-6 py-2 {{ $isStaff ? 'bg-indigo-600 hover:bg-indigo-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white font-medium rounded-lg transition">
                    {{ $isEdit ? 'Save Changes' : 'Submit Claim' }}
                </button>
            </div>
        </x-dynamic-form>
    </div>
</div>

<script src="{{ asset('js/dynamic-form.js') }}"></script>
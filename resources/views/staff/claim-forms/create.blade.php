<x-layouts.staff>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        {{-- Page Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('claim-form') }}" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
                        <i class="fas fa-plus-circle text-indigo-500 text-2xl"></i>
                        Create New Form Template
                    </h2>
                </div>
                <p class="text-gray-500 text-sm mt-1">
                    Define sections, fields, and conditional rules for a claim type.
                </p>
            </div>
            <div class="flex gap-3">
                <button onclick="saveDraft()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    Save Draft
                </button>
                <button onclick="publishTemplate()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium shadow-sm hover:bg-indigo-700 transition">
                    Publish Template
                </button>
            </div>
        </div>

        {{-- Basic Information --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-indigo-500"></i> Basic Information
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Template Name *</label>
                    <input type="text" id="tmpl-name" placeholder="e.g., Motor Claim Form"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Version</label>
                    <input type="text" value="1.0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50 outline-none">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description (internal use)</label>
                    <textarea rows="2" placeholder="What product/use case is this form for?"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- Form Structure Builder --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-layer-group text-indigo-500"></i> Form Structure
                </h3>
                <button onclick="addSection()"
                    class="text-indigo-600 text-sm flex items-center gap-1 border border-indigo-200 px-3 py-1.5 rounded-lg hover:bg-indigo-50 transition">
                    <i class="fas fa-plus-circle"></i> Add Section
                </button>
            </div>

            <div id="sections-container" class="space-y-4"></div>

            <button onclick="addSection()"
                class="mt-4 w-full border-2 border-dashed border-gray-300 rounded-lg py-3 px-4 text-sm text-gray-500 hover:border-indigo-300 hover:text-indigo-500 transition flex items-center justify-center gap-2">
                <i class="fas fa-plus-circle"></i> Add New Section
            </button>
        </div>

        {{-- File Attachments --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-paperclip text-indigo-500"></i> File Attachments
            </h3>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center bg-gray-50">
                <i class="fas fa-cloud-upload-alt text-3xl text-indigo-400 mb-2"></i>
                <p class="text-sm text-gray-600 font-medium">Claimants can upload supporting documents here</p>
                <p class="text-xs text-gray-400 mt-1">Accepted: PDF, JPG, PNG, DOCX &mdash; max 10MB per file</p>
                <div class="flex flex-wrap gap-2 justify-center mt-3">
                    <span class="text-xs bg-gray-100 border border-gray-300 text-gray-600 px-2 py-1 rounded-full">Police
                        report</span>
                    <span class="text-xs bg-gray-100 border border-gray-300 text-gray-600 px-2 py-1 rounded-full">Photos
                        of damage</span>
                    <span
                        class="text-xs bg-gray-100 border border-gray-300 text-gray-600 px-2 py-1 rounded-full">Medical
                        reports</span>
                    <span
                        class="text-xs bg-gray-100 border border-gray-300 text-gray-600 px-2 py-1 rounded-full">Receipts</span>
                </div>
                <p class="text-xs text-gray-400 mt-3">This section is included automatically in all published forms.</p>
            </div>
        </div>

        {{-- Declaration --}}
        <div class="bg-indigo-50 border border-indigo-200 rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-indigo-800 flex items-center gap-2">
                    <i class="fas fa-file-signature text-indigo-500"></i> Declaration
                </h3>
                <span
                    class="text-xs bg-indigo-100 border border-indigo-300 text-indigo-700 px-2 py-1 rounded-full font-medium">
                    Always included
                </span>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Declaration Text</label>
                <textarea id="declaration-text" rows="5"
                    class="w-full border border-indigo-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:ring-indigo-500 focus:border-indigo-500 outline-none resize-none leading-relaxed">I declare that the above statement is true in all respects to the best of my knowledge and belief and I hereby leave in the hands of the Company in accordance with the conditions of the Policy the conduct of all claims and litigation arising out of this accident and to which the Policy applies, to deal with, to prosecute and/or settle as they deem fit without further reference to me; and I undertake to give all such information and assistance as the Company may require.</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Note / Disclaimer</label>
                <input type="text" id="declaration-note"
                    value="The Company does not admit liability by the issue of this form."
                    class="w-full border border-indigo-200 rounded-lg px-3 py-2 text-sm text-gray-700 bg-white focus:ring-indigo-500 focus:border-indigo-500 outline-none">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div
                    class="bg-white border border-indigo-100 rounded-lg p-3 text-xs text-gray-500 flex items-center gap-2">
                    <i class="fas fa-check-square text-indigo-400"></i>
                    Agreement checkbox &mdash; <span class="text-red-500 font-medium">required</span>
                </div>
                <div
                    class="bg-white border border-indigo-100 rounded-lg p-3 text-xs text-gray-500 flex items-center gap-2">
                    <i class="fas fa-calendar-alt text-indigo-400"></i>
                    Date of declaration &mdash; <span class="text-red-500 font-medium">required</span>
                </div>
                <div
                    class="bg-white border border-indigo-100 rounded-lg p-3 text-xs text-gray-500 flex items-center gap-2">
                    <i class="fas fa-pen-nib text-indigo-400"></i>
                    Digital signature &mdash; <span class="text-red-500 font-medium">required</span>
                </div>
            </div>
            <p class="text-xs text-indigo-500 mt-3 flex items-center gap-1">
                <i class="fas fa-info-circle"></i>
                The agreement checkbox, date, and digital signature fields are always appended and cannot be removed.
            </p>
        </div>

    </div>

    {{-- ============================================================
         TEMPLATES (hidden, cloned via JS)
    ============================================================ --}}

    {{-- Section --}}
    <template id="tpl-section">
        <div class="section-block border border-gray-200 rounded-xl p-4 bg-gray-50/40" data-section-id="">
            <div class="flex items-center gap-2 mb-3">
                <span
                    class="section-badge text-xs bg-indigo-100 border border-indigo-200 text-indigo-700 px-2 py-0.5 rounded font-medium whitespace-nowrap"></span>
                <input type="text" placeholder="Section title or question..."
                    class="section-title flex-1 border border-gray-300 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-800 bg-white focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                <button class="btn-remove-section text-gray-400 hover:text-red-500 transition text-sm px-1">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>

            <div class="fields-container space-y-3 mb-3 ml-2"></div>

            <div
                class="add-field-row flex flex-wrap items-center gap-2 pt-2 border-t border-dashed border-gray-300 ml-2">
                <span class="text-xs text-gray-400 mr-1">Add field:</span>
                <button data-type="text"
                    class="btn-add-field text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded-md hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition"><i
                        class="fas fa-font mr-1"></i>Input</button>
                <button data-type="date"
                    class="btn-add-field text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded-md hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition"><i
                        class="fas fa-calendar mr-1"></i>Date</button>
                <button data-type="textarea"
                    class="btn-add-field text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded-md hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition"><i
                        class="fas fa-align-left mr-1"></i>Description</button>
                <button data-type="radio"
                    class="btn-add-field text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded-md hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition"><i
                        class="fas fa-dot-circle mr-1"></i>Radio (Yes/No)</button>
                <button data-type="number"
                    class="btn-add-field text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded-md hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition"><i
                        class="fas fa-hashtag mr-1"></i>Number</button>
                <button data-type="select"
                    class="btn-add-field text-xs border border-gray-300 text-gray-600 px-2 py-1 rounded-md hover:border-indigo-300 hover:text-indigo-600 hover:bg-indigo-50 transition"><i
                        class="fas fa-chevron-down mr-1"></i>Select</button>
                <button data-type="repeatable-group"
                    class="btn-add-field text-xs border border-teal-300 text-teal-700 px-2 py-1 rounded-md hover:bg-teal-50 transition"><i
                        class="fas fa-users mr-1"></i>Repeatable Group</button>
                <button data-type="repeatable-table"
                    class="btn-add-field text-xs border border-orange-300 text-orange-700 px-2 py-1 rounded-md hover:bg-orange-50 transition"><i
                        class="fas fa-table mr-1"></i>Repeatable Table</button>
            </div>
        </div>
    </template>

    {{-- Simple field (text / date / textarea / number / select) --}}
    <template id="tpl-field-simple">
        <div class="field-row flex items-start gap-2 p-2.5 bg-white border border-gray-200 rounded-lg"
            data-field-id="">
            <div class="flex-1 space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="text" placeholder="Field label..."
                        class="field-label-input flex-1 min-w-0 border border-gray-300 rounded-md px-2 py-1 text-sm text-gray-800 bg-white focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    <span
                        class="field-type-tag text-xs bg-indigo-50 border border-indigo-200 text-indigo-600 px-2 py-0.5 rounded font-medium"></span>
                    <label class="flex items-center gap-1 text-xs text-gray-500 whitespace-nowrap">
                        <input type="checkbox" class="field-required rounded border-gray-300 text-indigo-600">
                        Required
                    </label>
                </div>
                <div class="field-options-row hidden">
                    <input type="text" placeholder="Options: comma separated e.g. Collision, Theft, Fire"
                        class="w-full border border-gray-300 rounded-md px-2 py-1 text-xs text-gray-700 bg-white focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                </div>
            </div>
            <button class="btn-remove-field text-gray-300 hover:text-red-500 transition text-sm mt-0.5 flex-shrink-0">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </template>

    {{-- Radio (Yes/No) --}}
    <template id="tpl-field-radio">
        <div class="field-row-radio border border-gray-200 rounded-lg bg-white p-3" data-field-id="">
            <div class="flex items-center gap-2 mb-2">
                <div class="flex-1 flex items-center gap-2 flex-wrap">
                    <input type="text" placeholder="Question label..."
                        class="field-label-input flex-1 min-w-0 border border-gray-300 rounded-md px-2 py-1 text-sm text-gray-800 bg-white focus:ring-indigo-500 focus:border-indigo-500 outline-none">
                    <span
                        class="text-xs bg-indigo-50 border border-indigo-200 text-indigo-600 px-2 py-0.5 rounded font-medium">Radio
                        Yes/No</span>
                    <label class="flex items-center gap-1 text-xs text-gray-500 whitespace-nowrap">
                        <input type="checkbox" class="field-required rounded border-gray-300 text-indigo-600" checked>
                        Required
                    </label>
                </div>
                <button class="btn-remove-field text-gray-300 hover:text-red-500 transition text-sm flex-shrink-0">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            <div class="border-l-2 border-indigo-300 pl-3 ml-1 mt-2">
                <p class="text-xs text-indigo-600 font-medium mb-2">
                    <i class="fas fa-code-branch mr-1"></i>
                    Conditional &mdash; if <strong>"Yes"</strong> is selected, show:
                </p>
                <div class="cond-fields-container space-y-2 mb-2"></div>
                <div class="flex flex-wrap gap-2">
                    <span class="text-xs text-indigo-400 mr-1">Add if Yes:</span>
                    <button data-type="text"
                        class="btn-add-cond-field text-xs border border-indigo-200 text-indigo-600 px-2 py-0.5 rounded-md hover:bg-indigo-50 transition"><i
                            class="fas fa-font mr-1"></i>Input</button>
                    <button data-type="date"
                        class="btn-add-cond-field text-xs border border-indigo-200 text-indigo-600 px-2 py-0.5 rounded-md hover:bg-indigo-50 transition"><i
                            class="fas fa-calendar mr-1"></i>Date</button>
                    <button data-type="textarea"
                        class="btn-add-cond-field text-xs border border-indigo-200 text-indigo-600 px-2 py-0.5 rounded-md hover:bg-indigo-50 transition"><i
                            class="fas fa-align-left mr-1"></i>Description</button>
                    <button data-type="number"
                        class="btn-add-cond-field text-xs border border-indigo-200 text-indigo-600 px-2 py-0.5 rounded-md hover:bg-indigo-50 transition"><i
                            class="fas fa-hashtag mr-1"></i>Number</button>
                </div>
            </div>
        </div>
    </template>

    {{-- Conditional child field --}}
    <template id="tpl-cond-field">
        <div class="cond-field flex items-center gap-2 p-2 bg-indigo-50 border border-indigo-200 rounded-lg">
            <div class="flex-1 flex items-center gap-2 flex-wrap">
                <input type="text" placeholder="Field label..."
                    class="flex-1 min-w-0 border border-indigo-200 rounded-md px-2 py-1 text-xs text-gray-800 bg-white focus:ring-indigo-400 focus:border-indigo-400 outline-none">
                <span
                    class="cond-field-type-tag text-xs bg-indigo-100 border border-indigo-200 text-indigo-700 px-2 py-0.5 rounded font-medium"></span>
                <label class="flex items-center gap-1 text-xs text-indigo-500 whitespace-nowrap">
                    <input type="checkbox" class="rounded border-indigo-300 text-indigo-600" checked> Required
                </label>
            </div>
            <span class="text-xs text-indigo-400 italic whitespace-nowrap hidden sm:block">Shown if "Yes"</span>
            <button class="btn-remove-cond-field text-indigo-300 hover:text-red-500 transition text-xs flex-shrink-0">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </template>

    {{-- ============================================================
         REPEATABLE GROUP
         Card-style repeatable block (e.g. Injured Persons).
         Builder: define sub-fields per card.
         Runtime: "Add another" clones the card with those sub-fields.
    ============================================================ --}}
    <template id="tpl-field-repeatable-group">
        <div class="field-row-rgroup border border-teal-200 rounded-xl bg-white p-4" data-field-id="">
            <div class="flex items-start justify-between gap-2 mb-3">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <input type="text" placeholder="Group label (e.g. Injured Person)"
                            class="rgroup-label flex-1 min-w-0 border border-gray-300 rounded-md px-2 py-1.5 text-sm font-medium text-gray-800 bg-white focus:ring-teal-500 focus:border-teal-500 outline-none">
                        <span
                            class="text-xs bg-teal-50 border border-teal-200 text-teal-700 px-2 py-0.5 rounded font-medium whitespace-nowrap">
                            <i class="fas fa-users mr-1"></i>Repeatable Group
                        </span>
                    </div>
                    <input type="text" placeholder="Button label on the form (e.g. + Add Injured Person)"
                        class="rgroup-btn-label w-full border border-gray-300 rounded-md px-2 py-1 text-xs text-gray-700 bg-white focus:ring-teal-400 focus:border-teal-400 outline-none">
                </div>
                <button
                    class="btn-remove-field text-gray-300 hover:text-red-500 transition text-sm flex-shrink-0 mt-1">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="border border-teal-100 rounded-lg bg-teal-50/30 p-3">
                <p class="text-xs font-medium text-teal-700 mb-2 flex items-center gap-1">
                    <i class="fas fa-list-ul"></i> Sub-fields inside each group card:
                </p>
                <div class="rgroup-subfields space-y-2 mb-3"></div>
                <div class="flex flex-wrap gap-2 items-center">
                    <span class="text-xs text-teal-500 mr-1">Add sub-field:</span>
                    <button data-type="text"
                        class="btn-add-subfield text-xs border border-teal-200 text-teal-700 px-2 py-0.5 rounded-md hover:bg-teal-50 transition"><i
                            class="fas fa-font mr-1"></i>Input</button>
                    <button data-type="date"
                        class="btn-add-subfield text-xs border border-teal-200 text-teal-700 px-2 py-0.5 rounded-md hover:bg-teal-50 transition"><i
                            class="fas fa-calendar mr-1"></i>Date</button>
                    <button data-type="textarea"
                        class="btn-add-subfield text-xs border border-teal-200 text-teal-700 px-2 py-0.5 rounded-md hover:bg-teal-50 transition"><i
                            class="fas fa-align-left mr-1"></i>Description</button>
                    <button data-type="number"
                        class="btn-add-subfield text-xs border border-teal-200 text-teal-700 px-2 py-0.5 rounded-md hover:bg-teal-50 transition"><i
                            class="fas fa-hashtag mr-1"></i>Number</button>
                    <button data-type="select"
                        class="btn-add-subfield text-xs border border-teal-200 text-teal-700 px-2 py-0.5 rounded-md hover:bg-teal-50 transition"><i
                            class="fas fa-chevron-down mr-1"></i>Select</button>
                </div>
            </div>

            <p class="text-xs text-gray-400 mt-2 flex items-center gap-1">
                <i class="fas fa-info-circle text-teal-400"></i>
                At runtime, claimants can add multiple instances of this group.
            </p>
        </div>
    </template>

    {{-- Sub-field row inside a repeatable group --}}
    <template id="tpl-subfield-row">
        <div class="subfield-row flex items-start gap-2 p-2 bg-white border border-teal-200 rounded-lg">
            <div class="flex-1 space-y-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <input type="text" placeholder="Sub-field label..."
                        class="flex-1 min-w-0 border border-gray-300 rounded-md px-2 py-1 text-xs text-gray-800 bg-white focus:ring-teal-400 focus:border-teal-400 outline-none">
                    <span
                        class="subfield-type-tag text-xs bg-teal-50 border border-teal-200 text-teal-700 px-2 py-0.5 rounded font-medium"></span>
                    <label class="flex items-center gap-1 text-xs text-gray-500 whitespace-nowrap">
                        <input type="checkbox" class="rounded border-gray-300 text-teal-600" checked> Required
                    </label>
                </div>
                <div class="subfield-options-row hidden">
                    <input type="text" placeholder="Options: comma separated"
                        class="w-full border border-gray-300 rounded-md px-2 py-1 text-xs text-gray-700 bg-white focus:ring-teal-400 focus:border-teal-400 outline-none">
                </div>
            </div>
            <button class="btn-remove-subfield text-gray-300 hover:text-red-500 transition text-xs flex-shrink-0 mt-1">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </template>

    {{-- ============================================================
         REPEATABLE TABLE
         Row-based repeatable table (e.g. Particulars of Claim).
         Builder: define columns + types.
         Runtime: "Add Row" appends a new <tr>. Totals auto-appear
         when Number or Calculated columns exist.
    ============================================================ --}}
    <template id="tpl-field-repeatable-table">
        <div class="field-row-rtable border border-orange-200 rounded-xl bg-white p-4" data-field-id="">
            <div class="flex items-start justify-between gap-2 mb-3">
                <div class="flex-1 space-y-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        <input type="text" placeholder="Table label (e.g. Particulars of Claim)"
                            class="rtable-label flex-1 min-w-0 border border-gray-300 rounded-md px-2 py-1.5 text-sm font-medium text-gray-800 bg-white focus:ring-orange-500 focus:border-orange-500 outline-none">
                        <span
                            class="text-xs bg-orange-50 border border-orange-200 text-orange-700 px-2 py-0.5 rounded font-medium whitespace-nowrap">
                            <i class="fas fa-table mr-1"></i>Repeatable Table
                        </span>
                    </div>
                    <input type="text" placeholder="Add-row button label on the form (e.g. + Add Property)"
                        class="rtable-btn-label w-full border border-gray-300 rounded-md px-2 py-1 text-xs text-gray-700 bg-white focus:ring-orange-400 focus:border-orange-400 outline-none">
                </div>
                <button
                    class="btn-remove-field text-gray-300 hover:text-red-500 transition text-sm flex-shrink-0 mt-1">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            {{-- Column builder --}}
            <div class="border border-orange-100 rounded-lg bg-orange-50/30 p-3 mb-3">
                <p class="text-xs font-medium text-orange-700 mb-2 flex items-center gap-1">
                    <i class="fas fa-columns"></i> Define table columns:
                </p>

                <div class="rtable-columns space-y-2 mb-3"></div>

                <div class="flex items-center gap-2 flex-wrap">
                    <input type="text" placeholder="Column header (e.g. Description)"
                        class="rtable-col-input flex-1 min-w-[140px] border border-gray-300 rounded-md px-2 py-1 text-xs text-gray-700 bg-white focus:ring-orange-400 focus:border-orange-400 outline-none">
                    <select
                        class="rtable-col-type border border-gray-300 rounded-md px-2 py-1 text-xs text-gray-700 bg-white focus:ring-orange-400 focus:border-orange-400 outline-none">
                        <option value="text">Text</option>
                        <option value="number">Number</option>
                        <option value="date">Date</option>
                        <option value="calculated">Calculated</option>
                    </select>
                    <button
                        class="btn-add-col text-xs bg-orange-100 border border-orange-300 text-orange-700 px-3 py-1 rounded-md hover:bg-orange-200 transition flex items-center gap-1">
                        <i class="fas fa-plus"></i> Add Column
                    </button>
                </div>

                <p class="text-xs text-orange-500 mt-2 flex items-center gap-1">
                    <i class="fas fa-calculator"></i>
                    Mark a column as <strong>Calculated</strong> to flag it for a custom formula in the published
                    template (e.g. Price &minus; Depreciation).
                </p>
            </div>

            {{-- Live column preview --}}
            <div>
                <p class="text-xs font-medium text-gray-500 mb-1 flex items-center gap-1">
                    <i class="fas fa-eye text-orange-400"></i> Column preview:
                </p>
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr class="rtable-preview-head"></tr>
                        </thead>
                        <tbody>
                            <tr class="rtable-empty-row border-b border-gray-100">
                                <td colspan="99" class="px-3 py-2 text-gray-400 italic">No columns yet. Add columns
                                    above.</td>
                            </tr>
                        </tbody>
                        <tfoot class="rtable-preview-foot hidden bg-gray-50 border-t border-gray-200">
                            <tr>
                                <td colspan="99" class="px-3 py-2 text-right text-xs font-semibold text-gray-600">
                                    Total: <span class="font-bold text-gray-900">0.00</span>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <p class="text-xs text-gray-400 mt-1 flex items-center gap-1">
                    <i class="fas fa-info-circle text-orange-400"></i>
                    At runtime, claimants can add / remove rows. A totals row appears automatically when a
                    <strong>Number</strong> or <strong>Calculated</strong> column is present.
                </p>
            </div>
        </div>
    </template>

    {{-- Column pill inside repeatable table builder --}}
    <template id="tpl-col-pill">
        <div class="col-pill flex items-center justify-between gap-2 p-2 bg-white border border-orange-200 rounded-lg">
            <div class="flex items-center gap-2">
                <span class="text-gray-300 text-xs"><i class="fas fa-grip-vertical"></i></span>
                <span class="col-pill-name text-xs font-medium text-gray-700"></span>
                <span
                    class="col-pill-type text-xs bg-orange-50 border border-orange-200 text-orange-600 px-1.5 py-0.5 rounded"></span>
            </div>
            <button class="btn-remove-col text-gray-300 hover:text-red-500 transition text-xs">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </template>

    {{-- ============================================================
         JAVASCRIPT
    ============================================================ --}}
    <script>
        const fieldTypeLabels = {
            text: 'Input',
            date: 'Date',
            textarea: 'Description',
            number: 'Number',
            select: 'Select',
            radio: 'Radio Yes/No',
            'repeatable-group': 'Repeatable Group',
            'repeatable-table': 'Repeatable Table',
            calculated: 'Calculated',
        };

        let sectionCount = 0;

        function uid() {
            return Math.random().toString(36).slice(2, 8);
        }

        // ── Sections ───────────────────────────────────────────────
        function addSection() {
            sectionCount++;
            const sid = uid();
            const tpl = document.getElementById('tpl-section');
            const clone = tpl.content.cloneNode(true);
            const block = clone.querySelector('.section-block');
            block.dataset.sectionId = sid;
            block.querySelector('.section-badge').textContent = 'Section ' + sectionCount;

            block.querySelector('.btn-remove-section').addEventListener('click', () => {
                block.remove();
                renumberSections();
            });

            block.querySelectorAll('.btn-add-field').forEach(btn => {
                btn.addEventListener('click', () => {
                    addField(block.querySelector('.fields-container'), btn.dataset.type);
                });
            });

            document.getElementById('sections-container').appendChild(clone);
            renumberSections();
        }

        function renumberSections() {
            document.querySelectorAll('.section-block').forEach((s, i) => {
                const badge = s.querySelector('.section-badge');
                if (badge) badge.textContent = 'Section ' + (i + 1);
            });
        }

        // ── Field dispatcher ───────────────────────────────────────
        function addField(container, type) {
            if (type === 'radio') return addRadioField(container);
            if (type === 'repeatable-group') return addRepeatableGroup(container);
            if (type === 'repeatable-table') return addRepeatableTable(container);
            addSimpleField(container, type);
        }

        // ── Simple field ───────────────────────────────────────────
        function addSimpleField(container, type) {
            const tpl = document.getElementById('tpl-field-simple');
            const clone = tpl.content.cloneNode(true);
            const row = clone.querySelector('.field-row');
            row.dataset.fieldId = uid();
            row.querySelector('.field-type-tag').textContent = fieldTypeLabels[type];
            if (type === 'select') row.querySelector('.field-options-row').classList.remove('hidden');
            row.querySelector('.btn-remove-field').addEventListener('click', () => row.remove());
            container.appendChild(clone);
        }

        // ── Radio field ────────────────────────────────────────────
        function addRadioField(container) {
            const tpl = document.getElementById('tpl-field-radio');
            const clone = tpl.content.cloneNode(true);
            const block = clone.querySelector('.field-row-radio');
            block.dataset.fieldId = uid();
            block.querySelector('.btn-remove-field').addEventListener('click', () => block.remove());
            block.querySelectorAll('.btn-add-cond-field').forEach(btn => {
                btn.addEventListener('click', () => {
                    addConditionalField(block.querySelector('.cond-fields-container'), btn.dataset.type);
                });
            });
            container.appendChild(clone);
        }

        // ── Conditional child field ────────────────────────────────
        function addConditionalField(container, type) {
            const tpl = document.getElementById('tpl-cond-field');
            const clone = tpl.content.cloneNode(true);
            const row = clone.querySelector('.cond-field');
            row.querySelector('.cond-field-type-tag').textContent = fieldTypeLabels[type];
            row.querySelector('.btn-remove-cond-field').addEventListener('click', () => row.remove());
            container.appendChild(clone);
        }

        // ── Repeatable Group ───────────────────────────────────────
        function addRepeatableGroup(container) {
            const tpl = document.getElementById('tpl-field-repeatable-group');
            const clone = tpl.content.cloneNode(true);
            const block = clone.querySelector('.field-row-rgroup');
            block.dataset.fieldId = uid();

            block.querySelector('.btn-remove-field').addEventListener('click', () => block.remove());

            block.querySelectorAll('.btn-add-subfield').forEach(btn => {
                btn.addEventListener('click', () => {
                    addSubfield(block.querySelector('.rgroup-subfields'), btn.dataset.type);
                });
            });

            container.appendChild(clone);
        }

        function addSubfield(container, type) {
            const tpl = document.getElementById('tpl-subfield-row');
            const clone = tpl.content.cloneNode(true);
            const row = clone.querySelector('.subfield-row');
            row.querySelector('.subfield-type-tag').textContent = fieldTypeLabels[type];
            if (type === 'select') row.querySelector('.subfield-options-row').classList.remove('hidden');
            row.querySelector('.btn-remove-subfield').addEventListener('click', () => row.remove());
            container.appendChild(clone);
        }

        // ── Repeatable Table ───────────────────────────────────────
        function addRepeatableTable(container) {
            const tpl = document.getElementById('tpl-field-repeatable-table');
            const clone = tpl.content.cloneNode(true);
            const block = clone.querySelector('.field-row-rtable');
            block.dataset.fieldId = uid();

            block.querySelector('.btn-remove-field').addEventListener('click', () => block.remove());

            // Add column on button click
            block.querySelector('.btn-add-col').addEventListener('click', () => {
                const nameInput = block.querySelector('.rtable-col-input');
                const typeSelect = block.querySelector('.rtable-col-type');
                const name = nameInput.value.trim();
                if (!name) {
                    nameInput.focus();
                    return;
                }
                addTableColumn(block, name, typeSelect.value);
                nameInput.value = '';
                typeSelect.value = 'text';
                nameInput.focus();
            });

            // Also add column on Enter key in name input
            block.querySelector('.rtable-col-input').addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    block.querySelector('.btn-add-col').click();
                }
            });

            container.appendChild(clone);
        }

        function addTableColumn(tableBlock, colName, colType) {
            // Append pill to column list
            const pillTpl = document.getElementById('tpl-col-pill');
            const pillClone = pillTpl.content.cloneNode(true);
            const pill = pillClone.querySelector('.col-pill');
            pill.querySelector('.col-pill-name').textContent = colName;
            pill.querySelector('.col-pill-type').textContent = fieldTypeLabels[colType] || colType;

            pill.querySelector('.btn-remove-col').addEventListener('click', () => {
                pill.remove();
                refreshTablePreview(tableBlock);
            });

            tableBlock.querySelector('.rtable-columns').appendChild(pillClone);
            refreshTablePreview(tableBlock);
        }

        function refreshTablePreview(tableBlock) {
            const pills = tableBlock.querySelectorAll('.col-pill');
            const headRow = tableBlock.querySelector('.rtable-preview-head');
            const emptyRow = tableBlock.querySelector('.rtable-empty-row');
            const foot = tableBlock.querySelector('.rtable-preview-foot');

            headRow.innerHTML = '';

            if (!pills.length) {
                emptyRow.style.display = '';
                foot.classList.add('hidden');
                return;
            }

            emptyRow.style.display = 'none';
            let hasNumeric = false;

            pills.forEach(pill => {
                const name = pill.querySelector('.col-pill-name').textContent;
                const type = pill.querySelector('.col-pill-type').textContent;
                const th = document.createElement('th');
                th.className = 'px-3 py-2 font-semibold text-gray-600 whitespace-nowrap';
                th.textContent = name;
                if (type === 'Calculated') {
                    th.innerHTML += ' <span class="text-orange-400 text-xs font-normal">(calc)</span>';
                    hasNumeric = true;
                }
                if (type === 'Number') hasNumeric = true;
                headRow.appendChild(th);
            });

            // Action column placeholder
            const thAction = document.createElement('th');
            thAction.className = 'px-3 py-2 w-8';
            headRow.appendChild(thAction);

            // Show totals footer when numeric/calculated columns exist
            hasNumeric ? foot.classList.remove('hidden') : foot.classList.add('hidden');
        }

        // ── Save Draft ─────────────────────────────────────────────
        function saveDraft() {
            const name = document.getElementById('tmpl-name').value.trim();
            // Replace with your AJAX / form submission logic
            alert(name ? `Draft saved: "${name}"` : 'Draft saved (no template name set).');
        }

        // ── Publish Template ───────────────────────────────────────
        function publishTemplate() {
            const name = document.getElementById('tmpl-name').value.trim();
            if (!name) {
                alert('Please enter a template name before publishing.');
                return;
            }
            const sections = document.querySelectorAll('.section-block');
            if (!sections.length) {
                alert('Add at least one section before publishing.');
                return;
            }
            // Replace with your actual submission logic
            alert(`Template "${name}" published with ${sections.length} section(s).`);
        }
    </script>
</x-layouts.staff>

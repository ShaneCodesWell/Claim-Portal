<?php

namespace App\Http\Controllers;

use App\Models\FormTemplate;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class FormTemplateController extends Controller
{
    /**
     * Blank builder — creating a brand new template.
     */
    public function create()
    {
        return view('staff.claim-forms.create', ['formTemplate' => null]);
    }

    /**
     * Same builder view, but pre-populated from an existing template's schema.
     */
    public function edit(FormTemplate $formTemplate)
    {
        return view('staff.claim-forms.create', compact('formTemplate'));
    }

    /**
     * Read-only structural preview — no claim, no data, just the field layout.
     */
    public function preview(FormTemplate $formTemplate)
    {
        return view('staff.claim-forms.preview', compact('formTemplate'));
    }

    public function store(Request $request)
    {
        return $this->persist($request);
    }

    /**
     * "Update" deliberately creates a NEW version row rather than mutating
     * the existing one in place. Historical claims already stamped with the
     * old form_template_id keep rendering against the old schema — that's
     * the whole point of versioning. The route param is only used to look
     * up which product_type we're publishing a new version for.
     */
    public function update(Request $request, FormTemplate $formTemplate)
    {
        return $this->persist($request, $formTemplate->product_type);
    }

    protected function persist(Request $request, ?string $lockProductType = null)
    {
        $data = $request->validate([
            'product_type' => ['required', 'string'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'schema' => ['required', 'array'],
        ]);

        $productType = $lockProductType ?? $data['product_type'];
        $nextVersion = (int) FormTemplate::where('product_type', $productType)->max('version') + 1;

        $template = FormTemplate::create([
            'product_type' => $productType,
            'version' => $nextVersion,
            'status' => $data['status'],
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'schema' => $data['schema'],
            'created_by' => Auth::id(),
            'published_at' => $data['status'] === 'published' ? now() : null,
        ]);

        return response()->json([
            'id' => $template->id,
            'product_type' => $template->product_type,
            'version' => $template->version,
            'status' => $template->status,
        ]);
    }
}
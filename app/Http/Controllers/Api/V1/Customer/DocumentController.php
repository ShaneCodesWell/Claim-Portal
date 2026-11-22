<?php

namespace App\Http\Controllers\Api\V1\Customer;

use App\Http\Controllers\Api\V1\Concerns\ResolvesOwnedResources;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Dedoc\Scramble\Attributes\Group;

#[Group('Customer - Documents', weight: 13)]
class DocumentController extends Controller
{
    use ResolvesOwnedResources;

    public function show(Request $request, int $document)
    {
        $document = $this->documentFor($request, $document);
        
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        if (! $disk->exists($document->file_path)) {
            return ApiResponse::error('not_found', 'Document file not found.', 404);
        }

        return $disk->response(
            $document->file_path,
            $document->original_name,
            ['X-Content-Type-Options' => 'nosniff', 'Content-Type' => $document->mime_type],
            $request->boolean('download') ? 'attachment' : 'inline'
        );
    }
}

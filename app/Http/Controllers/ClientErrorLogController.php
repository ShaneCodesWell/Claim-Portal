<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ClientErrorLogController extends Controller
{
    /**
     * Receives a diagnosed client-side submission failure (from
     * claim-submit.js) and writes it into the normal Laravel log,
     * tagged so it's easy to grep separately from everything else.
     *
     * Example line this produces in storage/logs/laravel.log:
     *
     * [2026-08-29 14:02:11] production.WARNING: [CLIENT_ERROR] payload_too_large
     * {"reason":"payload_too_large","status":413,"user_id":42,"form_id":"motorForm",
     * "file_count":3,"total_file_size_mb":14.2,"url":"https://.../claims","body_snippet":null}
     */
    public function store(Request $request)
    {
        $reason = $request->input('reason', 'unknown');

        Log::warning('[CLIENT_ERROR] ' . $reason, [
            'reason'          => $reason,
            'status'          => $request->input('status'),
            'error_name'      => $request->input('errorName'),
            'error_message'   => $request->input('errorMessage'),
            'user_id'         => $request->user()?->id,
            'form_id'         => $request->input('formId'),
            'form_action'     => $request->input('action'),
            'file_count'      => $request->input('fileCount'),
            'total_file_size_mb' => $request->input('totalFileSizeMB'),
            'page_url'        => $request->input('pageUrl'),
            'server_message'  => $request->input('serverMessage'),
            'body_snippet'    => $request->input('bodySnippet'),
            'user_agent'      => $request->input('userAgent'),
            'online'          => $request->input('online'),
            'client_timestamp' => $request->input('timestamp'),
        ]);

        return response()->json(['received' => true]);
    }
}

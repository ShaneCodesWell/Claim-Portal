// ==================== CONFIG ====================

// Abort a submission if it hangs longer than this (large uploads on slow
// connections legitimately take a while — tune this up if needed).
const REQUEST_TIMEOUT_MS = 30000; // 30s

// Where diagnosed client-side errors get reported so they show up in your
// server logs instead of disappearing into the user's browser console.
// Set to null to disable server-side reporting entirely.
const CLIENT_ERROR_LOG_ENDPOINT = "/api/client-errors";

// ==================== CLAIM SUBMISSION ====================

async function submitClaimWithFiles(formId, formData, action = "/claims") {
    const submitBtn = document.querySelector(`#${formId} [type="submit"]`);
    const originalText = submitBtn?.innerHTML;

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...`;
    }

    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), REQUEST_TIMEOUT_MS);

    // Context attached to every log line so you can tell requests apart later
    const context = {
        formId,
        action,
        fileCount: countFiles(formData),
        totalFileSizeMB: totalFileSize(formData),
    };

    try {
        const response = await fetch(action, {
            method: "POST", // always POST — _method=PUT handles the override
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
                Accept: "application/json",
            },
            body: formData,
            signal: controller.signal,
        });

        clearTimeout(timeoutId);

        await handleClaimResponse(response, context, {
            onSuccess: (data) => {
                Swal.fire({
                    icon: "success",
                    title: "Claim Submitted!",
                    text: "Your claim has been received and is being processed.",
                    confirmButtonText: "View My Claims",
                    confirmButtonColor: "#2563eb",
                    allowOutsideClick: false,
                }).then(() => {
                    window.location.href = data.redirect;
                });
            },
        });
    } catch (error) {
        clearTimeout(timeoutId);
        handleClaimNetworkError(error, context);
    } finally {
        restoreSubmitButton(submitBtn, originalText);
    }
}

// Inspects the response, figures out what actually went wrong, logs it,
// and shows the user an appropriately specific message.
async function handleClaimResponse(response, context, { onSuccess }) {
    const contentType = response.headers.get("content-type") || "";
    const isJson = contentType.includes("application/json");

    let data = null;
    let rawText = null;

    if (isJson) {
        try {
            data = await response.json();
        } catch {
            rawText = "(server said JSON but body failed to parse)";
        }
    } else {
        // Laravel error pages, nginx 413s, gateway timeouts, etc. all come
        // back as HTML — this is the case that used to vanish silently.
        rawText = await response.text().catch(() => "(could not read response body)");
    }

    if (response.ok && data?.success) {
        onSuccess(data);
        return;
    }

    const diagnosis = diagnoseHttpFailure(response, data, rawText);
    logClientError(diagnosis, context);
    showClaimError(diagnosis.userMessage, data?.errors);
}

// Maps HTTP status + body into a specific reason and user-facing message.
function diagnoseHttpFailure(response, data, rawText) {
    const status = response.status;
    let reason = `http_${status}`;
    let userMessage = data?.message || `Something went wrong (error ${status}). Please try again.`;

    if (status === 413) {
        reason = "payload_too_large";
        userMessage =
            "Your upload is too large for the server to accept. Try removing a file or uploading smaller/fewer documents.";
    } else if (status === 419) {
        reason = "csrf_expired";
        userMessage = "Your session expired. Please refresh the page and try again.";
    } else if (status === 422) {
        reason = "validation_failed";
        userMessage = data?.message || "Please check the highlighted fields and try again.";
    } else if (status === 401 || status === 403) {
        reason = "auth_error";
        userMessage = "You're not authorized to do that. Please log in again.";
    } else if (status === 429) {
        reason = "rate_limited";
        userMessage = "Too many attempts — please wait a moment and try again.";
    } else if (status >= 500) {
        reason = "server_error";
        userMessage = "The server hit a problem processing your claim. Please try again in a moment.";
    }

    return {
        reason,
        userMessage,
        status,
        statusText: response.statusText,
        url: response.url,
        serverMessage: data?.message ?? null,
        validationErrors: data?.errors ?? null,
        bodySnippet: rawText ? rawText.slice(0, 500) : null,
    };
}

// Handles errors that never got a response at all (fetch threw).
function handleClaimNetworkError(error, context) {
    let diagnosis;

    if (error.name === "AbortError") {
        diagnosis = {
            reason: "timeout",
            userMessage:
                context.totalFileSizeMB > 5
                    ? "The request timed out — this usually happens with large uploads on a slow connection. Try again with fewer or smaller files."
                    : "The request took too long and timed out. Please try again.",
        };
    } else if (error instanceof TypeError) {
        // fetch() throws a plain TypeError for actual connectivity failures:
        // offline, DNS failure, connection refused, CORS block, mixed content.
        diagnosis = {
            reason: "network_failure",
            userMessage: navigator.onLine
                ? "We couldn't reach the server. This may be a temporary connection issue — please try again."
                : "You appear to be offline. Please check your connection and try again.",
        };
    } else {
        diagnosis = {
            reason: "unexpected_client_error",
            userMessage: "Something unexpected happened while submitting. Please try again.",
        };
    }

    diagnosis.errorName = error.name;
    diagnosis.errorMessage = error.message;

    logClientError(diagnosis, context);
    showClaimError(diagnosis.userMessage);
}

// Console log (always) + best-effort report to the server (if configured),
// so a support conversation with a user doesn't depend on them reading
// their own browser console to you over the phone.
function logClientError(diagnosis, context) {
    const payload = {
        ...diagnosis,
        ...context,
        timestamp: new Date().toISOString(),
        userAgent: navigator.userAgent,
        pageUrl: window.location.href,
        online: navigator.onLine,
    };

    console.error("[Claim Submission Error]", payload);

    if (!CLIENT_ERROR_LOG_ENDPOINT) return;

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
        const body = JSON.stringify({ event: "claim_submission_error", _token: csrf, ...payload });

        if (navigator.sendBeacon) {
            // Fires even if the user navigates away right after a failure.
            const blob = new Blob([body], { type: "application/json" });
            navigator.sendBeacon(CLIENT_ERROR_LOG_ENDPOINT, blob);
        } else {
            fetch(CLIENT_ERROR_LOG_ENDPOINT, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body,
                keepalive: true,
            }).catch(() => { }); // logging must never itself throw
        }
    } catch {
        // swallow — never let logging break the UX
    }
}

function restoreSubmitButton(btn, originalHTML) {
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

function showClaimError(message, validationErrors = null) {
    let html = `<p>${message}</p>`;

    if (validationErrors && Object.keys(validationErrors).length) {
        const items = Object.values(validationErrors)
            .flat()
            .map((e) => `<li>${e}</li>`)
            .join("");
        html += `<ul style="text-align:left; margin-top:10px; font-size:13px; padding-left:18px;">${items}</ul>`;
    }

    Swal.fire({
        icon: "error",
        title: "Something went wrong",
        html,
        confirmButtonText: "Try Again",
        confirmButtonColor: "#dc2626",
    });
}

// ==================== FILE INSPECTION HELPERS ====================

function countFiles(formData) {
    let count = 0;
    for (const [, value] of formData.entries()) {
        if (value instanceof File) count++;
    }
    return count;
}

function totalFileSize(formData) {
    let bytes = 0;
    for (const [, value] of formData.entries()) {
        if (value instanceof File) bytes += value.size;
    }
    return +(bytes / (1024 * 1024)).toFixed(2);
}

// ==================== COLLECT HELPERS ====================

// Collects all injured person rows from a container
function collectInjuredPersons(containerId) {
    const rows = [];
    document
        .querySelectorAll(`#${containerId} .injured-person-row`)
        .forEach((row) => {
            rows.push({
                name: row.querySelector('[name*="[name]"]')?.value ?? "",
                age: row.querySelector('[name*="[age]"]')?.value ?? "",
                address: row.querySelector('[name*="[address]"]')?.value ?? "",
                injuries:
                    row.querySelector('[name*="[injuries]"]')?.value ?? "",
            });
        });
    return rows;
}

// Collects fire claim property table rows
function collectPropertyRows() {
    const rows = [];
    document
        .querySelectorAll("#propertyTable tbody .property-row")
        .forEach((row) => {
            rows.push({
                qty: row.querySelector('[name="prop_qty[]"]')?.value ?? "",
                description:
                    row.querySelector('[name="prop_desc[]"]')?.value ?? "",
                price_paid:
                    row.querySelector('[name="prop_price[]"]')?.value ?? "",
                depreciation:
                    row.querySelector('[name="prop_deprec[]"]')?.value ?? "",
                claim_amount:
                    row.querySelector('[name="prop_claim[]"]')?.value ?? "",
            });
        });
    return rows;
}

// Safe value getter — returns empty string instead of null
function val(name) {
    return document.querySelector(`[name="${name}"]`)?.value ?? "";
}

function checked(name) {
    return document.querySelector(`[name="${name}"]:checked`)?.value ?? "";
}

function isChecked(name) {
    return document.querySelector(`[name="${name}"]`)?.checked ?? false;
}

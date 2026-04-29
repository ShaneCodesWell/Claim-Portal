// ==================== CLAIM SUBMISSION ====================

async function submitClaim(formId, formData) {
    const submitBtn = document.querySelector(`#${formId} [type="submit"]`);
    const originalHTML = submitBtn?.innerHTML;

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="animate-spin h-4 w-4 inline mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
            </svg>
            Submitting...
        `;
    }

    try {
        const csrfToken = document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content");

        const response = await fetch("/claims", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: JSON.stringify(formData),
        });

        const data = await response.json();

        if (data.success) {
            showClaimSuccessModal(data.claim_number, data.redirect);
        } else {
            showClaimError(
                data.message ?? "Submission failed. Please try again.",
            );
            restoreSubmitButton(submitBtn, originalHTML);
        }
    } catch (error) {
        console.error("Claim submission error:", error);
        showClaimError(
            "A network error occurred. Please check your connection and try again.",
        );
        restoreSubmitButton(submitBtn, originalHTML);
    }
}

function restoreSubmitButton(btn, originalHTML) {
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

function showClaimSuccessModal(claimNumber, redirectUrl) {
    document.getElementById("claimSuccessModal")?.remove();

    const modal = document.createElement("div");
    modal.id = "claimSuccessModal";
    modal.className =
        "fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4";
    modal.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full text-center">
            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check-circle text-green-500 text-3xl"></i>
            </div>
            <h2 class="text-xl font-bold text-gray-800 mb-2">Claim Submitted!</h2>
            <p class="text-gray-500 mb-4 text-sm">Your claim has been received and is being processed.</p>
            <div class="bg-blue-50 border border-blue-100 rounded-lg px-4 py-3 mb-6">
                <p class="text-xs text-gray-500 mb-1">Your claim reference number</p>
                <p class="text-lg font-mono font-bold text-blue-700">${claimNumber}</p>
            </div>
            <p class="text-xs text-gray-400 mb-6">Keep this number for your records. You can use it to track your claim status.</p>
            <button onclick="window.location.href='${redirectUrl}'"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2.5 rounded-lg transition text-sm">
                View My Claims
            </button>
        </div>
    `;
    document.body.appendChild(modal);
    document.body.style.overflow = "hidden";
}

function showClaimError(message) {
    document.getElementById("claimErrorBanner")?.remove();

    const banner = document.createElement("div");
    banner.id = "claimErrorBanner";
    banner.className =
        "fixed top-4 right-4 bg-red-600 text-white px-6 py-3 rounded-xl shadow-xl z-50 flex items-center gap-3 max-w-sm";
    banner.innerHTML = `
        <i class="fas fa-exclamation-circle shrink-0"></i>
        <span class="font-medium text-sm">${message}</span>
        <button onclick="this.parentElement.remove()" class="ml-auto shrink-0 hover:opacity-70">
            <i class="fas fa-times"></i>
        </button>
    `;
    document.body.appendChild(banner);
    setTimeout(() => banner?.remove(), 6000);
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

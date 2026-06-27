// ==================== CLAIM SUBMISSION ====================

async function submitClaimWithFiles(formId, formData, action = "/claims") {
    const submitBtn = document.querySelector(`#${formId} [type="submit"]`);
    const originalText = submitBtn?.innerHTML;

    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...`;
    }

    try {
        const response = await fetch(action, {
            method: "POST", // always POST — _method=PUT handles the override
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
                Accept: "application/json",
            },
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
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
        } else {
            showClaimError(
                data.message || "Submission failed. Please try again.",
            );
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        }
    } catch (error) {
        console.error("Submission error:", error);
        showClaimError("A network error occurred. Please try again.");
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }
}

function restoreSubmitButton(btn, originalHTML) {
    if (btn) {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    }
}

function showClaimError(message) {
    Swal.fire({
        icon: "error",
        title: "Something went wrong",
        text: message,
        confirmButtonText: "Try Again",
        confirmButtonColor: "#dc2626",
    });
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

/**
 * Generic behavior for <x-dynamic-form>. Works off data-* attributes
 * emitted by dynamic-form-field.blade.php — no per-field-name code.
 * One file handles every product (motor, fire, general accident).
 */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.dynamic-claim-form').forEach(initDynamicForm);
});

function initDynamicForm(form) {
    initConditionalRadios(form);
    initBranchSelects(form);
    initRepeatableGroups(form);
    initRepeatableTables(form);
}

/* ── Radio -> conditional panel (e.g. police_report yes/no) ───────── */
function initConditionalRadios(form) {
    form.querySelectorAll('[data-conditional-radio]').forEach(radio => {
        const key = radio.dataset.conditionalRadio;
        if (!key) return;
        radio.addEventListener('change', () => {
            const panel = form.querySelector(`[data-conditional-panel="${key}"]`);
            if (!panel) return;
            const show = radio.value === 'yes' && radio.checked;
            panel.classList.toggle('hidden', !show);
            panel.querySelectorAll('input, textarea, select').forEach(el => {
                el.required = show;
                if (!show) el.value = '';
            });
        });
    });
}

/* ── Select -> branch panel (e.g. driver_type self/other) ──────────── */
function initBranchSelects(form) {
    form.querySelectorAll('[data-branch-select]').forEach(select => {
        const key = select.dataset.branchSelect;
        if (!key) return;

        const setBranch = () => {
            const selected = select.selectedOptions[0];
            const activeBranch = selected ? selected.dataset.branch : null;

            form.querySelectorAll(`[data-branch-panel^="${key}:"]`).forEach(panel => {
                const branchKey = panel.dataset.branchPanel.split(':')[1];
                const show = branchKey === activeBranch;
                panel.classList.toggle('hidden', !show);
                panel.querySelectorAll('input, textarea, select').forEach(el => {
                    if (el.readOnly) return; // profile display fields stay as-is
                    el.required = show;
                    if (!show) el.value = '';
                });
            });
        };

        select.addEventListener('change', setBranch);
        setBranch(); // reflect pre-filled value on load
    });
}

/* ── Repeatable groups (card-style, e.g. Injured Persons) ──────────── */
function initRepeatableGroups(form) {
    form.querySelectorAll('[data-repeatable-group]').forEach(wrapper => {
        const key = wrapper.dataset.repeatableGroup;
        const rowsContainer = wrapper.querySelector(`[data-group-rows="${key}"]`);
        const template = wrapper.querySelector(`[data-group-template="${key}"]`);
        const addBtn = wrapper.querySelector(`[data-add-group-row="${key}"]`);

        let counter = rowsContainer.querySelectorAll('.group-row').length;

        function bindRemove(row) {
            row.querySelector('[data-remove-group-row]')?.addEventListener('click', () => {
                if (rowsContainer.querySelectorAll('.group-row').length > 1) {
                    row.remove();
                } else {
                    row.querySelectorAll('input, textarea').forEach(el => el.value = '');
                }
            });
        }

        rowsContainer.querySelectorAll('.group-row').forEach(bindRemove);

        addBtn?.addEventListener('click', () => {
            const clone = template.content.cloneNode(true);
            clone.querySelectorAll('[data-name-template]').forEach(el => {
                el.name = el.dataset.nameTemplate.replace('__i__', counter);
                el.removeAttribute('data-name-template');
            });
            const row = clone.querySelector('.group-row');
            rowsContainer.appendChild(clone);
            bindRemove(row);
            counter++;
        });
    });
}

/* ── Repeatable tables (row-based, e.g. Particulars of Claim) ──────── */
function initRepeatableTables(form) {
    form.querySelectorAll('[data-repeatable-table]').forEach(wrapper => {
        const key = wrapper.dataset.repeatableTable;
        const tbody = wrapper.querySelector(`[data-table-rows="${key}"]`);
        const template = wrapper.querySelector(`[data-table-row-template="${key}"]`);
        const addBtn = wrapper.querySelector(`[data-add-table-row="${key}"]`);
        const totalEl = wrapper.querySelector(`[data-table-total="${key}"]`);

        let counter = tbody.querySelectorAll('.table-row').length;

        function recalcRow(row) {
            row.querySelectorAll('[data-formula]').forEach(calcInput => {
                const formula = calcInput.dataset.formula;
                if (!formula) return;
                const result = evalRowFormula(row, formula);
                if (result !== null) calcInput.value = result.toFixed(2);
            });
        }

        function recalcTotal() {
            if (!totalEl) return;
            let total = 0;
            tbody.querySelectorAll('[data-col-type="number"], [data-col-type="calculated"]').forEach(input => {
                const v = parseFloat(input.value);
                if (!isNaN(v)) total += v;
            });
            totalEl.textContent = total.toFixed(2);
        }

        function bindRow(row) {
            row.querySelectorAll('input[data-col-type]:not([readonly])').forEach(input => {
                input.addEventListener('input', () => {
                    recalcRow(row);
                    recalcTotal();
                });
            });
            row.querySelector('[data-remove-table-row]')?.addEventListener('click', () => {
                if (tbody.querySelectorAll('.table-row').length > 1) {
                    row.remove();
                } else {
                    row.querySelectorAll('input').forEach(el => el.value = '');
                }
                recalcTotal();
            });
        }

        tbody.querySelectorAll('.table-row').forEach(bindRow);
        recalcTotal();

        addBtn?.addEventListener('click', () => {
            const clone = template.content.cloneNode(true);
            clone.querySelectorAll('[data-name-template]').forEach(el => {
                el.name = el.dataset.nameTemplate.replace('__i__', counter);
                el.removeAttribute('data-name-template');
            });
            const row = clone.querySelector('.table-row');
            tbody.appendChild(clone);
            bindRow(row);
            counter++;
        });
    });
}

/**
 * Minimal, safe formula evaluator for calculated table columns.
 * Supports "colA - colB", "colA + colB", "colA * colB", "colA / colB"
 * where colA/colB are other column keys in the SAME row. Deliberately
 * does not support arbitrary expressions — this is v1 scope, matching
 * the one real case in use today (Price Paid − Depreciation).
 */
function evalRowFormula(row, formula) {
    const match = formula.match(/^\s*(\w+)\s*([+\-*/])\s*(\w+)\s*$/);
    if (!match) return null;

    const [, leftKey, op, rightKey] = match;
    const leftInput = row.querySelector(`[data-col-key="${leftKey}"]`);
    const rightInput = row.querySelector(`[data-col-key="${rightKey}"]`);
    if (!leftInput || !rightInput) return null;

    const left = parseFloat(leftInput.value) || 0;
    const right = parseFloat(rightInput.value) || 0;

    switch (op) {
        case '+': return left + right;
        case '-': return Math.max(left - right, 0);
        case '*': return left * right;
        case '/': return right !== 0 ? left / right : 0;
        default: return null;
    }
}
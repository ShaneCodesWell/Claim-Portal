<div id="liabilityGuideModal"
    class="hidden fixed inset-0 bg-black/60 z-50 items-start justify-center overflow-y-auto py-8 px-4"
    style="transition: opacity 0.2s ease;">
    <div id="liabilityGuideModalInner" class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl relative"
        style="transition: transform 0.25s ease, opacity 0.25s ease; transform: scale(0.95); opacity: 0;">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white rounded-t-2xl z-10">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-blue-600 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-800">Liability Guide Preview</h3>
                    <p class="text-xs text-gray-500">{{ $claim->claim_number }} —
                        {{ ucfirst(str_replace('_', ' ', $claim->claim_type)) }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="printLiabilityGuideFromModal()"
                    class="bg-[#1a3a5c] hover:bg-[#0f2540] text-white text-sm px-4 py-2 rounded-lg font-medium flex items-center gap-2 transition">
                    <i class="fas fa-print"></i> Print
                </button>
                <button type="button" onclick="closeLiabilityGuideModal()"
                    class="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="p-8 m-8 border border-gray-300">
            <div id="liabilityGuideModalContent" class="p-6 min-h-64">
                <div id="liabilityGuideModalLoading" class="flex items-center justify-center py-20">
                    <svg class="animate-spin h-8 w-8 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z">
                        </path>
                    </svg>
                    <span class="ml-3 text-sm text-gray-500">Loading preview...</span>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    let liabilityGuideContentLoaded = false;

    function openLiabilityGuideModal() {
        const modal = document.getElementById('liabilityGuideModal');
        const inner = document.getElementById('liabilityGuideModalInner');

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';

        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                inner.style.transform = 'scale(1)';
                inner.style.opacity = '1';
            });
        });

        if (liabilityGuideContentLoaded) return;

        fetch('{{ route('staff.claims.liability-guide.print', $claim) }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ?? '',
                    'Accept': 'text/html',
                }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('liabilityGuideModalContent').innerHTML = `
            <style>
                #liabilityGuideModalContent table { width:100%; border-collapse:collapse; margin-bottom:10px; }
                #liabilityGuideModalContent th, #liabilityGuideModalContent td { border:1px solid #000; padding:4px 6px; font-size:10px; vertical-align:top; }
                #liabilityGuideModalContent th { font-weight:bold; background:#f0f0f0; text-transform:uppercase; font-size:9px; }
                #liabilityGuideModalContent .field-label { font-weight:bold; text-transform:uppercase; background:#fafafa; }
                #liabilityGuideModalContent .field-value { min-height:18px; }
                #liabilityGuideModalContent .field-value.tall { min-height:36px; }
                #liabilityGuideModalContent .yn-box { display:inline-block; border:1px solid #000; padding:2px 8px; font-size:10px; font-weight:bold; margin-left:4px; }
                #liabilityGuideModalContent .yn-box.selected { background:#000; color:#fff; }
                #liabilityGuideModalContent .section-heading { font-weight:bold; font-size:11px; margin:14px 0 6px; text-transform:uppercase; }
                #liabilityGuideModalContent .form-title { text-align:center; font-size:14px; font-weight:bold; text-decoration:underline; text-transform:uppercase; margin:12px 0 14px; }
                #liabilityGuideModalContent .header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:14px; }
                #liabilityGuideModalContent .declaration-block { border:1px solid #000; padding:10px; margin-top:14px; }
                #liabilityGuideModalContent .footer { margin-top:20px; border-top:2px solid #000; padding-top:8px; text-align:center; font-weight:bold; font-size:10px; }
                #liabilityGuideModalContent .signature-line { display:flex; justify-content:space-between; margin-top:16px; gap:20px; }
                #liabilityGuideModalContent .sig-field { flex:1; border-bottom:1px solid #000; padding-bottom:2px; font-size:10px; }
                #liabilityGuideModalContent .sig-label { font-size:9px; color:#555; margin-bottom:14px; }
                #liabilityGuideModalContent img { height:48px; object-fit:contain; }
                #liabilityGuideModalContent * { font-family: Arial, sans-serif; font-size:11px; }
                #liabilityGuideModalContent .no-print { display:none !important; }
            </style>
            ${html}
        `;
                liabilityGuideContentLoaded = true;
            })
            .catch(() => {
                document.getElementById('liabilityGuideModalContent').innerHTML =
                    '<p class="text-center text-red-500 py-12 text-sm">Failed to load liability guide preview. <a href="{{ route('staff.claims.liability-guide.print', $claim) }}" target="_blank" class="underline">Open in new tab instead.</a></p>';
            });
    }

    function closeLiabilityGuideModal() {
        const modal = document.getElementById('liabilityGuideModal');
        const inner = document.getElementById('liabilityGuideModalInner');

        inner.style.transform = 'scale(0.95)';
        inner.style.opacity = '0';

        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }, 220);
    }

    function printLiabilityGuideFromModal() {
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '{{ route('staff.claims.liability-guide.print', $claim) }}';
        document.body.appendChild(iframe);
        iframe.onload = function() {
            iframe.contentWindow.print();
            setTimeout(() => iframe.remove(), 2000);
        };
    }

    document.getElementById('liabilityGuideModal')?.addEventListener('click', function(e) {
        if (e.target === this) closeLiabilityGuideModal();
    });

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !document.getElementById('liabilityGuideModal')?.classList.contains(
            'hidden')) {
            closeLiabilityGuideModal();
        }
    });
</script>

<div id="docViewModal" class="fixed inset-0 bg-black/60 hidden items-center justify-center z-50 px-4">
    {{-- Added overflow-hidden to clip children to the rounded corners --}}
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2">
                <i id="docViewIcon" class="fas fa-file text-gray-400"></i>
                <span id="docViewName" class="text-sm font-semibold text-gray-800 truncate max-w-xs"></span>
            </div>
            <div class="flex items-center gap-2">
                <a id="docViewDownload" href="#" download
                    class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg flex items-center gap-1">
                    <i class="fas fa-download"></i> Download
                </a>
                <button onclick="closeDocPreview()"
                    class="text-gray-400 hover:text-gray-600 w-8 h-8 flex items-center justify-center rounded-lg hover:bg-gray-100">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        {{-- overflow-auto stays here so only the body scrolls, not the whole modal --}}
        <div id="docViewBody"
            class="flex-1 overflow-auto p-4 flex items-center justify-center bg-gray-50">
        </div>
    </div>
</div>
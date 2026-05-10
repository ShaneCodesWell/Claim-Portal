import "./bootstrap";
import "./staffLayout";

// ── Document Preview Modal ──────────────────────────────────────────────────

window.openDocPreview = function (url, name, mimeType) {
    const modal = document.getElementById("docViewModal");
    const body = document.getElementById("docViewBody");
    const nameEl = document.getElementById("docViewName");
    const iconEl = document.getElementById("docViewIcon");
    const download = document.getElementById("docViewDownload");

    nameEl.textContent = name;
    download.href = url + "?download=1";
    body.innerHTML = "";

    if (mimeType.includes("pdf")) {
        iconEl.className = "fas fa-file-pdf text-red-400";
        body.innerHTML = `<iframe src="${url}" class="w-full rounded" style="height:65vh;" frameborder="0"></iframe>`;
    } else if (mimeType.includes("image")) {
        iconEl.className = "fas fa-image text-blue-400";
        body.innerHTML = `<img src="${url}" class="max-w-full max-h-[65vh] rounded-lg shadow object-contain" />`;
    } else {
        iconEl.className = "fas fa-file text-gray-400";
        body.innerHTML = `
            <div class="text-center text-gray-500 py-12">
                <i class="fas fa-file-alt text-5xl mb-4 text-gray-300"></i>
                <p class="text-sm font-medium">${name}</p>
                <p class="text-xs text-gray-400 mt-1">Preview not available for this file type.</p>
                <a href="${url}?download=1"
                   class="mt-4 inline-block bg-blue-600 text-white text-sm px-4 py-2 rounded-lg">
                   Download to view
                </a>
            </div>`;
    }

    // Compensate for scrollbar width to prevent layout shift
    const scrollbarWidth =
        window.innerWidth - document.documentElement.clientWidth;
    document.body.style.paddingRight = scrollbarWidth + "px";
    document.body.style.overflow = "hidden";

    modal.classList.remove("hidden");
    modal.classList.add("flex");
};

window.closeDocPreview = function () {
    const modal = document.getElementById("docViewModal");
    modal.classList.add("hidden");
    modal.classList.remove("flex");

    // Restore body scroll and remove scrollbar compensation
    document.body.style.overflow = "";
    document.body.style.paddingRight = "";

    document.getElementById("docViewBody").innerHTML = "";
};

document.addEventListener("DOMContentLoaded", () => {
    document.getElementById("docViewModal")?.addEventListener("click", (e) => {
        if (e.target.id === "docViewModal") window.closeDocPreview();
    });

    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") window.closeDocPreview();
    });
});



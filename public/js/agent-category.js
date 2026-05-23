const categorySelect = document.getElementById("categorySelect");
const subCategorySelect = document.getElementById("subCategorySelect");

if (categorySelect && subCategorySelect) {
    const categoryMap = {
        "Service Providers": [
            "Financiers",
            "Surveyors",
            "Suppliers",
            "Service Providers",
            "Lawyers",
            "Salvage Buyer",
        ],

        Reinsurance: ["Reinsurance Inwards", "Reinsurance Outwards"],

        Agent: ["Agents", "Business Promo", "Sub-Agent"],

        Broker: ["Broker", "Sub Broker"],

        Bancassurance: ["Bancassurance"],

        Protocol: ["N/A"],
    };

    categorySelect.addEventListener("change", function () {
        const selectedCategory = this.value;

        subCategorySelect.innerHTML =
            '<option value="">Select Sub Category</option>';

        if (!selectedCategory || !categoryMap[selectedCategory]) {
            return;
        }

        categoryMap[selectedCategory].forEach((subCategory) => {
            const option = document.createElement("option");

            option.value = subCategory;
            option.textContent = subCategory;

            subCategorySelect.appendChild(option);
        });
    });

    window.addEventListener("DOMContentLoaded", () => {
        const selectedCategory = categorySelect.value;

        if (selectedCategory && categoryMap[selectedCategory]) {
            categorySelect.dispatchEvent(new Event("change"));

            const selectedSubCategory =
                subCategorySelect.dataset.selected?.trim();

            if (selectedSubCategory) {
                subCategorySelect.value = selectedSubCategory;
            }
        }
    });
}

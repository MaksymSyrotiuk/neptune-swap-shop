// Script written partially with ChatGPT

document.addEventListener("DOMContentLoaded", () => {
    const selectBtn = document.querySelector(".select-btn");
    const options = document.querySelector(".options");
    const checkboxes = document.querySelectorAll(
        '.options input[type="checkbox"]',
    );

    if (!selectBtn || !options || checkboxes.length === 0) return;

    // Open/close category list
    selectBtn.addEventListener("click", () => {
        options.classList.toggle("active");
    });

    document.addEventListener("click", (event) => {
        if (
            !selectBtn.contains(event.target) &&
            !options.contains(event.target)
        ) {
            options.classList.remove("active");
        }
    });

    // Function to update URL
    function updateURL() {
        let selectedCategories = [];

        checkboxes.forEach((checkbox) => {
            if (checkbox.checked) {
                selectedCategories.push(checkbox.value);
            }
        });

        const categoryParam = selectedCategories.length
            ? `category=${selectedCategories.join(",")}`
            : "";
        const baseURL = window.location.pathname;
        const newURL = `${baseURL}?${categoryParam}`;

        window.history.pushState({}, "", newURL);
    }

    // Function to set initial checkbox state
    function setInitialCheckboxState() {
        const urlParams = new URLSearchParams(window.location.search);
        const selectedCategories = urlParams.get("category")
            ? urlParams.get("category").split(",")
            : [];

        checkboxes.forEach((checkbox) => {
            checkbox.checked = selectedCategories.includes(checkbox.value);
        });
    }

    // Initialization
    setInitialCheckboxState();

    // Checkbox click handler
    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener("change", updateURL);
    });
});

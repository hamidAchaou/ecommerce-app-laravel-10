document.addEventListener("DOMContentLoaded", () => {
    /**
     * ===============================
     * Quantity Controls
     * ===============================
     */
    document.addEventListener("click", (e) => {
        if (e.target.matches(".quantity-increment, .quantity-decrement")) {
            const input = e.target.closest("div").querySelector(".quantity-input");
            if (!input) return;

            const min = parseInt(input.getAttribute("min")) || 1;
            const max = parseInt(input.getAttribute("max")) || 99;
            let value = parseInt(input.value) || min;

            if (e.target.matches(".quantity-increment") && value < max) {
                input.value = value + 1;
            } else if (e.target.matches(".quantity-decrement") && value > min) {
                input.value = value - 1;
            }
        }
    });

    document.querySelectorAll(".quantity-input").forEach((input) => {
        input.addEventListener("change", () => {
            const min = parseInt(input.getAttribute("min")) || 1;
            const max = parseInt(input.getAttribute("max")) || 99;
            let value = parseInt(input.value) || min;
            input.value = Math.min(Math.max(value, min), max);
        });
    });

    /**
     * ===============================
     * Price Filter Sliders
     * ===============================
     */
    const minSlider = document.getElementById("minSlider");
    const maxSlider = document.getElementById("maxSlider");
    const minPrice = document.getElementById("minPrice");
    const maxPrice = document.getElementById("maxPrice");
    const minLabel = document.getElementById("minLabel");
    const maxLabel = document.getElementById("maxLabel");

    if (minSlider && maxSlider) {
        const updateSlider = () => {
            let minVal = parseInt(minSlider.value);
            let maxVal = parseInt(maxSlider.value);

            if (minVal > maxVal - 10) minVal = maxVal - 10;
            if (maxVal < minVal + 10) maxVal = minVal + 10;

            minSlider.value = minVal;
            maxSlider.value = maxVal;

            if (minPrice) minPrice.value = minVal;
            if (maxPrice) maxPrice.value = maxVal;

            if (minLabel) minLabel.textContent = `$${minVal}`;
            if (maxLabel) maxLabel.textContent = `$${maxVal}`;
        };

        minSlider.addEventListener("input", updateSlider);
        maxSlider.addEventListener("input", updateSlider);
        updateSlider();
    }

    /**
     * ===============================
     * Wishlist Buttons
     * ===============================
     */
    const toggleWishlistButton = (button, isActive) => {
        const svg = button.querySelector("svg");
        if (!svg) return;

        if (isActive) {
            button.classList.add("bg-red-600", "text-white", "border-red-600", "hover:bg-red-700");
            button.classList.remove("bg-white", "text-red-600", "hover:bg-red-50");
            svg.setAttribute("fill", "currentColor");
        } else {
            button.classList.remove("bg-red-600", "text-white", "border-red-600", "hover:bg-red-700");
            button.classList.add("bg-white", "text-red-600", "hover:bg-red-50");
            svg.setAttribute("fill", "none");
        }
    };

    document.addEventListener("click", async (e) => {
        const button = e.target.closest(".wishlist-btn");
        if (!button) return;

        const productId = button.dataset.productId;
        const isActive = button.classList.contains("bg-red-600");

        try {
            const response = await fetch(`/wishlist/${productId}`, {
                method: isActive ? "DELETE" : "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                    Accept: "application/json",
                },
                credentials: "same-origin",
            });

            const data = await response.json().catch(() => ({}));

            if (response.status === 401 || data.message === "Unauthenticated.") {
                window.location.href = "/login";
                return;
            }

            if (!response.ok) {
                throw new Error(data.message || "Wishlist action failed");
            }

            if (data.success) {
                toggleWishlistButton(button, data.isInWishlist);
            }
        } catch (error) {
            console.error(error);
            alert("Something went wrong. Please try again.");
        }
    });

    /**
     * ===============================
     * Category Filters
     * ===============================
     */
    const categoryForm = document.querySelector("form[action*='products']");
    if (categoryForm) {
        const allCategory = categoryForm.querySelector("input[type='radio'][name='category[]'][value='']");
        const categoryCheckboxes = categoryForm.querySelectorAll("input[type='checkbox'][name='category[]']");

        if (allCategory) {
            allCategory.addEventListener("change", () => {
                if (allCategory.checked) {
                    categoryCheckboxes.forEach((checkbox) => (checkbox.checked = false));
                }
                categoryForm.submit();
            });
        }

        categoryCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", () => {
                if (checkbox.checked && allCategory.checked) {
                    allCategory.checked = false;
                }
                categoryForm.submit();
            });
        });
    }
});

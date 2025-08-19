document.addEventListener("DOMContentLoaded", () => {
    const cartCountBadge = document.getElementById("cart-count");

    // Attach click event to all add-to-cart buttons
    document.querySelectorAll(".add-to-cart-btn").forEach(button => {
        button.addEventListener("click", async () => {
            const productId = button.dataset.productId;

            // Get the quantity input closest to this button
            const quantityInput = button.closest('div').querySelector('.quantity-input');
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;

            if (quantity < 1) {
                showToast("Quantity must be at least 1", true);
                return;
            }

            // Disable button while processing
            setButtonLoading(button, true);

            try {
                const response = await fetch("/cart", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json",
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({ product_id: productId, quantity })
                });

                if (!response.ok) throw new Error("Network error");

                const data = await response.json();

                // Update cart count badge
                if (cartCountBadge) cartCountBadge.textContent = data.cart_count;

                // Show success toast
                showToast(data.message || "Item added to cart!");
            } catch (error) {
                console.error(error);
                showToast("Failed to add item to cart", true);
            } finally {
                setButtonLoading(button, false);
            }
        });
    });

    /**
     * Show a temporary toast notification
     * @param {string} message - The message to display
     * @param {boolean} isError - Whether it's an error toast
     */
    function showToast(message, isError = false) {
        const toast = document.createElement("div");
        toast.className = `
            fixed bottom-5 right-5 z-50 px-4 py-2 rounded-xl shadow-lg text-white
            ${isError ? "bg-red-600" : "bg-green-600"}
            transition-opacity duration-500
        `;
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add("opacity-0");
            setTimeout(() => toast.remove(), 500);
        }, 2000);
    }

    /**
     * Set the loading state of a button
     * @param {HTMLButtonElement} button 
     * @param {boolean} isLoading 
     */
    function setButtonLoading(button, isLoading) {
        button.disabled = isLoading;
        button.classList.toggle("opacity-50", isLoading);
        button.classList.toggle("cursor-not-allowed", isLoading);
    }
});

// add to cart js
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const maxQuantity = parseInt(btn.dataset.maxQuantity || 99, 10);
            let currentQuantity = parseInt(btn.dataset.quantity, 10) || 0;

            if (currentQuantity < maxQuantity) {
                currentQuantity += 1;
                btn.dataset.quantity = currentQuantity;

                // Update button text
                const textElement = btn.querySelector('.cart-text');
                textElement.textContent = currentQuantity === 1 ? '1 Added to Cart' : `${currentQuantity} Added to Cart`;

                // Update button background
                btn.classList.remove('bg-morocco-red', 'bg-morocco-blue');
                btn.classList.add(currentQuantity === 1 ? 'bg-morocco-blue' : 'bg-morocco-green');

            }
        });
    });
});
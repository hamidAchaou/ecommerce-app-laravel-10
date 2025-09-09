import "./bootstrap";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import Swal from "sweetalert2";
import { confirmDelete } from "./helpers/confirmDelete";
import Choices from "choices.js";
import Chart from 'chart.js/auto';
import './pages/product';
import './pages/cart';
import checkout from './pages/checkout.js';

Alpine.data('checkout', checkout);
// ==================== Alpine ====================
Alpine.plugin(collapse);
window.Alpine = Alpine;
Alpine.start();

// ==================== SweetAlert ====================
window.Swal = Swal;
window.confirmDelete = confirmDelete;

// ==================== Choices.js ====================
window.choicesInstances = {};

/**
 * Initializes Choices.js on a given select element by ID.
 * @param {string} selectId - The ID of the <select> element
 */
function initChoicesFor(selectId) {
    const element = document.getElementById(selectId);
    if (!element) return;

    // Destroy previous instance if exists
    if (window.choicesInstances[selectId]) {
        window.choicesInstances[selectId].destroy();
    }

    // Create new instance
    window.choicesInstances[selectId] = new Choices(element, {
        removeItemButton: true,
        placeholderValue: element.getAttribute("placeholder") || "Sélectionnez...",
        searchPlaceholderValue: "Rechercher...",
        shouldSort: false,
    });
}

/**
 * Initialize all required select elements with Choices.js
 */
window.initChoices = function () {
    ["roles", "permissions"].forEach(initChoicesFor);
};

/**
 * Select all options in a multiselect element
 * @param {string} selectId 
 */
window.selectAll = function (selectId) {
    const element = document.getElementById(selectId);
    const instance = window.choicesInstances[selectId];
    if (element && instance) {
        const values = Array.from(element.options).map(option => option.value);
        instance.setChoiceByValue(values);
    }
};

/**
 * Deselect all selected options in a multiselect element
 * @param {string} selectId 
 */
window.deselectAll = function (selectId) {
    const instance = window.choicesInstances[selectId];
    if (instance) {
        instance.removeActiveItems();
    }
};

// ==================== Run on Page Load ====================
window.addEventListener("load", window.initChoices);

// ================ Chart.js ====================
window.Chart = Chart;

// Add this to your success page or global script
function clearLocalCart() {
    // Clear localStorage cart if you're using client-side storage
    if (typeof(Storage) !== "undefined") {
        localStorage.removeItem('cart');
        localStorage.removeItem('cartItems');
        localStorage.removeItem('cartTotal');
    }

    // Clear sessionStorage cart if you're using it
    if (typeof(Storage) !== "undefined") {
        sessionStorage.removeItem('cart');
        sessionStorage.removeItem('cartItems');
        sessionStorage.removeItem('cartTotal');
    }

    // Dispatch custom event to notify other components
    window.dispatchEvent(new CustomEvent('cartCleared'));
    
    console.log('Local cart cleared after successful payment');
}

// Call this function on the success page
document.addEventListener('DOMContentLoaded', function() {
    // Only clear cart if we're on the success page and have a valid session
    if (window.location.pathname.includes('checkout/success') && 
        new URLSearchParams(window.location.search).get('session_id')) {
        clearLocalCart();
    }
});

// Optional: Add to your Alpine.js checkout component
function enhanceCheckoutPage() {
    return {
        // ... existing checkout page data ...
        
        init() {
            // Listen for successful payment completion
            window.addEventListener('cartCleared', () => {
                this.cartItems = [];
                console.log('Cart cleared via event');
            });
        },

        // Enhanced clear cart method
        async clearCart() {
            try {
                // Clear server-side cart
                const res = await fetch('/cart/clear/all', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                if (res.ok) {
                    this.cartItems = [];
                    clearLocalCart(); // Also clear local storage
                }
            } catch (err) {
                console.error('Error clearing cart:', err);
            }
        },

        // ... rest of your checkout methods ...
    }
}
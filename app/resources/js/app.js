import "./bootstrap";
import Alpine from "alpinejs";
import collapse from "@alpinejs/collapse";
import Swal from "sweetalert2";
import { confirmDelete } from "./helpers/confirmDelete";
import Choices from "choices.js";

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

document.addEventListener('DOMContentLoaded', function() {
    // Handle quantity input changes
    const quantityInputs = document.querySelectorAll('.quantity-input');
    quantityInputs.forEach(input => {
        input.addEventListener('change', function() {
            const min = parseInt(this.getAttribute('min')) || 1;
            const max = parseInt(this.getAttribute('max')) || 99;
            let value = parseInt(this.value);
            
            if (isNaN(value) || value < min) {
                this.value = min;
            } else if (value > max) {
                this.value = max;
            }
        });
    });

    // Handle quantity increment/decrement buttons if they exist
    document.addEventListener('click', function(e) {
        if (e.target.matches('.quantity-increment')) {
            const input = e.target.parentNode.querySelector('.quantity-input');
            if (input) {
                const max = parseInt(input.getAttribute('max')) || 99;
                const current = parseInt(input.value) || 1;
                if (current < max) {
                    input.value = current + 1;
                }
            }
        } else if (e.target.matches('.quantity-decrement')) {
            const input = e.target.parentNode.querySelector('.quantity-input');
            if (input) {
                const min = parseInt(input.getAttribute('min')) || 1;
                const current = parseInt(input.value) || 1;
                if (current > min) {
                    input.value = current - 1;
                }
            }
        }
    });
});
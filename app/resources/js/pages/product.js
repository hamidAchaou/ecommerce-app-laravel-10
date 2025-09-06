document.addEventListener('DOMContentLoaded', function () {
    /**
     * Quantity controls
     */
    const quantityInputs = document.querySelectorAll('.quantity-input');

    quantityInputs.forEach(input => {
        input.addEventListener('change', function () {
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

    document.addEventListener('click', function (e) {
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

    /**
     * Price filter sliders
     */
    const minSlider = document.getElementById('minSlider');
    const maxSlider = document.getElementById('maxSlider');
    const minPrice = document.getElementById('minPrice');
    const maxPrice = document.getElementById('maxPrice');
    const minLabel = document.getElementById('minLabel');
    const maxLabel = document.getElementById('maxLabel');

    if (minSlider && maxSlider) {
        function updateSlider() {
            let minVal = parseInt(minSlider.value);
            let maxVal = parseInt(maxSlider.value);

            // Prevent overlap
            if (minVal > maxVal - 10) {
                minVal = maxVal - 10;
                minSlider.value = minVal;
            }
            if (maxVal < minVal + 10) {
                maxVal = minVal + 10;
                maxSlider.value = maxVal;
            }

            // Update hidden fields
            minPrice.value = minVal;
            maxPrice.value = maxVal;

            // Update labels
            minLabel.textContent = "$" + minVal;
            maxLabel.textContent = "$" + maxVal;
        }

        minSlider.addEventListener('input', updateSlider);
        maxSlider.addEventListener('input', updateSlider);
        updateSlider();
    }
});

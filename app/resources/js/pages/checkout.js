// app/resources/js/pages/checkout.js
export default function checkoutPage() {
    return {
        // Cart functionality
        cartItems: window.cartItems || [],
        
        // Checkout functionality  
        countries: window.countries || [],
        selectedCountry: window.oldCountryId || "",
        selectedCity: window.oldCityId || "",
        
        // Loading state
        isProcessingPayment: false,
        
        // Computed properties
        get filteredCities() {
            const country = this.countries.find(c => c.id == this.selectedCountry);
            return country ? country.cities : [];
        },
        
        // Cart methods
        getCartTotal() {
            return this.cartItems.reduce((acc, item) => acc + item.price * item.quantity, 0);
        },
        
        getCartCount() {
            return this.cartItems.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        },
        
        async clearCart() {
            const result = await this.showConfirmDialog(
                'Clear Cart?',
                'All items will be removed from your cart!',
                'warning'
            );

            if (!result.isConfirmed) return;

            try {
                const response = await fetch('/cart/clear/all', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });

                if (response.ok) {
                    this.cartItems = [];
                    this.showNotification('Cart cleared successfully!', 'success');
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to clear cart');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                this.showNotification(error.message || 'Failed to clear cart', 'error');
            }
        },
        
        // Payment method
        async payWithStripe() {
            // Prevent multiple submissions
            if (this.isProcessingPayment) return;
            
            // Validate form first
            const form = document.getElementById('checkout-form');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            // Check if cart is empty
            if (this.cartItems.length === 0) {
                this.showNotification('Your cart is empty', 'error');
                return;
            }
            
            this.isProcessingPayment = true;
            
            try {
                const response = await fetch(window.routes?.checkout?.stripe || '/checkout/stripe', {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": this.getCsrfToken()
                    },
                    body: JSON.stringify({
                        name: document.getElementById('name').value,
                        phone: document.getElementById('phone').value,
                        address: document.getElementById('address').value,
                        country_id: document.getElementById('country_id').value,
                        city_id: document.getElementById('city_id').value,
                        notes: document.getElementById('notes').value
                    })
                });
                
                const data = await response.json();
                
                if(data.error) {
                    this.showNotification(data.error, 'error');
                } else if (data.id) {
                    const stripe = Stripe(window.stripeKey);
                    const result = await stripe.redirectToCheckout({ sessionId: data.id });
                    
                    if (result.error) {
                        this.showNotification(result.error.message, 'error');
                    }
                } else {
                    throw new Error('Invalid response from server');
                }
            } catch (error) {
                console.error('Payment error:', error);
                this.showNotification('Payment processing failed. Please try again.', 'error');
            } finally {
                this.isProcessingPayment = false;
            }
        },
        
        // Utility methods
        getCsrfToken() {
            return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        },

        showNotification(message, type = 'info') {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: type,
                    title: message,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            } else {
                alert(message);
            }
        },

        async showConfirmDialog(title, text, icon) {
            if (typeof Swal !== 'undefined') {
                return await Swal.fire({
                    title,
                    text,
                    icon,
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, proceed!',
                    cancelButtonText: 'Cancel'
                });
            } else {
                return { isConfirmed: confirm(`${title}\n${text}`) };
            }
        }
    }
}

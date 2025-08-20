document.addEventListener('alpine:init', () => {
    Alpine.data('cart', () => ({
        open: false,
        cartOpen: false,
        cartItems: [],
        isLoading: false,

        async init() {
            await this.fetchCartItems();
            this.bindAddToCartButtons();
            
            // Listen for cart updates from other parts of the app
            document.addEventListener('cart-updated', () => {
                this.fetchCartItems();
            });
        },

        async fetchCartItems() {
            try {
                const response = await fetch('/cart', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.cartItems = data.cartItems || [];
                } else {
                    console.warn('Failed to load cart');
                    this.cartItems = [];
                }
            } catch (error) {
                console.error('Error loading cart:', error);
                this.cartItems = [];
            }
        },

        // Bind event listeners to add-to-cart buttons
        bindAddToCartButtons() {
            document.addEventListener('click', async (e) => {
                const button = e.target.closest('.add-to-cart-btn');
                if (!button) return;

                e.preventDefault();
                
                const productId = button.getAttribute('data-product-id');
                const quantityElement = button.getAttribute('data-quantity') === '0' 
                    ? document.querySelector(`input[name="quantity"]`) 
                    : null;
                
                const quantity = quantityElement ? parseInt(quantityElement.value) || 1 : 1;
                
                await this.addToCart(productId, quantity, button);
            });
        },

        // Add item to cart
        async addToCart(productId, quantity = 1, buttonElement = null) {
            if (this.isLoading) return;
            
            this.isLoading = true;
            
            // Update button state
            if (buttonElement) {
                const textElement = buttonElement.querySelector('.cart-text');
                if (textElement) {
                    textElement.textContent = 'Adding...';
                }
                buttonElement.disabled = true;
                buttonElement.classList.add('opacity-75');
            }

            try {
                const response = await fetch('/cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Reload cart to get updated data
                    await this.fetchCartItems();
                    
                    // Dispatch custom event to notify other cart instances
                    document.dispatchEvent(new CustomEvent('cart-updated'));
                    
                    // Show success message
                    this.showNotification(data.message || 'Product added to cart!', 'success');
                    
                    // Reset button state
                    if (buttonElement) {
                        const textElement = buttonElement.querySelector('.cart-text');
                        if (textElement) {
                            textElement.textContent = 'Added!';
                            setTimeout(() => {
                                textElement.textContent = 'Add to Cart';
                            }, 2000);
                        }
                    }
                } else {
                    throw new Error(data.message || 'Failed to add product to cart');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                this.showNotification(error.message || 'Failed to add product to cart', 'error');
                
                // Reset button text on error
                if (buttonElement) {
                    const textElement = buttonElement.querySelector('.cart-text');
                    if (textElement) textElement.textContent = 'Add to Cart';
                }
            } finally {
                this.isLoading = false;
                if (buttonElement) {
                    buttonElement.disabled = false;
                    buttonElement.classList.remove('opacity-75');
                }
            }
        },

        // Remove item from cart
        async removeFromCart(productId) {
            try {
                const response = await fetch(`/cart/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    await this.fetchCartItems();
                    document.dispatchEvent(new CustomEvent('cart-updated'));
                    this.showNotification('Item removed from cart', 'success');
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to remove item');
                }
            } catch (error) {
                console.error('Error removing item:', error);
                this.showNotification(error.message || 'Failed to remove item', 'error');
            }
        },

        // Update item quantity
        async updateQuantity(productId, quantity) {
            if (quantity < 1) {
                await this.removeFromCart(productId);
                return;
            }

            try {
                const response = await fetch(`/cart/${productId}`, {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify({ quantity: quantity })
                });

                if (response.ok) {
                    await this.fetchCartItems();
                    document.dispatchEvent(new CustomEvent('cart-updated'));
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to update quantity');
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                this.showNotification(error.message || 'Failed to update quantity', 'error');
            }
        },

        // Clear entire cart
        async clearCart() {
            if (!confirm('Are you sure you want to clear your cart?')) return;

            try {
                const response = await fetch('/cart/clear/all', {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    }
                });

                if (response.ok) {
                    this.cartItems = [];
                    document.dispatchEvent(new CustomEvent('cart-updated'));
                    this.showNotification('Cart cleared', 'success');
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to clear cart');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                this.showNotification(error.message || 'Failed to clear cart', 'error');
            }
        },

        // Get cart total
        getCartTotal() {
            return this.cartItems.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
        },

        // Get cart count
        getCartCount() {
            return this.cartItems.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        },

        // Show notification
        showNotification(message, type = 'info') {
            // Remove any existing notifications
            const existingNotifications = document.querySelectorAll('.cart-notification');
            existingNotifications.forEach(notification => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            });

            // Create notification element
            const notification = document.createElement('div');
            notification.className = `cart-notification fixed top-4 right-4 z-[9999] p-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
                type === 'success' ? 'bg-green-500' : 
                type === 'error' ? 'bg-red-500' : 'bg-blue-500'
            } text-white max-w-sm`;
            notification.textContent = message;

            document.body.appendChild(notification);

            // Animate in
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);

            // Auto remove after 3 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }, 3000);
        }
    }));

    // Global function to add to cart (can be called from anywhere)
    window.addToCart = async function(productId, quantity = 1) {
        const cartInstance = Alpine.$data(document.querySelector('[x-data*="cart"]'));
        if (cartInstance) {
            await cartInstance.addToCart(productId, quantity);
        }
    };
});
document.addEventListener('alpine:init', () => {
    Alpine.data('cart', () => ({
        open: false,
        cartOpen: false,
        cartItems: [],
        isLoading: false,

        async init() {
            await this.fetchCartItems();
            // Remove duplicate event binding - Alpine handles this automatically
            
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
                    this.updateCartBadge();
                } else {
                    console.warn('Failed to load cart');
                    this.cartItems = [];
                }
            } catch (error) {
                console.error('Error loading cart:', error);
                this.cartItems = [];
            }
        },

        async addToCart(productId, quantity = 1, buttonElement = null) {
            // Prevent multiple simultaneous requests
            if (this.isLoading) {
                console.log('Cart operation already in progress');
                return;
            }
            
            this.isLoading = true;

            if (buttonElement) {
                const textElement = buttonElement.querySelector('.cart-text');
                if (textElement) textElement.textContent = 'Adding...';
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
                        product_id: parseInt(productId), 
                        quantity: parseInt(quantity) 
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    await this.fetchCartItems();
                    document.dispatchEvent(new CustomEvent('cart-updated'));
                    this.showNotification(data.message || 'Product added to cart!', 'success');

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

        async removeFromCart(productId) {
            const result = await Swal.fire({
                title: 'Remove item?',
                text: "This item will be removed from your cart!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) return;

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
                    body: JSON.stringify({ quantity: parseInt(quantity) })
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

        async clearCart() {
            const result = await Swal.fire({
                title: 'Clear Cart?',
                text: "All items will be removed from your cart!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'Cancel'
            });

            if (!result.isConfirmed) return;

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
                    Swal.fire({
                        title: 'Cleared!',
                        text: 'Your cart has been emptied.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to clear cart');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                Swal.fire({
                    title: 'Error!',
                    text: error.message || 'Failed to clear cart',
                    icon: 'error'
                });
            }
        },

        getCartTotal() {
            return this.cartItems.reduce((sum, item) => sum + (parseFloat(item.price) * parseInt(item.quantity)), 0);
        },

        getCartCount() {
            return this.cartItems.reduce((sum, item) => sum + parseInt(item.quantity), 0);
        },

        updateCartBadge() {
            const badge = document.getElementById('cart-count');
            if (badge) {
                const count = this.getCartCount();
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline' : 'none';
            }
        },

        showNotification(message, type = 'info') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type,
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });
        }
    }));
});

// Single global event listener for add to cart buttons
document.addEventListener('DOMContentLoaded', function() {
    // Remove any existing listeners to prevent duplicates
    const existingListener = document.querySelector('body').getAttribute('data-cart-listener');
    if (existingListener) return;
    
    document.querySelector('body').setAttribute('data-cart-listener', 'true');
    
    document.addEventListener('click', async function(e) {
        const button = e.target.closest('.add-to-cart-btn');
        if (!button) return;
        
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent multiple clicks
        if (button.disabled) return;
        
        const productId = button.getAttribute('data-product-id');
        if (!productId) {
            console.error('No product ID found on button');
            return;
        }
        
        // Get quantity - check for nearby input or use default
        let quantity = 1;
        const quantityInput = button.closest('.product-actions, .product-card, form')?.querySelector('input[name="quantity"], .quantity-input');
        if (quantityInput) {
            quantity = parseInt(quantityInput.value) || 1;
        }
        
        // Find cart instance
        const cartElement = document.querySelector('[x-data*="cart"]');
        if (cartElement && cartElement._x_dataStack) {
            const cartData = cartElement._x_dataStack[0];
            if (cartData && typeof cartData.addToCart === 'function') {
                await cartData.addToCart(productId, quantity, button);
            }
        } else {
            // Fallback for non-Alpine pages
            await addToCartFallback(productId, quantity, button);
        }
    });
});

// Fallback function for pages without Alpine.js
async function addToCartFallback(productId, quantity, buttonElement) {
    if (buttonElement.disabled) return;
    
    buttonElement.disabled = true;
    const originalText = buttonElement.querySelector('.cart-text')?.textContent || buttonElement.textContent;
    
    if (buttonElement.querySelector('.cart-text')) {
        buttonElement.querySelector('.cart-text').textContent = 'Adding...';
    } else {
        buttonElement.textContent = 'Adding...';
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
                product_id: parseInt(productId), 
                quantity: parseInt(quantity) 
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Update cart badge
            const badge = document.getElementById('cart-count');
            if (badge && data.cart_count !== undefined) {
                badge.textContent = data.cart_count;
                badge.style.display = data.cart_count > 0 ? 'inline' : 'none';
            }
            
            // Show success message
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: data.message || 'Product added to cart!',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            }
            
            // Update button text
            if (buttonElement.querySelector('.cart-text')) {
                buttonElement.querySelector('.cart-text').textContent = 'Added!';
                setTimeout(() => {
                    buttonElement.querySelector('.cart-text').textContent = originalText;
                }, 2000);
            } else {
                buttonElement.textContent = 'Added!';
                setTimeout(() => {
                    buttonElement.textContent = originalText;
                }, 2000);
            }
        } else {
            throw new Error(data.message || 'Failed to add product to cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'error',
                title: error.message || 'Failed to add product to cart',
                showConfirmButton: false,
                timer: 3000
            });
        }
        
        // Restore button text
        if (buttonElement.querySelector('.cart-text')) {
            buttonElement.querySelector('.cart-text').textContent = originalText;
        } else {
            buttonElement.textContent = originalText;
        }
    } finally {
        buttonElement.disabled = false;
    }
}

// Global function for external calls
window.addToCart = async function(productId, quantity = 1) {
    const cartElement = document.querySelector('[x-data*="cart"]');
    if (cartElement && cartElement._x_dataStack) {
        const cartData = cartElement._x_dataStack[0];
        if (cartData && typeof cartData.addToCart === 'function') {
            await cartData.addToCart(productId, quantity);
        }
    } else {
        await addToCartFallback(productId, quantity);
    }
};
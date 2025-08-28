document.addEventListener('alpine:init', () => {
    Alpine.data('cart', () => ({
        open: false,
        cartOpen: false,
        cartItems: [],
        isLoading: false,
        loadingStates: new Map(), // Track loading state per product

        async init() {
            await this.fetchCartItems();
            
            // Listen for cart updates
            document.addEventListener('cart-updated', () => {
                this.fetchCartItems();
            });

            // Listen for custom add-to-cart events
            document.addEventListener('add-to-cart', (e) => {
                this.addToCart(e.detail.productId, e.detail.quantity, e.detail.button);
            });
        },

        async fetchCartItems() {
            try {
                const response = await fetch('/cart', {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.cartItems = data.cartItems || [];
                    this.updateCartBadge();
                } else {
                    console.warn('Failed to load cart:', response.status);
                    this.cartItems = [];
                }
            } catch (error) {
                console.error('Error loading cart:', error);
                this.cartItems = [];
            }
        },

        async addToCart(productId, quantity = 1, buttonElement = null) {
            const key = `${productId}-${quantity}`;
            
            // Prevent multiple simultaneous requests for same product
            if (this.loadingStates.get(key) || this.isLoading) {
                console.log('Cart operation already in progress for product:', productId);
                return false;
            }
            
            this.loadingStates.set(key, true);
            this.isLoading = true;

            // Update button state
            const buttonState = this.updateButtonState(buttonElement, 'loading');

            try {
                const response = await fetch('/cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
                    },
                    body: JSON.stringify({ 
                        product_id: parseInt(productId), 
                        quantity: parseInt(quantity) 
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Update cart items and badge
                    await this.fetchCartItems();
                    document.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
                    
                    this.showNotification(data.message || 'Product added to cart!', 'success');
                    this.updateButtonState(buttonElement, 'success');
                    
                    return true;
                } else {
                    throw new Error(data.message || `Server error: ${response.status}`);
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                this.showNotification(error.message || 'Failed to add product to cart', 'error');
                this.updateButtonState(buttonElement, 'error');
                return false;
            } finally {
                this.loadingStates.delete(key);
                this.isLoading = false;
                
                // Reset button after delay
                setTimeout(() => {
                    this.updateButtonState(buttonElement, 'default');
                }, 2000);
            }
        },

        async removeFromCart(productId) {
            const result = await this.showConfirmDialog(
                'Remove item?',
                'This item will be removed from your cart!',
                'warning'
            );

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/cart/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.getCsrfToken()
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
            quantity = Math.max(1, Math.min(quantity, 99)); // Clamp between 1-99

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
                        'X-CSRF-TOKEN': this.getCsrfToken()
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
                // Revert the quantity change in UI
                await this.fetchCartItems();
            }
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
                    document.dispatchEvent(new CustomEvent('cart-updated'));
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

        // Helper methods
        getCartTotal() {
            return this.cartItems.reduce((sum, item) => {
                return sum + (parseFloat(item.price) * parseInt(item.quantity));
            }, 0);
        },

        getCartCount() {
            return this.cartItems.reduce((sum, item) => {
                return sum + parseInt(item.quantity);
            }, 0);
        },

        updateCartBadge() {
            const badge = document.getElementById('cart-count');
            if (badge) {
                const count = this.getCartCount();
                badge.textContent = count;
                badge.style.display = count > 0 ? 'inline' : 'none';
            }
        },

        updateButtonState(buttonElement, state) {
            if (!buttonElement) return;

            const textElement = buttonElement.querySelector('.cart-text');
            const iconElement = buttonElement.querySelector('svg');

            // Store original content if not already stored
            if (!buttonElement.dataset.originalText) {
                buttonElement.dataset.originalText = textElement?.textContent || buttonElement.textContent;
            }

            switch (state) {
                case 'loading':
                    if (textElement) textElement.textContent = 'Adding...';
                    buttonElement.disabled = true;
                    buttonElement.classList.add('opacity-75', 'cursor-not-allowed');
                    if (iconElement) iconElement.classList.add('animate-spin');
                    break;

                case 'success':
                    if (textElement) textElement.textContent = 'Added!';
                    buttonElement.classList.remove('bg-morocco-red');
                    buttonElement.classList.add('bg-green-600');
                    break;

                case 'error':
                    if (textElement) textElement.textContent = 'Try Again';
                    buttonElement.classList.remove('bg-morocco-red');
                    buttonElement.classList.add('bg-red-600');
                    break;

                case 'default':
                default:
                    if (textElement) {
                        textElement.textContent = buttonElement.dataset.originalText || 'Add to Cart';
                    }
                    buttonElement.disabled = false;
                    buttonElement.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-green-600', 'bg-red-600');
                    buttonElement.classList.add('bg-morocco-red');
                    if (iconElement) iconElement.classList.remove('animate-spin');
                    break;
            }
        },

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
                // Fallback notification
                console.log(`${type.toUpperCase()}: ${message}`);
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
                // Fallback to native confirm
                return { isConfirmed: confirm(`${title}\n${text}`) };
            }
        }
    }));
});

// Enhanced global event listener for add to cart buttons
document.addEventListener('DOMContentLoaded', function() {
    let isListenerAttached = false;
    
    function attachCartListener() {
        if (isListenerAttached) return;
        isListenerAttached = true;

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
            
            // Get quantity from multiple possible sources
            let quantity = 1;
            const quantityFromButton = button.getAttribute('data-quantity');
            const quantityInput = button.closest('.product-actions, .product-card, form, .product-details')
                ?.querySelector('input[name="quantity"], .quantity-input, [x-model*="quantity"]');
            
            if (quantityInput && quantityInput.value) {
                quantity = parseInt(quantityInput.value) || 1;
            } else if (quantityFromButton && quantityFromButton !== '0') {
                quantity = parseInt(quantityFromButton) || 1;
            }

            // Ensure quantity is within limits
            quantity = Math.max(1, Math.min(quantity, 99));
            
            // Try Alpine.js first
            const cartElement = document.querySelector('[x-data*="cart"]');
            if (cartElement && cartElement._x_dataStack) {
                const cartData = cartElement._x_dataStack[0];
                if (cartData && typeof cartData.addToCart === 'function') {
                    await cartData.addToCart(productId, quantity, button);
                    return;
                }
            }
            
            // Fallback for non-Alpine pages
            await addToCartFallback(productId, quantity, button);
        });
    }

    // Attach listener immediately if DOM is ready
    attachCartListener();
    
    // Also listen for dynamic content changes
    if (window.MutationObserver) {
        const observer = new MutationObserver(() => {
            attachCartListener();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }
});

// Enhanced fallback function for pages without Alpine.js
async function addToCartFallback(productId, quantity, buttonElement) {
    if (buttonElement?.disabled) return;
    
    const originalState = {
        disabled: buttonElement?.disabled || false,
        text: buttonElement?.querySelector('.cart-text')?.textContent || buttonElement?.textContent || 'Add to Cart',
        classes: buttonElement?.className || ''
    };
    
    // Update button state
    if (buttonElement) {
        buttonElement.disabled = true;
        buttonElement.classList.add('opacity-75');
        
        const textElement = buttonElement.querySelector('.cart-text');
        if (textElement) {
            textElement.textContent = 'Adding...';
        } else {
            buttonElement.textContent = 'Adding...';
        }
    }
    
    try {
        const response = await fetch('/cart', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({ 
                product_id: parseInt(productId), 
                quantity: parseInt(quantity) 
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Update cart badge
            updateCartBadge(data.count);
            
            // Show success message
            showFallbackNotification(data.message || 'Product added to cart!', 'success');
            
            // Update button to success state
            updateFallbackButton(buttonElement, 'Added!', 'success');
            
            // Dispatch custom event
            document.dispatchEvent(new CustomEvent('cart-updated', { detail: data }));
            
        } else {
            throw new Error(data.message || 'Failed to add product to cart');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showFallbackNotification(error.message || 'Failed to add product to cart', 'error');
        updateFallbackButton(buttonElement, 'Try Again', 'error');
    } finally {
        // Reset button after delay
        setTimeout(() => {
            if (buttonElement) {
                buttonElement.disabled = originalState.disabled;
                buttonElement.className = originalState.classes;
                
                const textElement = buttonElement.querySelector('.cart-text');
                if (textElement) {
                    textElement.textContent = originalState.text;
                } else {
                    buttonElement.textContent = originalState.text;
                }
            }
        }, 2000);
    }
}

// Helper functions for fallback
function updateCartBadge(count) {
    const badge = document.getElementById('cart-count');
    if (badge) {
        badge.textContent = count || 0;
        badge.style.display = (count > 0) ? 'inline' : 'none';
    }
}

function updateFallbackButton(button, text, state) {
    if (!button) return;
    
    const textElement = button.querySelector('.cart-text');
    
    if (textElement) {
        textElement.textContent = text;
    } else {
        button.textContent = text;
    }
    
    // Update button appearance based on state
    button.classList.remove('bg-morocco-red', 'bg-green-600', 'bg-red-600');
    
    switch (state) {
        case 'success':
            button.classList.add('bg-green-600');
            break;
        case 'error':
            button.classList.add('bg-red-600');
            break;
        default:
            button.classList.add('bg-morocco-red');
    }
}

function showFallbackNotification(message, type) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    } else {
        // Simple fallback notification
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}

// Global function for external calls
window.addToCart = async function(productId, quantity = 1, buttonElement = null) {
    const cartElement = document.querySelector('[x-data*="cart"]');
    if (cartElement && cartElement._x_dataStack) {
        const cartData = cartElement._x_dataStack[0];
        if (cartData && typeof cartData.addToCart === 'function') {
            return await cartData.addToCart(productId, quantity, buttonElement);
        }
    }
    
    return await addToCartFallback(productId, quantity, buttonElement);
};

// Utility function to trigger add to cart from anywhere
window.triggerAddToCart = function(productId, quantity = 1, buttonSelector = null) {
    const button = buttonSelector ? document.querySelector(buttonSelector) : null;
    const event = new CustomEvent('add-to-cart', {
        detail: { productId, quantity, button }
    });
    document.dispatchEvent(event);
};
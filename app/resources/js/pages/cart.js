
document.addEventListener('alpine:init', () => {
    Alpine.data('cart', () => ({
        open: false,
        cartOpen: false,
        cartItems: [],
        isLoading: false,

        async init() {
            await this.fetchCartItems();
            this.bindAddToCartButtons();

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

        async addToCart(productId, quantity = 1, buttonElement = null) {
            if (this.isLoading) return;
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
                    body: JSON.stringify({ product_id: productId, quantity: quantity })
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

    window.addToCart = async function(productId, quantity = 1) {
        const cartInstance = Alpine.$data(document.querySelector('[x-data*="cart"]'));
        if (cartInstance) await cartInstance.addToCart(productId, quantity);
    };
});

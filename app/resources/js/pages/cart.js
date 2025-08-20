document.addEventListener('alpine:init', () => {
    Alpine.data('cart', () => ({
        open: false,
        cartOpen: false,
        cartItems: [],

        async init() {
            await this.fetchCartItems();
        },

        async fetchCartItems() {
            try {
                const response = await fetch('/cart', {
                    headers: { 'Accept': 'application/json' }
                });
                if (!response.ok) throw new Error('Failed to fetch cart');
                const data = await response.json();
                this.cartItems = data.cartItems ?? [];
            } catch (err) {
                console.error(err);
                this.cartItems = [];
            }
        },

        async removeFromCart(id) {
            try {
                const response = await fetch(`/cart/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                if (response.ok) {
                    this.cartItems = this.cartItems.filter(i => i.id !== id);
                }
            } catch (err) {
                console.error(err);
            }
        }
    }))
})

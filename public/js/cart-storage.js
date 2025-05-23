// Fungsi untuk menyimpan keranjang ke Local Storage
function saveCartToLocal(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    // Dispatch event untuk memberitahu komponen lain tentang perubahan keranjang
    window.dispatchEvent(new CustomEvent('cartUpdated'));
}

// Fungsi untuk mengambil keranjang dari Local Storage
function getCartFromLocal() {
    const cart = localStorage.getItem('cart');
    return cart ? JSON.parse(cart) : [];
}

// Fungsi untuk menambahkan item ke keranjang
function addToLocalCart(item) {
    const cart = getCartFromLocal();
    const existingItemIndex = cart.findIndex(
        cartItem => cartItem.id === item.id && 
                   cartItem.size_id === item.size_id && 
                   cartItem.sugar_id === item.sugar_id && 
                   cartItem.topping_id === item.topping_id
    );

    if (existingItemIndex > -1) {
        // Update quantity jika item sudah ada
        cart[existingItemIndex].qty += item.qty;
    } else {
        // Tambahkan item baru
        cart.push(item);
    }

    saveCartToLocal(cart);
    return cart;
}

// Fungsi untuk memperbarui kuantitas item di keranjang
function updateCartItemQty(index, newQty) {
    const cart = getCartFromLocal();
    if (cart[index]) {
        if (newQty > 0) {
            cart[index].qty = newQty;
        } else {
            cart.splice(index, 1); // Hapus item jika qty <= 0
        }
        saveCartToLocal(cart);
    }
    return cart;
}

// Fungsi untuk menghapus item dari keranjang
function removeFromLocalCart(index) {
    const cart = getCartFromLocal();
    if (cart[index]) {
        cart.splice(index, 1);
        saveCartToLocal(cart);
    }
    return cart;
}

// Fungsi untuk mengosongkan keranjang
function clearLocalCart() {
    localStorage.removeItem('cart');
    window.dispatchEvent(new CustomEvent('cartUpdated'));
}

// Fungsi untuk menyinkronkan keranjang dari Local Storage ke Session
async function syncCartToSession() {
    const cart = getCartFromLocal();
    try {
        const response = await fetch('/cart/sync', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ cart })
        });
        
        if (!response.ok) {
            throw new Error('Gagal menyinkronkan keranjang');
        }
        
        return await response.json();
    } catch (error) {
        console.error('Error syncing cart:', error);
        throw error;
    }
}

// Ekspor fungsi yang diperlukan
window.CartStorage = {
    saveCartToLocal,
    getCartFromLocal,
    addToLocalCart,
    updateCartItemQty,
    removeFromLocalCart,
    clearLocalCart,
    syncCartToSession
};

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Minuman;

class MinumanDetail extends Component
{
    public $minuman;
    public $cart = []; 
    public function mount($id)
    {
        $this->minuman = Minuman::findOrFail($id);
    }
    // Metode untuk menambahkan produk ke keranjang
    public function addToCart($id, $basePrice, $sizeId, $sugarId, $toppingId)
    {
        // Harga tambahan dari ukuran, gula, dan topping
        $sizePrice = 0;  // Gantilah dengan harga yang sesuai
        $sugarPrice = 0; // Gantilah dengan harga yang sesuai
        $toppingPrice = 0; // Gantilah dengan harga yang sesuai

        // Hitung total harga
        $totalPrice = $basePrice + $sizePrice + $sugarPrice + $toppingPrice;

        // Buat key unik untuk setiap kombinasi produk
        $key = $id . '-' . ($sizeId ?? '0') . '-' . ($sugarId ?? '0') . '-' . ($toppingId ?? '0');

        // Ambil cart yang sudah ada di session, jika tidak ada, buat array kosong
        $cart = session()->get('cart', []);

        // Jika produk sudah ada dalam cart, tambahkan kuantitasnya
        if (isset($cart[$key])) {
            $cart[$key]['qty']++;
        } else {
            // Jika produk belum ada, tambahkan produk baru ke dalam cart
            $cart[$key] = [
                'id' => $id,
                'size_id' => $sizeId,
                'sugar_id' => $sugarId,
                'topping_id' => $toppingId,
                'price' => $totalPrice,
                'qty' => 1, // Kuantitas dimulai dari 1
            ];
        }

        // Simpan kembali cart yang telah diubah ke session
        session()->put('cart', $cart);

        // Menampilkan pesan sukses ke pengguna
        session()->flash('message', 'Produk berhasil ditambahkan ke keranjang!');

        // Mengembalikan response JSON dengan data cart terbaru
        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart' => $cart,
        ]);
    }

    public function render()
    {
        return view('livewire.minuman-detail')->layout('layouts.public');
    }
}
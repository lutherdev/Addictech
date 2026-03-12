<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\ProductModel;

class Cart extends BaseController
{
    protected $session;
    protected $cartModel;
    protected $cartItemModel;
    protected $productModel;

    public function __construct()
    {
        $this->session = session();
        $this->cartModel = model('CartModel');
        $this->cartItemModel = model('CartItemModel');
        $this->productModel = model('ProductModel');
    }

    private function checkLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }
        return null;
    }

    private function getOrCreateCart()
    {
        $userId = $this->session->get('user_id');
        
        $cart = $this->cartModel->where('user_id', $userId)->first();
        
        if (!$cart) {
            $cartData = [
                'user_id' => $userId,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->cartModel->insert($cartData);
            $cartId = $this->cartModel->insertID();
        } else {
            $cartId = $cart['id'];
        }
        
        return $cartId;
    }

    public function index()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $cartId = $this->getOrCreateCart();

        $data = [
            'title' => 'My Cart',
            'cart_items' => $this->cartItemModel->getCartItemsWithProducts($cartId),
            'total' => $this->cartItemModel->getCartTotal($cartId)
        ];
        
        return view('cart/index', $data);
    }

    public function add()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $productId = $this->request->getPost('product_id');
        $quantity = $this->request->getPost('quantity') ?: 1;

        // Check if product exists and has stock
        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        if ($product['stock'] < $quantity) {
            return redirect()->back()->with('error', 'Insufficient stock.');
        }

        $cartId = $this->getOrCreateCart();

        // Check if product already in cart
        $cartItem = $this->cartItemModel
            ->where('cart_id', $cartId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            // Check if total quantity exceeds stock
            $newQuantity = $cartItem['quantity'] + $quantity;
            if ($newQuantity > $product['stock']) {
                return redirect()->back()->with('error', 'Cannot add more than available stock.');
            }
            
            // Update quantity
            $this->cartItemModel->update($cartItem['id'], [
                'quantity' => $newQuantity
            ]);
        } else {
            // Add new item
            $this->cartItemModel->insert([
                'cart_id' => $cartId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart.');
    }

    public function update($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $quantity = $this->request->getPost('quantity');
        
        // Get cart item to check product stock
        $cartItem = $this->cartItemModel->getCartItemWithProduct($id);
        
        if (!$cartItem) {
            return redirect()->to('cart')->with('error', 'Cart item not found.');
        }

        if ($quantity > $cartItem['stock']) {
            return redirect()->to('cart')->with('error', 'Quantity exceeds available stock.');
        }
        
        if ($quantity > 0) {
            $this->cartItemModel->update($id, ['quantity' => $quantity]);
            return redirect()->to('cart')->with('success', 'Cart updated successfully.');
        } else {
            return $this->remove($id);
        }
    }

    public function remove($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $this->cartItemModel->delete($id);
        return redirect()->to('cart')->with('success', 'Item removed from cart.');
    }

    public function clear()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        $cart = $this->cartModel->where('user_id', $userId)->first();
        
        if ($cart) {
            $this->cartItemModel->where('cart_id', $cart['id'])->delete();
        }

        return redirect()->to('cart')->with('success', 'Cart cleared successfully.');
    }

    public function count()
    {
        $check = $this->checkLogin();
        if ($check) return $this->response->setJSON(['count' => 0]);

        $cartId = $this->getOrCreateCart();
        $count = $this->cartItemModel->where('cart_id', $cartId)->countAllResults();
        
        return $this->response->setJSON(['count' => $count]);
    }
}
<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Models\CartModel;
use App\Models\ProductModel;

class CartItem extends BaseController
{
    protected $session;
    protected $cartItemModel;
    protected $cartModel;
    protected $productModel;

    public function __construct()
    {
        $this->session = session();
        $this->cartItemModel = model('CartItemModel');
        $this->cartModel = model('CartModel');
        $this->productModel = model('ProductModel');
    }

    private function checkLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }
        return null;
    }

    private function verifyCartOwnership($cartId)
    {
        $userId = $this->session->get('user_id');
        $cart = $this->cartModel->where('id', $cartId)->where('user_id', $userId)->first();
        
        if (!$cart) {
            return false;
        }
        
        return true;
    }

    public function update($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $quantity = $this->request->getPost('quantity');
        
        // Get cart item
        $cartItem = $this->cartItemModel->find($id);
        
        if (!$cartItem) {
            return redirect()->to('cart')->with('error', 'Cart item not found.');
        }

        // Verify cart ownership
        if (!$this->verifyCartOwnership($cartItem['cart_id'])) {
            return redirect()->to('cart')->with('error', 'Cart not found.');
        }

        // Check product stock
        $product = $this->productModel->find($cartItem['product_id']);
        
        if ($quantity > $product['stock']) {
            return redirect()->to('cart')->with('error', 'Quantity exceeds available stock.');
        }
        
        if ($quantity > 0) {
            $this->cartItemModel->update($id, ['quantity' => $quantity]);
            return redirect()->to('cart')->with('success', 'Cart updated successfully.');
        } else {
            $this->cartItemModel->delete($id);
            return redirect()->to('cart')->with('success', 'Item removed from cart.');
        }
    }

    public function delete($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        // Get cart item
        $cartItem = $this->cartItemModel->find($id);
        
        if (!$cartItem) {
            return redirect()->to('cart')->with('error', 'Cart item not found.');
        }

        // Verify cart ownership
        if (!$this->verifyCartOwnership($cartItem['cart_id'])) {
            return redirect()->to('cart')->with('error', 'Cart not found.');
        }

        $this->cartItemModel->delete($id);
        return redirect()->to('cart')->with('success', 'Item removed from cart.');
    }

    public function bulkUpdate()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $items = $this->request->getPost('items');
        
        if (!is_array($items)) {
            return redirect()->to('cart')->with('error', 'Invalid request.');
        }

        $errors = [];
        
        foreach ($items as $itemId => $quantity) {
            $cartItem = $this->cartItemModel->find($itemId);
            
            if (!$cartItem) {
                continue;
            }

            // Verify cart ownership
            if (!$this->verifyCartOwnership($cartItem['cart_id'])) {
                continue;
            }

            // Check product stock
            $product = $this->productModel->find($cartItem['product_id']);
            
            if ($quantity > $product['stock']) {
                $errors[] = "{$product['name']} exceeds available stock.";
                continue;
            }
            
            if ($quantity > 0) {
                $this->cartItemModel->update($itemId, ['quantity' => $quantity]);
            } else {
                $this->cartItemModel->delete($itemId);
            }
        }

        if (!empty($errors)) {
            return redirect()->to('cart')->with('error', implode('<br>', $errors));
        }

        return redirect()->to('cart')->with('success', 'Cart updated successfully.');
    }
}
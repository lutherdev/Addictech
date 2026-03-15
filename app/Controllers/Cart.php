<?php

namespace App\Controllers;

use App\Models\CartItemModel;
use App\Models\Products_Model;

class Cart extends BaseController
{
    public function viewCart()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login')->with('error', 'Please login first.');
        }

        $cartModel  = model('CartItemModel');
        $user_id    = $session->get('user_id');
        $cart_items = $cartModel->getCartByUser($user_id);
        $total      = $cartModel->getCartTotal($user_id);
        $count      = $cartModel->getCartCount($user_id);

        return view('view_cart', [
            'cart_items' => $cart_items,
            'total'      => $total,
            'count'      => $count,
        ]);
    }

   public function add()
{
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('login')->with('error', 'Please login first.');
    }

    $product_id = $this->request->getPost('product_id');
    $quantity   = (int) ($this->request->getPost('quantity') ?? 1);

    $productModel = new Products_Model();
    $product      = $productModel->find($product_id);

    if (!$product || $product['stock'] <= 0) {
        $session->setFlashData('error', 'Product is not available.');
        return redirect()->back();
    }

    $cartModel = model('CartItemModel');
    $cartModel->addOrUpdate($session->get('user_id'), $product_id, $quantity);

    $session->setFlashData('success', $product['name'] . ' added to cart.');
    return redirect()->back();
}

    public function remove($id)
    {
        $session   = session();
        $cartModel = model('CartItemModel');
        $cartModel->removeItem($id, $session->get('user_id'));

        $session->setFlashData('success', 'Item removed from cart.');
        return redirect()->to('cart');
    }

    public function update($id)
    {
        $session   = session();
        $quantity  = (int) $this->request->getPost('quantity');
        $cartModel = model('CartItemModel');
        $cartModel->updateQuantity($id, $session->get('user_id'), $quantity);

        return redirect()->to('cart');
    }
}
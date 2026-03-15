<?php

namespace App\Controllers;

use App\Models\Orders_model;
use App\Models\OrderItemModel;
use App\Models\CartItemModel;
use App\Models\Products_Model;

class Orders extends BaseController
{
    public function index()
    {
        // $check = $this->checkLogin();
        //if ($check) return $check;
        $session = session();
        $userId = $session->get('user_id');
        $ordersModel     = model('Orders_model');
        $data = [
            'title' => 'My Orders',
            'orders' => $ordersModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll()
        ];
        
        return view('view_admin_order', $data);
    }

    public function checkout()
{
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('login')->with('error', 'Please login first.');
    }

    $user_id    = $session->get('user_id');
    $buy_now    = $session->get('buy_now');

    if ($buy_now) {
        // Direct buy flow
        $cart_items = [$buy_now];
    } else {
        // Cart flow
        $cartModel  = model('CartItemModel');
        $cart_items = $cartModel->getCartByUser($user_id);
    }

    if (empty($cart_items)) {
        return redirect()->to('cart')->with('error', 'Nothing to checkout.');
    }

    $subtotal = array_reduce($cart_items, function ($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);

    $data = [
        'cart_items'  => $cart_items,
        'subtotal'    => $subtotal,
        'is_buy_now'  => (bool) $buy_now,
        'user'        => $session->get(),
    ];

    return view('view_checkout', $data);
}

public function placeOrder()
{
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('login')->with('error', 'Please login first.');
    }

    $user_id         = $session->get('user_id');
    $buy_now         = $session->get('buy_now');
    $cartModel       = model('CartItemModel');
    $ordersModel     = model('Orders_model');
    $orderItemsModel = model('OrderItemModel');
    $productModel    = new Products_Model();

    // Determine which flow
    if ($buy_now) {
        $cart_items = [$buy_now];
    } else {
        $cart_items = $cartModel->getCartByUser($user_id);
    }

    if (empty($cart_items)) {
        return redirect()->to('cart')->with('error', 'Nothing to checkout.');
    }

    // Validate stock
    foreach ($cart_items as $item) {
        $product = $productModel->find($item['product_id']);
        if (!$product || $product['stock'] < $item['quantity']) {
            $session->setFlashData('error', $item['name'] . ' does not have enough stock.');
            return redirect()->to('cart');
        }
    }

    $delivery_method  = $this->request->getPost('delivery_method');
    $payment_method   = $this->request->getPost('payment_method');
    $delivery_address = $this->request->getPost('delivery_address');
    $notes            = $this->request->getPost('notes');
    $shipping_fee     = $this->calculateShipping($delivery_method, $delivery_address);

    $subtotal = array_reduce($cart_items, function ($sum, $item) {
        return $sum + ($item['price'] * $item['quantity']);
    }, 0);

    $order_id = $ordersModel->insert([
        'user_id'          => $user_id,
        'order_number'     => $ordersModel->generateOrderNumber(),
        'status'           => 'pending',
        'payment_method'   => $payment_method,
        'delivery_method'  => $delivery_method,
        'delivery_address' => $delivery_address,
        'subtotal'         => $subtotal,
        'shipping_fee'     => $shipping_fee,
        'total'            => $subtotal + $shipping_fee,
        'payment_status'   => 'unpaid',
        'notes'            => $notes,
    ]);

    $orderItemsModel->insertFromCart($order_id, $cart_items);

    // Deduct stock
    foreach ($cart_items as $item) {
        $product = $productModel->find($item['product_id']);
        $productModel->update($item['product_id'], [
            'stock' => $product['stock'] - $item['quantity'],
        ]);
    }

    // Clear whichever flow was used
    if ($buy_now) {
        $session->remove('buy_now');
    } else {
        $cartModel->clearCart($user_id);
    }

    $session->setFlashData('success', 'Order placed successfully! ORDER ID:' . $order_id);
    return redirect()->to('wishlist');
}

// Direct buy — stores single item in session then redirects to checkout
    public function buyNow()
{
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('login')->with('error', 'Please login first.');
    }

    $product_id = $this->request->getPost('product_id');
    $quantity   = (int) ($this->request->getPost('quantity') ?? 1);

    $productModel = new Products_Model();
    $product      = $productModel->find($product_id);

    if (!$product || $product['stock'] < $quantity) {
        $session->setFlashData('error', 'Product is not available.');
        return redirect()->back();
    }

    $session->set('buy_now', [
        'product_id' => $product['id'],
        'name'       => $product['name'],
        'variant'    => $product['variant'],
        'price'      => $product['price'],
        'quantity'   => $quantity,
        'image'      => $product['image'],
        'category'   => $product['category'],
    ]);

    return redirect()->to('checkout');
}

    // Order confirmation page
    public function confirmation($order_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $ordersModel = model('Orders_model');
        $order       = $ordersModel->getOrderWithItems($order_id);

        // Prevent users from viewing other people's orders
        if (!$order || $order['user_id'] != $session->get('user_id')) {
            return redirect()->to('orders');
        }

        return view('view_order_confirmation', ['order' => $order]);
    }

    // User's order history
    public function userOrder()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $ordersModel = model('Orders_model');
        $orders      = $ordersModel->getOrdersByUser($session->get('user_id'));

        return view('view_orders', ['orders' => $orders]);
    }

    // Single order detail view
    public function view($order_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $ordersModel = model('Orders_model');
        $order       = $ordersModel->getOrderWithItems($order_id);

        if (!$order || $order['user_id'] != $session->get('user_id')) {
            return redirect()->to('orders');
        }

        return view('view_order_detail', ['order' => $order]);
    }

    // Cancel an order (only if still pending)
    public function cancel($order_id)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('login');
        }

        $ordersModel     = model('Orders_model');
        $productModel    = new Products_Model();
        $orderItemsModel = model('OrderItemModel');

        $order = $ordersModel->find($order_id);

        if (!$order || $order['user_id'] != $session->get('user_id')) {
            return redirect()->to('orders');
        }

        if ($order['status'] !== 'pending') {
            $session->setFlashData('error', 'Only pending orders can be cancelled.');
            return redirect()->to('orders/view/' . $order_id);
        }

        // Restore stock
        $items = $orderItemsModel->getItemsByOrder($order_id);
        foreach ($items as $item) {
            if ($item['product_id']) {
                $product = $productModel->find($item['product_id']);
                if ($product) {
                    $productModel->update($item['product_id'], [
                        'stock' => $product['stock'] + $item['quantity'],
                    ]);
                }
            }
        }

        $ordersModel->updateStatus($order_id, 'cancelled');
        $session->setFlashData('success', 'Order cancelled successfully.');
        return redirect()->to('orders');
    }

    // ── ADMIN ──────────────────────────────────────────

    public function adminIndex()
    {
        // $session = session();
        // if ($session->get('role') !== 'admin') {
        //     return redirect()->to('login');
        // }

        $ordersModel = model('Orders_model');
        $data['orders'] = $ordersModel->getAllWithUser();
        return view('view_admin_order', $data);
    }

    public function adminView($order_id)
    {
        // $session = session();
        // if ($session->get('role') !== 'admin') {
        //     return redirect()->to('login');
        // }

        $ordersModel = model('Orders_model');
        $order       = $ordersModel->getOrderWithItems($order_id);

        if (!$order) {
            return redirect()->to('admin/orders');
        }

        return view('view_admin_order_detail', ['order' => $order]);
    }

    public function adminEdit($order_id)
    {
        // $session = session();
        // if ($session->get('role') !== 'admin') {
        //     return redirect()->to('login');
        // }

        $ordersModel = model('Orders_model');
        $order       = $ordersModel->getOrderWithItems($order_id);

        if (!$order) {
            return redirect()->to('admin/orders');
        }

        return view('view_adminEdit_order', ['order' => $order]);
    }

    public function adminUpdateStatus($order_id)
    {
        $session = session();
        // if ($session->get('role') !== 'admin') {
        //     return redirect()->to('login');
        // }

        $ordersModel = model('Orders_model');
        $status      = $this->request->getPost('status');

        $allowed = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        if (!in_array($status, $allowed)) {
            $session->setFlashData('error', 'Invalid status.');
            return redirect()->to('admin/orders/view/' . $order_id);
        }

        $ordersModel->updateStatus($order_id, $status);
        $session->setFlashData('success', 'Order status updated.');
        return redirect()->to('admin/orders/view/' . $order_id);
    }

    public function adminUpdatePayment($order_id)
    {
        $session = session();
        if ($session->get('role') !== 'admin') {
            return redirect()->to('login');
        }

        $ordersModel   = model('Orders_model');
        $payment_status = $this->request->getPost('payment_status');

        $allowed = ['unpaid', 'paid', 'refunded'];
        if (!in_array($payment_status, $allowed)) {
            $session->setFlashData('error', 'Invalid payment status.');
            return redirect()->to('admin/orders/view/' . $order_id);
        }

        $ordersModel->updatePaymentStatus($order_id, $payment_status);
        $session->setFlashData('success', 'Payment status updated.');
        return redirect()->to('admin/orders/view/' . $order_id);
    }

    // ── HELPERS ────────────────────────────────────────

    private function calculateShipping($delivery_method, $delivery_address)
    {
        // Basic shipping logic — adjust as needed
        if ($delivery_method === 'pickup') return 0;
        if ($delivery_method === 'express') return 250;

        // Standard — free above ₱5000 subtotal handled in view,
        // base rate here is just the flat fee
        return 150;
    }
}
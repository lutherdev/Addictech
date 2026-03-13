<?php

namespace App\Controllers;

use App\Models\OrderModel;
use App\Models\OrderItemModel;
use App\Models\CartModel;
use App\Models\CartItemModel;
use App\Models\ProductModel;

class Order extends BaseController
{
    protected $session;
    protected $orderModel;
    protected $orderItemModel;
    protected $cartModel;
    protected $cartItemModel;
    protected $productModel;

    public function __construct()
    {
        $this->session = session();
        $this->orderModel = model('OrderModel');
        $this->orderItemModel = model('OrderItemModel');
        $this->cartModel = model('CartModel');
        $this->cartItemModel = model('CartItemModel');
        $this->productModel = model('Products_model');
    }

    private function checkLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }
        return null;
    }

    public function index()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        
        $data = [
            'title' => 'My Orders',
            'orders' => $this->orderModel->where('user_id', $userId)->orderBy('created_at', 'DESC')->findAll()
        ];
        
        return view('orders/index', $data);
    }

    public function viewcatalog()
    {
        $userId = $this->session->get('user_id');
        
        $data = [
            'title' => 'Catalog',
            //'products' => $this->productModel->findAll()
        ];
        
        return view('view_catalog', $data);
    }

    public function checkout()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        $cart = $this->cartModel->where('user_id', $userId)->first();
        
        if (!$cart) {
            return redirect()->to('cart')->with('error', 'Your cart is empty.');
        }

        $cartItems = $this->cartItemModel->getCartItemsWithProducts($cart['id']);
        
        if (empty($cartItems)) {
            return redirect()->to('cart')->with('error', 'Your cart is empty.');
        }

        // Check stock availability
        foreach ($cartItems as $item) {
            if ($item['quantity'] > $item['stock']) {
                return redirect()->to('cart')->with('error', "Insufficient stock for {$item['name']}. Available: {$item['stock']}");
            }
        }

        $data = [
            'title' => 'Checkout',
            'cart_items' => $cartItems,
            'total' => $this->cartItemModel->getCartTotal($cart['id'])
        ];
        
        return view('orders/checkout', $data);
    }

    public function place()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        $cart = $this->cartModel->where('user_id', $userId)->first();
        
        if (!$cart) {
            return redirect()->to('cart')->with('error', 'Your cart is empty.');
        }

        $cartItems = $this->cartItemModel->getCartItemsWithProducts($cart['id']);
        
        if (empty($cartItems)) {
            return redirect()->to('cart')->with('error', 'Your cart is empty.');
        }

        // Calculate total
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Begin transaction
        $db = db_connect();
        $db->transStart();

        try {
            // Create order
            $orderData = [
                'user_id' => $userId,
                'total_price' => $total,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $this->orderModel->insert($orderData);
            $orderId = $this->orderModel->insertID();

            // Create order items and update stock
            foreach ($cartItems as $item) {
                // Add to order items
                $this->orderItemModel->insert([
                    'order_id' => $orderId,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price']
                ]);

                // Update product stock
                $newStock = $item['stock'] - $item['quantity'];
                $this->productModel->update($item['product_id'], ['stock' => $newStock]);
            }

            // Clear cart
            $this->cartItemModel->where('cart_id', $cart['id'])->delete();

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('order/success/' . $orderId)->with('success', 'Order placed successfully!');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('order/checkout')->with('error', 'Failed to place order. Please try again.');
        }
    }

    public function success($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        
        $order = $this->orderModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$order) {
            return redirect()->to('order')->with('error', 'Order not found.');
        }

        $data = [
            'title' => 'Order Success',
            'order' => $order
        ];
        
        return view('orders/success', $data);
    }

    public function view($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        
        $order = $this->orderModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$order) {
            return redirect()->to('order')->with('error', 'Order not found.');
        }

        $data = [
            'title' => 'Order Details',
            'order' => $order,
            'order_items' => $this->orderItemModel->getOrderItemsWithProducts($id)
        ];
        
        return view('orders/view', $data);
    }

    public function cancel($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        
        $order = $this->orderModel->where('id', $id)->where('user_id', $userId)->first();
        
        if (!$order) {
            return redirect()->to('order')->with('error', 'Order not found.');
        }

        // Only pending orders can be cancelled
        if ($order['status'] !== 'pending') {
            return redirect()->to('order/view/' . $id)->with('error', 'This order cannot be cancelled.');
        }

        // Begin transaction
        $db = db_connect();
        $db->transStart();

        try {
            // Update order status
            $this->orderModel->update($id, ['status' => 'cancelled']);

            // Restore stock
            $orderItems = $this->orderItemModel->where('order_id', $id)->findAll();
            foreach ($orderItems as $item) {
                $product = $this->productModel->find($item['product_id']);
                $newStock = $product['stock'] + $item['quantity'];
                $this->productModel->update($item['product_id'], ['stock' => $newStock]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Transaction failed');
            }

            return redirect()->to('order')->with('success', 'Order cancelled successfully.');
            
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->to('order/view/' . $id)->with('error', 'Failed to cancel order.');
        }
    }
}
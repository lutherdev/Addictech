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
        $this->orderModel = model('Orders_Model');
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

            $this->orderItemModel->insertFromCart($orderId, $cartItems);

            foreach ($cartItems as $item) {
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
            'order_items' => $this->orderItemModel->getItemsByOrder($id)
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

    // ======================== ADMIN FUNCTIONS ========================
 
    public function adminIndex()
    {
        // $check = $this->checkLogin();
        // if ($check) return $check;
 
        $db = db_connect();
 
        // Join orders with users and order_items to get all needed columns
        $orders = $db->table('orders o')
            ->select('
                o.id,
                o.total_price,
                o.status,
                o.created_at,
                CONCAT(u.first_name, " ", u.last_name) AS username,
                p.name    AS product_name,
                p.image   AS product_image,
                SUM(oi.quantity) AS total_quantity
            ')
            ->join('users u',       'u.id = o.user_id',          'left')
            ->join('order_items oi', 'oi.order_id = o.id',        'left')
            ->join('products p',    'p.id = oi.product_id',       'left')
            ->groupBy('o.id, p.id')
            ->orderBy('o.created_at', 'DESC')
            ->get()
            ->getResultArray();
 
        $data = [
            'title'  => 'Admin – Orders',
            'orders' => $orders
        ];
 
        return view('view_admin_order', $data);
    }
 
    public function adminView($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $db = db_connect();

        $order = $db->table('orders o')
            ->select('o.*, u.email, CONCAT(u.first_name, " ", u.last_name) AS username')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->where('o.id', $id)
            ->get()
            ->getRowArray();

        if (!$order) {
            return redirect()->to('admin/orders')->with('error', 'Order not found.');
        }

        $orderItemModel = new \App\Models\OrderItemModel();
        $orderItems = $orderItemModel->getItemsByOrder($id);

        $data = [
            'title'       => 'View Order',
            'order'       => $order,
            'order_items' => $orderItems
        ];

        return view('view_adminView_order', $data);
    }
 
    public function adminUpdateView($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;
 
        $db = db_connect();
 
        $order = $db->table('orders o')
            ->select('o.*, u.email, CONCAT(u.first_name, " ", u.last_name) AS username')
            ->join('users u', 'u.id = o.user_id', 'left')
            ->where('o.id', $id)
            ->get()
            ->getRowArray();
 
        if (!$order) {
            return redirect()->to('admin/orders')->with('error', 'Order not found.');
        }
 
        $data = [
            'title' => 'Update Order',
            'order' => $order
        ];
 
        return view('view_adminEdit_order', $data);
    }
 
    public function adminUpdate($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $orderModel     = $this->orderModel;
        $orderItemModel = $this->orderItemModel;
        $productModel   = $this->productModel;

        $status = $this->request->getPost('status');
        $allowedStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];

        if (!in_array($status, $allowedStatuses)) {
            return redirect()->to('admin/orders/update/' . $id)->with('error', 'Invalid status selected.');
        }

        $order = $orderModel->find($id);

        if (!$order) {
            return redirect()->to('admin/orders')->with('error', 'Order not found.');
        }

        if ($status === 'cancelled' && $order['status'] !== 'cancelled') {
            $db = db_connect();
            $db->transStart();

            try {
                $orderModel->update($id, ['status' => 'cancelled']);

                $orderItems = $orderItemModel->where('order_id', $id)->findAll();
                foreach ($orderItems as $item) {
                    $product  = $productModel->find($item['product_id']);
                    $newStock = $product['stock'] + $item['quantity'];
                    $productModel->update($item['product_id'], ['stock' => $newStock]);
                }

                $db->transComplete();

                if ($db->transStatus() === false) {
                    throw new \Exception('Transaction failed');
                }

            } catch (\Exception $e) {
                $db->transRollback();
                return redirect()->to('admin/orders/update/' . $id)->with('error', 'Failed to cancel order.');
            }
        } else {
            $orderModel->update($id, ['status' => $status]);
        }

        return redirect()->to('admin/orders')->with('success', 'Order status updated successfully.');
    }
 
    public function adminDelete($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;
 
        $order = $this->orderModel->find($id);
 
        if (!$order) {
            return redirect()->to('admin/orders')->with('error', 'Order not found.');
        }
 
        // Delete order items first, then the order
        $this->orderItemModel->where('order_id', $id)->delete();
        $this->orderModel->delete($id);
 
        return redirect()->to('admin/orders')->with('success', 'Order deleted successfully.');
    }
 
}
<?php

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;

class OrderItem extends BaseController
{
    protected $session;
    protected $orderItemModel;
    protected $orderModel;

    public function __construct()
    {
        $this->session = session();
        $this->orderItemModel = model('OrderItemModel');
        $this->orderModel = model('OrderModel');
    }

    private function checkLogin()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please login first.');
        }
        return null;
    }

    private function checkOrderOwnership($orderId)
    {
        $userId = $this->session->get('user_id');
        $order = $this->orderModel->where('id', $orderId)->where('user_id', $userId)->first();
        
        if (!$order) {
            return false;
        }
        
        return true;
    }

    public function index($orderId)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        // Verify order ownership
        if (!$this->checkOrderOwnership($orderId)) {
            return redirect()->to('order')->with('error', 'Order not found.');
        }

        $data = [
            'title' => 'Order Items',
            'order_id' => $orderId,
            'order_items' => $this->orderItemModel->getOrderItemsWithProducts($orderId)
        ];
        
        return view('order_items/index', $data);
    }

    public function view($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $orderItem = $this->orderItemModel->getOrderItemWithProduct($id);
        
        if (!$orderItem) {
            return redirect()->to('order')->with('error', 'Order item not found.');
        }

        // Verify order ownership
        if (!$this->checkOrderOwnership($orderItem['order_id'])) {
            return redirect()->to('order')->with('error', 'Order not found.');
        }

        $data = [
            'title' => 'Order Item Details',
            'order_item' => $orderItem
        ];
        
        return view('order_items/view', $data);
    }
}
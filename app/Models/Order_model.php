<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'user_id',
        'total_price',
        'status',
        'created_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
        'user_id' => 'required|integer|is_not_unique[users.id]',
        'total_price' => 'required|numeric|greater_than[0]',
        'status' => 'permit_empty|in_list[pending,processing,shipped,completed,cancelled]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
            'is_not_unique' => 'User does not exist'
        ],
        'total_price' => [
            'required' => 'Total price is required',
            'greater_than' => 'Total price must be greater than 0'
        ]
    ];
    
    protected $skipValidation = false;

    // Relationships
    public function user()
    {
        return $this->belongsTo('App\Models\UserModel', 'user_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItemModel', 'order_id', 'id');
    }

    // Custom Methods
    public function getUserOrders($userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getOrderWithItems($orderId)
    {
        return $this->select('orders.*, 
                             COUNT(order_items.id) as total_items,
                             SUM(order_items.quantity) as total_quantity')
                    ->join('order_items', 'order_items.order_id = orders.id', 'left')
                    ->where('orders.id', $orderId)
                    ->groupBy('orders.id')
                    ->first();
    }

    public function getOrdersByStatus($status)
    {
        return $this->where('status', $status)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    public function getRecentOrders($limit = 10)
    {
        return $this->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getOrderStatistics()
    {
        $db = db_connect();
        
        $stats = [
            'total_orders' => $this->countAll(),
            'total_revenue' => $this->selectSum('total_price')->first()['total_price'] ?? 0,
            'pending_orders' => $this->where('status', 'pending')->countAllResults(),
            'processing_orders' => $this->where('status', 'processing')->countAllResults(),
            'completed_orders' => $this->where('status', 'completed')->countAllResults(),
            'cancelled_orders' => $this->where('status', 'cancelled')->countAllResults()
        ];
        
        // Get today's orders
        $stats['today_orders'] = $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults();
        $stats['today_revenue'] = $this->selectSum('total_price')
                                       ->where('DATE(created_at)', date('Y-m-d'))
                                       ->first()['total_price'] ?? 0;
        
        return $stats;
    }

    public function updateStatus($id, $status)
    {
        $allowedStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        
        if (in_array($status, $allowedStatuses)) {
            return $this->update($id, ['status' => $status]);
        }
        
        return false;
    }

    public function cancelOrder($id)
    {
        $order = $this->find($id);
        
        if ($order && $order['status'] === 'pending') {
            return $this->update($id, ['status' => 'cancelled']);
        }
        
        return false;
    }

    public function getUserOrderHistory($userId, $limit = null)
    {
        $query = $this->where('user_id', $userId)
                      ->orderBy('created_at', 'DESC');
        
        if ($limit) {
            $query->limit($limit);
        }
        
        return $query->findAll();
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class OrderItemModel extends Model
{
    protected $table = 'order_items';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'order_id',
        'product_id',
        'quantity',
        'price'
    ];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'order_id' => 'required|integer|is_not_unique[orders.id]',
        'product_id' => 'required|integer|is_not_unique[products.id]',
        'quantity' => 'required|integer|greater_than[0]',
        'price' => 'required|numeric|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'order_id' => [
            'required' => 'Order ID is required',
            'is_not_unique' => 'Order does not exist'
        ],
        'product_id' => [
            'required' => 'Product ID is required',
            'is_not_unique' => 'Product does not exist'
        ]
    ];
    
    protected $skipValidation = false;

    // Relationships
    public function order()
    {
        return $this->belongsTo('App\Models\OrderModel', 'order_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\ProductModel', 'product_id', 'id');
    }

    // Custom Methods
    public function getOrderItemsWithProducts($orderId)
    {
        return $this->select('order_items.*, 
                             products.name, 
                             products.image,
                             products.description')
                    ->join('products', 'products.id = order_items.product_id')
                    ->where('order_items.order_id', $orderId)
                    ->findAll();
    }

    public function getOrderItemWithProduct($id)
    {
        return $this->select('order_items.*, 
                             products.name, 
                             products.image,
                             products.description,
                             products.stock')
                    ->join('products', 'products.id = order_items.product_id')
                    ->where('order_items.id', $id)
                    ->first();
    }

    public function getOrderTotal($orderId)
    {
        $result = $this->select('SUM(price * quantity) as total')
                      ->where('order_id', $orderId)
                      ->first();
        
        return $result['total'] ?? 0;
    }

    public function getProductSales($productId)
    {
        $result = $this->select('SUM(quantity) as total_sold, SUM(price * quantity) as total_revenue')
                      ->where('product_id', $productId)
                      ->whereIn('order_id', function($builder) {
                          return $builder->select('id')
                                        ->from('orders')
                                        ->where('status !=', 'cancelled');
                      })
                      ->first();
        
        return [
            'total_sold' => $result['total_sold'] ?? 0,
            'total_revenue' => $result['total_revenue'] ?? 0
        ];
    }

    public function getDailySales($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        return $this->select('order_items.*, products.name')
                    ->join('products', 'products.id = order_items.product_id')
                    ->join('orders', 'orders.id = order_items.order_id')
                    ->where('DATE(orders.created_at)', $date)
                    ->where('orders.status !=', 'cancelled')
                    ->findAll();
    }

    public function getTopSellingProducts($limit = 10)
    {
        return $this->select('products.id, products.name, products.image, 
                             SUM(order_items.quantity) as total_sold,
                             SUM(order_items.price * order_items.quantity) as total_revenue')
                    ->join('products', 'products.id = order_items.product_id')
                    ->join('orders', 'orders.id = order_items.order_id')
                    ->where('orders.status !=', 'cancelled')
                    ->groupBy('order_items.product_id')
                    ->orderBy('total_sold', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getItemsByOrderStatus($status)
    {
        return $this->select('order_items.*, orders.status as order_status, products.name')
                    ->join('orders', 'orders.id = order_items.order_id')
                    ->join('products', 'products.id = order_items.product_id')
                    ->where('orders.status', $status)
                    ->findAll();
    }

    public function getOrderItemsSummary($orderId)
    {
        $items = $this->getOrderItemsWithProducts($orderId);
        $summary = [
            'items' => $items,
            'subtotal' => 0,
            'total_quantity' => 0
        ];
        
        foreach ($items as $item) {
            $summary['subtotal'] += $item['price'] * $item['quantity'];
            $summary['total_quantity'] += $item['quantity'];
        }
        
        return $summary;
    }
}
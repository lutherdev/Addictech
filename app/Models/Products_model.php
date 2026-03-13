<?php

namespace App\Models;

use CodeIgniter\Model;

class Products_Model extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'category',
        'name',
        'variant',
        'description',
        'price',
        'stock',
        'image',
        'status',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'category' => 'required|in_list[KEYBOARD,MOUSE,HEADSET,MONITOR,SPEAKER,WEB CAM]',
        'name'     => 'required|min_length[3]|max_length[150]',
        'variant'  => 'permit_empty|max_length[150]',
        'price'    => 'required|numeric|greater_than[0]',
        'stock'    => 'permit_empty|integer|greater_than_equal_to[0]',
    ];

    protected $validationMessages = [
        'category' => [
            'required' => 'Product category is required',
            'in_list'  => 'Invalid category selected'
        ],
        'name' => [
            'required'   => 'Product name is required',
            'min_length' => 'Product name must be at least 3 characters long',
            'max_length' => 'Product name cannot exceed 150 characters'
        ],
        'price' => [
            'required'     => 'Price is required',
            'numeric'      => 'Price must be a number',
            'greater_than' => 'Price must be greater than 0'
        ]
    ];

    protected $skipValidation = false;

   
}
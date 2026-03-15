<?php

namespace App\Controllers;

use App\Models\Products_Model;

class Products extends BaseController
{
    public function index()
    {
        // $session = session();
        // if (!$session->get('isLoggedIn')) {
        //     return redirect()->to('/login')->with('error', 'Please login first.');
        // }

        $productModel = new Products_Model();
        $data = [
            'title' => 'Product Management',
            'products' => $productModel->findAll()
        ];

        return view('view_admin_product', $data);
    }

    public function add()
    {
        return view('view_adminAdd_product');
    }

    public function insert()
    {
        $productModel = new Products_Model();
        $session = session();
        $validation = service('validation');

        $data = [
            'category'    => $this->request->getPost('category'),
            'name'        => $this->request->getPost('name'),
            'variant'     => $this->request->getPost('variant'),
            'description' => $this->request->getPost('description'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
            'image'       => $this->request->getPost('image'),
            'status'      => $this->request->getPost('status') ?? 'active',
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if (!$validation->run($data, 'product')) { // assumes you have a validation group 'product'
            $errors = implode('<br>', $validation->getErrors());
            $session->setFlashData('errors', $errors);
            return redirect()->to('products/add');
        }

        $productModel->insert($data);
        $session->setFlashData('success', 'Product added successfully.');
        return redirect()->to('products');
    }

    public function view($id)
    {
        $productModel = new Products_Model();
        $data = [
            'title'   => 'View Product',
            'product' => $productModel->find($id)
        ];

        return view('view_adminView_product', $data);
    }

    public function edit($id)
    {
        $productModel = new Products_Model();
        $session = session();
        $data = [
            'title'   => 'Edit Product',
            'product' => $productModel->find($id)
        ];

        return view('view_adminEdit_product', $data);
    }

    public function update($id)
    {
        $productModel = new Products_Model();
        $session = session();

        $data = [
            'category'    => $this->request->getPost('category'),
            'name'        => $this->request->getPost('name'),
            'variant'     => $this->request->getPost('variant'),
            'description' => $this->request->getPost('description'),
            'price'       => $this->request->getPost('price'),
            'stock'       => $this->request->getPost('stock'),
            'image'       => $this->request->getPost('image'),
            'status'      => $this->request->getPost('status'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];

        $productModel->update($id, $data);
        $session->setFlashData('success', 'Product updated successfully.');
        return redirect()->to('products');
    }

    public function delete($id)
    {
        $productModel = new Products_Model();
        $productModel->delete($id);
        return redirect()->to('products')->with('success', 'Product deleted successfully.');
    }

    public function statusChangeView()
    {
        $productModel = new Products_Model();
        $data['products'] = $productModel->findAll();
        return view('products_status', $data);
    }

    public function statusChange()
    {
        $productModel = new Products_Model();
        $session = session();

        $id = $this->request->getPost('product_id');
        $status = $this->request->getPost('status');

        if (!$id || !$status) {
            return redirect()->back()->with('error', 'Invalid form submission.');
        }

        $productModel->update($id, [
            'status'     => strtolower($status),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('products')->with('success', 'Product status updated successfully.');
    }















     // Relationships
    public function cartItems()
    {
        return $this->hasMany('App\Models\CartItemModel', 'product_id', 'id');
    }

    public function orderItems()
    {
        return $this->hasMany('App\Models\OrderItemModel', 'product_id', 'id');
    }

    public function favorites()
    {
        return $this->hasMany('App\Models\FavoriteModel', 'product_id', 'id');
    }

    // Custom Methods
    public function getActiveProducts()
    {
        return $this->where('status', 'active')
                    ->where('stock >', 0)
                    ->findAll();
    }

    public function getLowStockProducts($threshold = 5)
    {
        return $this->where('stock <=', $threshold)
                    ->where('stock >', 0)
                    ->findAll();
    }

    public function getOutOfStockProducts()
    {
        return $this->where('stock', 0)
                    ->orWhere('stock IS NULL')
                    ->findAll();
    }

    public function searchProducts($keyword)
    {
        return $this->like('name', $keyword)
                    ->orLike('description', $keyword)
                    ->where('status', 'active')
                    ->findAll();
    }

    public function getProductsByCategory($category)
    {
        return $this->where('category', $category)
                    ->where('status', 'active')
                    ->findAll();
    }

    public function updateStock($id, $quantity)
    {
        $product = $this->find($id);
        if ($product) {
            $newStock = $product['stock'] - $quantity;
            return $this->update($id, ['stock' => $newStock]);
        }
        return false;
    }

    public function isInStock($id, $quantity = 1)
    {
        $product = $this->find($id);
        return $product && $product['stock'] >= $quantity;
    }
}
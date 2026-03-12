<?php

namespace App\Controllers;

use App\Models\ProductModel;
use App\Models\CategoryModel;

class Product extends BaseController
{
    protected $session;
    protected $productModel;
    protected $categoryModel;

    public function __construct()
    {
        $this->session = session();
        $this->productModel = model('ProductModel');
        $this->categoryModel = model('CategoryModel');
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

        $data = [
            'title' => 'Products List',
            'products' => $this->productModel->getProductsWithCategory(),
            'categories' => $this->categoryModel->findAll()
        ];
        
        return view('products/index', $data);
    }

    public function add()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $data = [
            'title' => 'Add New Product',
            'categories' => $this->categoryModel->findAll()
        ];
        
        return view('products/add', $data);
    }

    public function insert()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $validation = service('validation');
        
        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'stock' => $this->request->getPost('stock'),
            'status' => $this->request->getPost('status') ?? 'active',
            'created_at' => date('Y-m-d H:i:s')
        ];

        // Handle image upload
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move('uploads/products', $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        if (!$validation->run($data, 'productValidation')) {
            $this->session->setFlashData('errors', implode('<br>', $validation->getErrors()));
            return redirect()->back()->withInput();
        }

        $this->productModel->insert($data);
        $this->session->setFlashData('success', 'Product added successfully.');
        return redirect()->to('product');
    }

    public function edit($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $data = [
            'title' => 'Edit Product',
            'product' => $this->productModel->find($id),
            'categories' => $this->categoryModel->findAll()
        ];
        
        return view('products/edit', $data);
    }

    public function update($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $data = [
            'category_id' => $this->request->getPost('category_id'),
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'price' => $this->request->getPost('price'),
            'stock' => $this->request->getPost('stock'),
            'status' => $this->request->getPost('status'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Handle image upload
        $file = $this->request->getFile('image');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Delete old image
            $oldProduct = $this->productModel->find($id);
            if ($oldProduct['image'] && file_exists($oldProduct['image'])) {
                unlink($oldProduct['image']);
            }
            
            $newName = $file->getRandomName();
            $file->move('uploads/products', $newName);
            $data['image'] = 'uploads/products/' . $newName;
        }

        $this->productModel->update($id, $data);
        $this->session->setFlashData('success', 'Product updated successfully.');
        return redirect()->to('product');
    }

    public function delete($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        // Delete product image
        $product = $this->productModel->find($id);
        if ($product['image'] && file_exists($product['image'])) {
            unlink($product['image']);
        }

        $this->productModel->delete($id);
        $this->session->setFlashData('success', 'Product deleted successfully.');
        return redirect()->to('product');
    }

    public function view($id)
    {
        $data = [
            'title' => 'Product Details',
            'product' => $this->productModel->getProductWithCategory($id)
        ];
        
        return view('products/view', $data);
    }

    public function search()
    {
        $keyword = $this->request->getGet('keyword');
        
        $data = [
            'title' => 'Search Results',
            'products' => $this->productModel->searchProducts($keyword),
            'keyword' => $keyword
        ];
        
        return view('products/search', $data);
    }

    public function byCategory($categoryId)
    {
        $data = [
            'title' => 'Products by Category',
            'products' => $this->productModel->where('category_id', $categoryId)->findAll(),
            'category' => $this->categoryModel->find($categoryId)
        ];
        
        return view('products/category', $data);
    }
}
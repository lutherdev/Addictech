<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class Category extends BaseController
{
    protected $session;
    protected $categoryModel;
    protected $productModel;

    public function __construct()
    {
        $this->session = session();
        $this->categoryModel = model('CategoryModel');
        $this->productModel = model('ProductModel');
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
            'title' => 'Categories List',
            'categories' => $this->categoryModel->findAll()
        ];
        
        return view('categories/index', $data);
    }

    public function add()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        return view('categories/add', ['title' => 'Add New Category']);
    }

    public function insert()
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description'),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->categoryModel->insert($data);
        $this->session->setFlashData('success', 'Category added successfully.');
        return redirect()->to('category');
    }

    public function edit($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $data = [
            'title' => 'Edit Category',
            'category' => $this->categoryModel->find($id)
        ];
        
        return view('categories/edit', $data);
    }

    public function update($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $data = [
            'name' => $this->request->getPost('name'),
            'description' => $this->request->getPost('description')
        ];

        $this->categoryModel->update($id, $data);
        $this->session->setFlashData('success', 'Category updated successfully.');
        return redirect()->to('category');
    }

    public function delete($id)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        // Check if category has products
        $products = $this->productModel->where('category_id', $id)->findAll();
        if (count($products) > 0) {
            $this->session->setFlashData('error', 'Cannot delete category with existing products.');
            return redirect()->to('category');
        }

        $this->categoryModel->delete($id);
        $this->session->setFlashData('success', 'Category deleted successfully.');
        return redirect()->to('category');
    }

    public function view($id)
    {
        $data = [
            'title' => 'Category Details',
            'category' => $this->categoryModel->find($id),
            'products' => $this->productModel->where('category_id', $id)->findAll()
        ];
        
        return view('categories/view', $data);
    }
}
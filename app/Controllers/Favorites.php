<?php

namespace App\Controllers;

use App\Models\FavoriteModel;
use App\Models\ProductModel;

class Favorite extends BaseController
{
    protected $session;
    protected $favoriteModel;
    protected $productModel;

    public function __construct()
    {
        $this->session = session();
        $this->favoriteModel = model('FavoriteModel');
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

        $userId = $this->session->get('user_id');
        
        $data = [
            'title' => 'My Favorites',
            'favorites' => $this->favoriteModel->getFavoritesWithProducts($userId)
        ];
        
        return view('favorites/index', $data);
    }

    public function add($productId)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        
        // Check if product exists
        $product = $this->productModel->find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }
        
        // Check if already in favorites
        $existing = $this->favoriteModel
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if (!$existing) {
            $this->favoriteModel->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            return redirect()->back()->with('success', 'Product added to favorites.');
        }
        
        return redirect()->back()->with('info', 'Product already in favorites.');
    }

    public function remove($productId)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        
        $this->favoriteModel
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->delete();
        
        return redirect()->back()->with('success', 'Product removed from favorites.');
    }

    public function toggle($productId)
    {
        $check = $this->checkLogin();
        if ($check) return $check;

        $userId = $this->session->get('user_id');
        
        // Check if product exists
        $product = $this->productModel->find($productId);
        if (!$product) {
            return $this->response->setJSON(['success' => false, 'message' => 'Product not found.']);
        }
        
        // Check if already in favorites
        $existing = $this->favoriteModel
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();

        if ($existing) {
            // Remove from favorites
            $this->favoriteModel->delete($existing['id']);
            $isFavorite = false;
            $message = 'Removed from favorites';
        } else {
            // Add to favorites
            $this->favoriteModel->insert([
                'user_id' => $userId,
                'product_id' => $productId,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            $isFavorite = true;
            $message = 'Added to favorites';
        }
        
        return $this->response->setJSON([
            'success' => true,
            'is_favorite' => $isFavorite,
            'message' => $message
        ]);
    }

    public function check($productId)
    {
        $check = $this->checkLogin();
        if ($check) return $this->response->setJSON(['is_favorite' => false]);

        $userId = $this->session->get('user_id');
        
        $existing = $this->favoriteModel
            ->where('user_id', $userId)
            ->where('product_id', $productId)
            ->first();
        
        return $this->response->setJSON(['is_favorite' => !is_null($existing)]);
    }

    public function count()
    {
        $check = $this->checkLogin();
        if ($check) return $this->response->setJSON(['count' => 0]);

        $userId = $this->session->get('user_id');
        $count = $this->favoriteModel->where('user_id', $userId)->countAllResults();
        
        return $this->response->setJSON(['count' => $count]);
    }
}
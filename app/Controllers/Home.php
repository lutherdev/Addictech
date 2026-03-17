<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(){
        $role = session()->get('role'); 
        $usermodel = model('Users_model');
        // $itemmodel = model('Equipments_model');
        // $reservationmodel = model('Reservation_model');
        // $borrowmodel = model('Borrow_model');
        $userId = session()->get('user_id');
        $productModel = model("Products_Model");
        $products     = $productModel->where('status', 'active')->findAll();

        $featured_products = [];

        if (!empty($products)) {
            shuffle($products); // randomize order

            $count = 3; // 1 to 3, but not more than total
            $featured_products = array_slice($products, 0, $count);
        }
        $data = array(
            'title' => 'TW32 App - View User Record',
            'user' => $usermodel->find($userId),
            'featured_products'=>$featured_products,
        );

        // $data2 = array(
        //     'title' => 'TW32 App - View User Record',
        //     'reservations' => $reservationmodel
        //     ->select('tblreservations.*, tblusers.username, tblusers.first_name, tblusers.last_name, tblequipments.name as equipment_name')
        //     ->join('tblusers', 'tblusers.id = tblreservations.user_id')
        //     ->join('tblequipments', 'tblequipments.id = tblreservations.equipment_id')
        //     ->where('tblreservations.user_id', $userId)
        //     ->findAll(),

        //     'borrowers' => $borrowmodel
        //     ->select('tblborrow.*, tblusers.username, tblusers.first_name, tblusers.last_name, tblequipments.name as equipment_name')
        //     ->join('tblusers', 'tblusers.id = tblborrow.user_id')
        //     ->join('tblequipments', 'tblequipments.id = tblborrow.equipment_id')
        //     ->where('tblborrow.user_id', $userId)
        //     ->findAll(),
        // );

        // if ($role == 'customer') {
        //     return view('view_homepage');
        // } else if ($role == 'admin') {
        //     return view('view_user_profile', $data);
        // } else if ($role == 'God'){
        //     return view('view_homepage');
        // } else {
        //     if (session()->getFlashdata('error')) :
        //     return redirect()->to('login')->with('error', session()->getFlashdata('error'));
        //     elseif (session()->getFlashdata('success')) :
        //     return redirect()->to('login')->with('success', session()->getFlashdata('success'));
        //     else : return redirect()->to('login');
        //     endif;            
        // }
        return view('view_homepage', $data);
    }

    public function viewcatalog()
    {
        $session      = session();
        $productModel = model("Products_Model");
        $products     = $productModel->where('status', 'active')->findAll();

        $featured_products = [];

        if (!empty($products)) {
            shuffle($products); // randomize order

            $count = min(rand(1, 3), count($products)); // 1 to 3, but not more than total
            $featured_products = array_slice($products, 0, $count);
        }

        $wishlisted_ids = [];
        if ($session->get('isLoggedIn')) {
            $wishlistModel  = model('Wishlists_model');
            $wishlist       = $wishlistModel->getWishlistByUser($session->get('user_id'));
            $wishlisted_ids = array_map('intval', array_column($wishlist, 'product_id'));
        }

        return view('view_catalog', [
            'products'       => $products,
            'wishlisted_ids' => $wishlisted_ids,
        ]);
    }

    public function viewwishlist()
    {
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return redirect()->to('login')->with('error', 'Please login first.');
    }

    $wishlistModel = model('Wishlists_model');
    $user_id       = $session->get('user_id');
    $wishlist      = $wishlistModel->getWishlistByUser($user_id);

    // Map to product format the view expects
    $products = array_map(function($item) {
        return [
            'id'          => $item['product_id'],
            'name'        => $item['name'],
            'category'    => $item['category'],
            'price'       => $item['price'],
            'variant'     => $item['variant']     ?? '',
            'description' => $item['description'] ?? '',
            'stock'       => $item['stock'],
            'image'       => $item['image'],
        ];
    }, $wishlist);

    return view('view_wishlist', ['products' => $products]);
    }

    public function viewabout()
    {
        return view('view_aboutus');
    }
}

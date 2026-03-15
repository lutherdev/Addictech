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
        $data = array(
            'title' => 'TW32 App - View User Record',
            'user' => $usermodel->find($userId),
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
        return view('view_homepage');
    }

    public function viewcatalog()
    {
        $productModel = model('Products_model');
        $data = [
            'title' => 'Catalog',
            'products' => $productModel->findAll()
        ];
        
        return view('view_catalog', $data);
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
}

<?php

namespace App\Controllers;

use App\Models\OrderItemModel;
use App\Models\OrderModel;

class OrderItem extends BaseController
{
    public function viewcart() {  //
        // $usermodel = model('Users_model');

        // $data = array(
        //     'title' => 'TW32 App - View User Record',
        //     'user' => $usermodel->find($id)
        // );
        // return view('view_cart', $data); data of user's cart will be passed here
        return view('view_cart');
    }
}
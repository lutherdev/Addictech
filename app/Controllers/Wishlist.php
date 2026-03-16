<?php

namespace App\Controllers;

use App\Models\Wishlists_model;

class Wishlist extends BaseController
{
    public function toggle()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
            }
            return redirect()->to('login');
        }

        $product_id    = $this->request->getPost('product_id');
        $user_id       = $session->get('user_id');
        $wishlistModel = model('Wishlists_model');
        $wished        = $wishlistModel->toggle($user_id, $product_id);
        $count         = $wishlistModel->getWishlistCount($user_id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'wished'  => $wished,
                'count'   => $count,
            ]);
        }

        $session->setFlashData('success', $wished ? 'Added to wishlist.' : 'Removed from wishlist.');
        return redirect()->back();
    }

    public function remove()
    {
        $session = session();

        if (!$session->get('isLoggedIn')) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'Not logged in']);
            }
            return redirect()->to('login');
        }

        $product_id    = $this->request->getPost('product_id');
        $user_id       = $session->get('user_id');
        $wishlistModel = model('Wishlists_model');
        $wishlistModel->removeItem($user_id, $product_id);
        $count         = $wishlistModel->getWishlistCount($user_id);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'count'   => $count,
            ]);
        }

        $session->setFlashData('success', 'Removed from wishlist.');
        return redirect()->back();
    }
}
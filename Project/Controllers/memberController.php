<?php
namespace Project\Controllers;

use Ionian\Core\Controller;
use Ionian\Database\Database;

class MemberController extends Controller{

    public function showAllMembersAction (){
        //TODO Susanne
    }
    public function getMemberByIdAction($id){
        $stm = Database::get()->prepare("SELECT user_id, email FROM users WHERE user_id = :id");
        $stm->bindparam(":id",$id);
        $stm->execute();
        $this->outputJSON($stm->fetch());
    }
    public function getMemberByEmailAction($email){
        $stm = Database::get()->prepare("SELECT user_id,email FROM users WHERE email = :email");
        $stm->bindparam(":email",$email);
        $stm->execute();
        $this->outputJSON($stm->fetch());
    }

    public function getActiveUserCart(){
        $stm = Database::get()->prepare("SELECT * FROM cart WHERE cart_status = 0");
        $stm->execute();

        return $stm->fetchAll();

    }
    public function showMemberCartAction(){
        $carts = $this->getActiveUserCart();
        $cartsContent = [];

        //if there is a cart
        if (count($carts) > 0){

            foreach($carts as $cart){
                //select all products from it
                $stm = Database::get()->prepare("SELECT cart_products.cartproducts_cart_id,
                                                    products.product_id,
                                                    products.product_brand,
                                                    products.product_size,
                                                    products.product_price,
                                                    products.product_color
                        FROM cart_products JOIN products ON cart_products.cartproducts_product_id = products.product_id
                        WHERE cartproducts_cart_id = :cid");

                $stm->bindParam(":cid", $cart['cart_id']);
                $stm->execute();
                $cartsContent[] = $stm->fetchAll();
            }
        }

        $this->outputJSON("Here is a complete list of carts", $cartsContent);
    }
}


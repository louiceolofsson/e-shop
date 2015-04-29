<?php

namespace Project\Controllers;

use Ionian\Core\Controller;
use Ionian\Database\Database;

class CartController extends Controller{
    public function addToCartAction($prodID){
        session_start();

        $userCart = $this->getActiveUserCart();


        //if user has a cart
        if ($userCart !== false) {
            //get the cart ID
            $cartID = $userCart['cart_id'];
        }
        //if user does NOT have a cart
        else {
            //create a cart for him/her
            $cartID = $this->createCart();
        }

        //Insert
        $stm = Database::get()->prepare("
                INSERT INTO cart_products(
                    cartproducts_cart_id,
                    cartproducts_product_id
                )
                VALUES (
                    :cartproducts_cart_id,
                    :cartproducts_product_id
                )");

        $stm->bindParam(":cartproducts_cart_id", $cartID);
        $stm->bindParam(":cartproducts_product_id", $prodID);
        if ($stm->execute()){
            $this->outputJSON("Item added to cart");

        }
        else {
            $this->errorHandler->internalServerError();
        }
    }
    //denna körs per automatic när du skriver addToCart med en parameter för att addTo Cart kallar på dessa i den functionen
        //hämtar alla carts med status noll för den inloggade
    public function getActiveUserCart(){
        $stm = Database::get()->prepare("SELECT * FROM cart WHERE cart_user_id = :cart_user_id AND cart_status = 0");
        $stm->bindParam(":cart_user_id", $_SESSION["id"]);
        $stm->execute();

        return $stm->fetch();
    }
    //denna körs per automatic när du skriver addToCart med en parameter för att addTo Cart kallar på dessa i den functionen
//skapar en varukorg om det inte finns någon och tar det senaste cart_id
    public function createCart(){
        $stm = Database::get()->prepare("INSERT INTO cart(cart_user_id) VALUES (:cart_user_id)");
        $stm->bindParam(":cart_user_id", $_SESSION["id"]);
        $stm->execute();

        return Database::get()->lastInsertId();
    }



    public function showCartAction(){
        session_start();

        $cart = $this->getActiveUserCart();

        //if there is a cart
        if($cart !== false){
            //select all products from it
            $stm = Database::get()->prepare("SELECT cart_products.cartproducts_cart_id,
                                                    products.product_id,
                                                    products.product_brand,
                                                    products.product_size,
                                                    products.product_price,
                                                    products.product_color
            FROM cart_products JOIN products ON cart_products.cartproducts_product_id = products.product_id
            WHERE cartproducts_cart_id = :cid");
            $stm->bindParam(":cid", $cart["cart_id"]);
            $stm->execute();

            //output all the products to the user
            $this->outputJSON("Successfully selected cart content", $stm->fetchAll());

        }
        //if there is NO active cart available
        else{
            //output error msg
            $this->errorHandler->customError(400, "Bad Request", "You don't have an active cart");
        }

    }

    public function deleteProductAction($prodID) {
            $stm = Database::get()->prepare("DELETE FROM cart_products WHERE cartproducts_product_id = :delete_id");
            $stm->bindParam(":delete_id", $prodID);
            if ($stm->execute()) {
                $this->outputJSON("Successfully deleted");

            } else{
                $this->errorHandler->customError(400, "Bad Request", "could not delete product");

            }
        }


    public function checkOutAction() {
        session_start();
        $stm = Database::get()->prepare("  UPDATE cart
                                            SET cart_status=1
                                            WHERE cart_status = 0");

            if($stm->execute()){
                $this->outputJSON("Successfully checked out");
            }
        else {
            $this->errorHandler->customError(400, "Bad Request", "kjsdbfnjwkbfwjkefbw");
        }
    }
}
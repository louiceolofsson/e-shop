<?php
namespace Project\Controllers;

use Ionian\Core\Controller;
use Ionian\Database\Database;

class UserController extends Controller {

    public function writeNameAction($name, $lastName){
        $this->outputJSON("Welcome $name $lastName");

    }


    public function registerAction($email, $password){

        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errorHandler->customError(400, "Bad Request", "Email not valid!");
        }
        else {
            $options = [
                'cost' => 8,
            ];
            $h_password = password_hash($password, PASSWORD_BCRYPT, $options);

            $stm = Database::get()->prepare("INSERT INTO users(email, password) VALUES (:email, :password)");
            $stm->bindParam(":email", $email);
            $stm->bindParam(":password", $h_password);

            if($stm->execute()){
                $this->outputJSON("Welcome $email");
            }
            else{
                $this->errorHandler->internalServerError();
            }
        }
    }
/*
     public function registerAction($email, $password){

       if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
           echo "This ($email) email address is not considered valid.";
       }

       else {

    $options = [
                'cost' => 8,
            ];
                $h_password = password_hash($password, PASSWORD_BCRYPT, $options);

           $stm = Database::get()->prepare("INSERT INTO users(email, password) VALUES (:email, :password)");
           $res = $stm->execute([
               ":email" => $email,
               ":password" => $h_password
            ]);

           if($res){
               $this->outputJSON("Welcome $email");
           }
           else{
               $this->errorHandler->internalServerError();
           }
       }
   }
    */

    public function loginAction($email,$password){
        /*  Gets the hash-password from the db and puts the value inside $hash */

        $stm = Database::get()->prepare("SELECT password FROM users WHERE email = :email");
        $stm->bindParam(":email", $email);

        $stm->execute();
        $hash = $stm->fetchColumn();

        //verify it by comparing to the given passowrd
        if (password_verify($password, $hash)) {
            //$db = new PDO("mysql:host=localhost;dbname=theletterbox", "root", "");
            $stm = Database::get()->prepare("SELECT * FROM users WHERE email = :email AND password = :hash");
            $stm->execute(array(
                ":hash" => $hash,
                ":email" => $email
            ));

            //If we logged in the person without problems
            if ($stm->rowCount() == 1) {
                $row = $stm->fetch();

                session_start();
                $_SESSION["status"] = "inloggad";
                $_SESSION["email"] = $row["email"];
                $_SESSION["id"] = $row["user_id"];

                $this->outputJSON("Your login was successful!");
            }
            else {
                $this->errorHandler->unauthorized();
            }
        }
        else {
            $this->errorHandler->unauthorized();
        }
    }

    public function logoutAction(){
        session_start();
        session_unset();
        session_destroy();

        $this->outputJSON("Your logout was successful!");


    }






}


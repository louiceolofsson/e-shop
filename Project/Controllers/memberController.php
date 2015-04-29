<?php
namespace Project\Controllers;

use Ionian\Core\Controller;
use Ionian\Database\Database;

class MemberController extends Controller{

    public function showAllMembersAction (){
        //TODO Susanne
    }
    public function getMemberByIdAction($id){
        $stm->bindparam(":id",$id);
        $stm->execute();
        $this->outputJSON($stm->fetch());
    }
    public function getMemberByEmailAction(){
        //TODO Lina
    }
    public function showMemberCartAction(){
        //TODO Jennie
    }
}


<?php
namespace Lobby\App;

use Lobby\App\admin\Fr\LS;

class admin extends \Lobby\App {

  public function page($page){
    require_once $this->dir . "/src/inc/load.php";

    if($page === "/logout"){
      LS::logout();
    }else{
      return "auto";
    }
  }

}

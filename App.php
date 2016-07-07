<?php
namespace Lobby\App;

class admin extends \Lobby\App {
  
  public function page($page){
    if($page === "/logout"){
      \Fr\LS::logout();
      \Response::redirect("/admin/login");
    }else{
      return "auto";
    }
  }
  
}

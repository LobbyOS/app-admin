<?php
class logSysLobbyDB {
  public function prepare($query){
    $obj = \Lobby\DB::getDBH()->prepare($query);
    return $obj;
  }
}

require_once __DIR__ . "/class.logsys.php";

$salt = Lobby\DB::getOption("admin_secure_salt");
$cookie = Lobby\DB::getOption("admin_secure_cookie");

\Fr\LS::config(array(
  "db" => array(
    "table" => \Lobby\DB::getPrefix() . "users"
  ),
  "features" => array(
    "auto_init" => false,
    "start_session" => false,
    "email_login" => false,
  ),
  "keys" => array(
    "cookie" => $cookie,
    "salt" => $salt
  )
));

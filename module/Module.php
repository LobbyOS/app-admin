<?php
namespace Lobby\Module;

use Assets;
use Hooks;
use Lobby\App\admin\Fr\LS;
use Lobby\DB;
use Lobby\Modules;
use Lobby\UI\Panel;
use Response;

class app_admin extends \Lobby\Module {

  public function init(){
    if(\Lobby::status("lobby.assets-serve") === false){
      $this->install();
      $this->routes();

      require_once $this->app->dir . "/src/inc/load.php";

      if(LS::$loggedIn){
        /**
         * Logged In
         */
        Hooks::addAction("init", function(){
          /**
           * Add Change Password Item in Top Panel -> Admin before Log Out item
           * This is done by first removing the Log Out item, adding the Change
           * Password item and then adding back the Log Out item
           */
          \Lobby\UI\Panel::addTopItem('adminModule', array(
            "text" => "<img src='". $this->app->srcURL ."/src/image/logo.svg' style='width: 40px;height: 40px;' />",
            "href" => "/",
            "position" => "left",
            "subItems" => array(
              "changePassword" => array(
                "text" => "Change Password",
                "href" => "/app/admin/change-password",
              ),
              'LogOut' => array(
                "text" => "Log Out",
                "href" => "/app/admin/logout"
              )
            )
          ));
        });
      }else{
        /**
         * If `indi` module is active, make the index page available to everyone
         */
        if(!Modules::exists("app_indi")){
          if(\Lobby::curPage() != "/admin/login" && !\Lobby::status("lobby.install")){
            \Response::redirect("/admin/login");
          }
        }else{
          Panel::removeTopItem("indiModule", "left");
          if(\Lobby::curPage() != "/admin/login" && \Lobby::curPage() != "/admin/install.php" && substr(\Lobby::curPage(), 0, 6) == "/admin"){
            \Response::redirect("/admin/login");
          }
        }

        Hooks::addFilter("panel.top.left.items", function($left){
          unset($left["lobbyAdmin"]);
          if(Modules::exists("app_indi"))
            unset($left["indiModule"]);
          return $left;
        });

        Assets::removeJS("notify");
        Assets::removeCSS("notify");
      }
    }
  }
  /**
   * Install module
   * --------------
   * Create the `users` table
   */
  public function install(){
    if(DB::getOption("admin_installed") == null && \Lobby::$installed){
      /**
       * Install Module
       */
      $salt = \Helper::randStr(15);
      $cookie = \Helper::randStr(15);
      DB::saveOption("admin_secure_salt", $salt);
      DB::saveOption("admin_secure_cookie", $cookie);

      $prefix = DB::getPrefix();

      /**
       * Create `users` TABLE
       */
      if(DB::getType() === "mysql"){
        $sql = DB::getDBH()->prepare("CREATE TABLE IF NOT EXISTS `{$prefix}users` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `username` varchar(20) NOT NULL,
          `password` varchar(255) NOT NULL,
          `name` varchar(30) NOT NULL,
          `created` datetime NOT NULL,
          `attempt` varchar(15) NOT NULL DEFAULT '0',
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;");
      }else{
        $sql = DB::getDBH()->prepare("CREATE TABLE IF NOT EXISTS `{$prefix}users` (
          `id` INTEGER PRIMARY KEY AUTOINCREMENT,
          `username` varchar(20) NOT NULL,
          `password` varchar(255) NOT NULL,
          `name` varchar(30) NOT NULL,
          `created` datetime NOT NULL,
          `attempt` varchar(15) NOT NULL DEFAULT '0'
        );");
      }
      $sql->execute();
      DB::saveOption("admin_installed", "true");
    }
  }

  /**
   * Add routes
   */
  public function routes(){
    /**
     * Add the Login Page in /admin/login route
     */
    \Lobby\Router::route("/admin/login", function(){
      if(LS::userExists("admin") === false){
        LS::register("admin", "admin", array(
          "name" => "Admin",
          "created" => date("Y-m-d H:i:s")
        ));
      }
      ob_start();
        include __DIR__ . "/page/login.php";
      $html = ob_get_clean();
      Response::setContent($html);
    });
  }
}

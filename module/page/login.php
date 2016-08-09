<?php
use Lobby\App\admin\Fr\LS;

/**
 * User is logged in, so redirect to Admin main page
 */
if(LS::$loggedIn){
  \Response::redirect("/admin");
}
if(isset($_POST["username"]) && isset($_POST["password"])){
  $user = $_POST["username"];
  $pass = $_POST["password"];
  if($user == "" || $pass == ""){
    $error = array("Username / Password Wrong", "The username or password you submitted was wrong.");
  }else{
    $login = LS::login($user, $pass, isset($_POST['remember_me']));
    if($login === false){
      $error = array("Username / Password Wrong", "The username or password you submitted was wrong.");
    }else if(is_array($login) && $login['status'] == "blocked"){
      $error = array("Account Blocked", "Too many login attempts. You can attempt login again after ". $login['minutes'] ." minutes (". $login['seconds'] ." seconds)");
    }else{
      \Response::redirect("/admin");
    }
  }
}
?>
<html>
  <head>
    <?php
    \Hooks::doAction("head.begin");
    \Response::head("Admin Login");
    ?>
  </head>
  <body>
    <?php \Hooks::doAction("body.begin");?>
    <div id="workspace">
      <div class="contents">
        <h2>Log In</h2>
        <form method="POST" action="<?php echo \Lobby::u("/admin/login");?>">
          <label clear>
            <span clear>Username</span>
            <input clear type="text" name="username" value="<?php if(isset($_POST['username'])){echo htmlspecialchars($_POST['username']);}?>" />
          </label>
          <label clear>
            <span clear>Password</span>
            <input clear type="password" name="password" id="password" />
            <?php if(isset($_POST['username'])){echo "<script>$('#password').focus()</script>";}?>
          </label>
          <label clear>
            <input type="checkbox" name="remember_me" checked="checked" />
            <span>Remember Me</span>
          </label>
          <button class="btn" clear>Log In</button>
        </form>
        <?php
        if(isset($error)){
          echo ser($error[0], $error[1], false);
        }
        ?>
        <div>
          &copy; <a target="_blank" href="http://lobby.subinsb.com">Lobby</a> <?php echo date("Y");?>
        </div>
        <style>
          form input{
            max-width: 500px;
          }
        </style>
      </div>
    </div>
  </body>
</html>

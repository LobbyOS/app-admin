<?php
$this->setTitle("Change Password");
?>
<div class="contents">
  <h2>Change Password</h2>
  <?php
  if(isset($_POST['change_password'])){
    $curPass = $_POST['current_password'];
    $newPass = $_POST['new_password'];
    $retypePass = $_POST['retype_password'];
    
    if($curPass != null && $newPass != null && $retypePass != null){
      if(!\Fr\LS::login("admin", $curPass, false, false)){
        echo ser("Login Failed", "Couldn't login to your account to change password.");
      }else if($newPass !== $retypePass){
        echo ser("Passwords Doesn't match"), "The passwords you entered didn't match. Try again.</p></p>";
      }else{
        $changePass = \Fr\LS::changePassword($newPass);
        if($changePass === true){
          echo sss("Password Changed Successfully", "Your password was updated.");
        }
      }
    }else{
      echo "<p><h2>Password Fields was blank</h2><p>Form fields were left blank</p></p>";
    }
  }
  ?>
  <form action="<?php echo \Lobby::u();?>" method='POST'>
    <label>
      <p>Current Password</p>
      <input type='password' name='current_password' />
    </label>
    <label>
      <p>New Password</p>
      <input type='password' name='new_password' />
    </label>
    <label>
      <p>Retype New Password</p>
      <input type='password' name='retype_password' />
    </label>
    <button style="display: block;margin-top: 10px;" class="btn green" name='change_password' type='submit'>Change Password</button>
  </form>
</div>

<?php
    include '../db.php';

    session_start();
    
    if(isset($_POST['user'])){
        $_SESSION['user'] = $_POST['user'];
    } else {
        $_SESSION['user'] = "";
    }
    
    if(isset($_POST['pass'])){
        $pass = $_POST['pass'];
    } else {
        $pass = '';
    }
    
    $md5_err = false;
    $login_err = false;
    
    if($_SESSION['user']!= ""){
    $conn = db_connect();

    if (CRYPT_MD5 == 1){
        $user_id = user_login($_SESSION['user'], $pass);
        if($user_id >= 0){
            /* User successfully logged in! */
            $_SESSION['user_id'] = $user_id;
            
            header("Location: post.php?login=true");
            
        } else {
            $login_err = true;
        }
    } else {
        $md5_err = true;
    }
    db_close($conn);
    }
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>livetick - Login</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="">
    <link href="../css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="../css/style.css">
  </head>
  <body>
  
  <div class="container top-buffer">
  
  <?php 
  
  if($md5_err){
    echo '<div class="span6 offset3"><span class="label label-important">Fehler: MD5 nicht verfügbar!</span></div>'."\n";
  }
  if($login_err){
    echo '<div class="span6 offset3"><span class="label label-important">Fehler: Benutzername und/oder Passwort ungültig.</span></div>'."\n";
  }
  
  ?>
    <div class="span6 offset3">
    <h1>Anmelden</h1>
    <form class="form-horizontal" action="login.php" method="post">
        <div class="control-group">
            <label class="control-label" for="inputUser">Benutzername</label>
            <div class="controls">
                <input type="text" id="inputUser" name="user" placeholder="Benutzername">
            </div>
        </div>
        <div class="control-group">
            <label class="control-label" for="inputPassword">Passwort</label>
            <div class="controls">
                <input type="password" id="inputPassword" name="pass" placeholder="Passwort">
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <button type="submit" class="btn btn-primary"><i class="icon-user icon-white"></i> Anmelden</button>
            </div>
        </div>
    </form>
    </div>
  </div>


  
  </body>
</html>
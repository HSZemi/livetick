<?php 

// login: $user as string (username), $pass as string
function user_login($user, $pass){
    $user = mysql_real_escape_string($user);
    $pass = mysql_real_escape_string($pass);
    
    $query = "SELECT ID, password FROM ".PREFIX."users WHERE username LIKE '$user'";
    $result = mysql_query($query) or die("user_login: Anfrage fehlgeschlagen: " . mysql_error());
    $row = mysql_fetch_array($result);
    $passwd_enc = $row['password'];
    $user_id = $row['ID'];
    mysql_free_result($result);
    
    if (CRYPT_MD5 == 1){
        if(crypt($pass,"$1$".PASSSALT) != $passwd_enc){
            return -1;
        } else {
            return $user_id;
        }
    } else {
        echo "MD5 not available.\n<br>";
    }
}

// return userlevel as int
function get_userlevel(){
      /* 0: not logged in
       * 1: regular user, may comment
       * 2: editor, may write posts
       * 3: admin, may do anything
       */
      if(!isset($_SESSION['user_id'])){
            return 0;
      }
      $level = 0;
      $user_id = intval($_SESSION['user_id']);
      $query = "SELECT userlevel FROM ".PREFIX."users WHERE ID=$user_id";
      $result = mysql_query($query) or die("get_userlevel: Anfrage fehlgeschlagen: " . mysql_error());
      if($row = mysql_fetch_array($result)){
            $level = intval($row['userlevel']);
      }
      mysql_free_result($result);
      
      return $level;
}

// take user_id as string or int, return corresponding username as string
function get_user_by_id($user_id){
    $user_id = mysql_real_escape_string($user_id);
    $query = "SELECT username FROM ".PREFIX."users WHERE ID='$user_id'";
    $result = mysql_query($query) or die("get_user_by_id: Anfrage fehlgeschlagen: " . mysql_error());
    $row = mysql_fetch_array($result);
    $username = $row['username'];
    mysql_free_result($result);
    
    if(isset($row['username'])){
        return $username;
    } else {
        return "";
    }
}

// take username as string, return corresponding user id as int or -1 if none exists
function get_id_of_user($username){
    $username = mysql_real_escape_string($username);
    $query = "SELECT ID FROM ".PREFIX."users WHERE username LIKE '".$username."'";
    $result = mysql_query($query) or die("get_id_of_user: Anfrage fehlgeschlagen: " . mysql_error());
    $row = mysql_fetch_array($result);
    $user_id = $row['ID'];
    mysql_free_result($result);
    
    if(isset($row['ID'])){
        return intval($user_id);
    } else {
        return -1;
    }
}

// take username as string and password as string and make a user available with those credentials
function create_or_update_user($user, $pass){
    $user = mysql_real_escape_string($user);
    $pass = mysql_real_escape_string($pass);
    
    if (CRYPT_MD5 == 1){
        $pass = crypt($pass,"$1$".PASSSALT);
        
        $user_id = get_id_of_user($user);
        
        if($user_id >= 0){
            $query = "UPDATE ".PREFIX."users SET password='".$pass."' WHERE ID=$user_id;";
        } else {
            $query = "INSERT INTO ".PREFIX."users(username, password, userlevel) VALUES ('$user', '$pass', 1);";
        }
        $result = mysql_query($query);
        if(!$result){
            echo "create_or_update_user: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
            return false;
        }
        return true;
    } else {
        echo "MD5 not available.\n<br>";
        return false;
    }
}

// take username as string and password as string and delete corresponding user if the
// credentials are valid
function delete_user($user, $pass){
    if(user_login($user, $pass) > -1){
        $user = mysql_real_escape_string($user);
        $query = "DELETE FROM ".PREFIX."users WHERE username LIKE '".$user."'";
        $result = mysql_query($query);
        if(!$result){
            echo "delete_user: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
            return false;
        }
        return true;
    } else {
        return false;
    }
}

?>
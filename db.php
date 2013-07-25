<?php

/*
 * The first two methods defined in this file are required to manage a connection
 * with the database. The others return useful information about the gamestate or
 * change the gamestate, for example by modifying players or rounds.
 */
 
 include 'config.php';


function db_connect(){
	// establish connection with the database
	$link = mysql_connect("localhost", USER, PASS)
	or die("Keine Verbindung möglich: " . mysql_error());
// 	echo "Verbindung zum Datenbankserver erfolgreich<br/>";

	mysql_select_db(DATABASE) or die("Auswahl der Datenbank fehlgeschlagen</br>");
	
	// UTF8 ist cool!
	$query = "set names 'utf8';";
      $result = mysql_query($query);
      if(!$result){
            echo "create_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
	
	
	return $link;
}

function db_close($link){
	mysql_close($link);
}

function user_login($user, $pass){
    $user = mysql_real_escape_string($user);
    $pass = mysql_real_escape_string($pass);
    
    $query = "SELECT ID, password FROM ".PREFIX."users WHERE username LIKE '$user'";
    $result = mysql_query($query) or die("user_login: Anfrage fehlgeschlagen: " . mysql_error());
    $row = mysql_fetch_array($result);
    $passwd_enc = $row['password'];
    $user_id = $row['ID'];
    
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

function get_user_by_id($user_id){
    $user_id = mysql_real_escape_string($user_id);
    $query = "SELECT username FROM ".PREFIX."users WHERE ID='$user_id'";
    $result = mysql_query($query) or die("get_user_by_id: Anfrage fehlgeschlagen: " . mysql_error());
    $row = mysql_fetch_array($result);
    $username = $row['username'];
    
    if(isset($row['username'])){
        return $username;
    } else {
        return "";
    }
}

function get_id_of_user($username){
    $username = mysql_real_escape_string($username);
    $query = "SELECT ID FROM ".PREFIX."users WHERE username LIKE '".$username."'";
    $result = mysql_query($query) or die("get_id_of_user: Anfrage fehlgeschlagen: " . mysql_error());
    $row = mysql_fetch_array($result);
    $user_id = $row['ID'];
    
    if(isset($row['ID'])){
        return intval($user_id);
    } else {
        return -1;
    }
}

function create_or_update_user($user, $pass){
    $user = mysql_real_escape_string($user);
    $pass = mysql_real_escape_string($pass);
    
    if (CRYPT_MD5 == 1){
        $pass = crypt($pass,"$1$".PASSSALT);
        
        $user_id = get_id_of_user($user);
        
        if($user_id >= 0){
            $query = "UPDATE ".PREFIX."users SET password='".$pass."' WHERE ID=$user_id;";
        } else {
            $query = "INSERT INTO ".PREFIX."users(username, password) VALUES ('$user', '$pass');";
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

function validate_string_for_mysql_html($string){
      return mysql_real_escape_string(htmlspecialchars($string, ENT_QUOTES | ENT_HTML401));
	//return mysql_real_escape_string($string);
}

function create_post($content, $user_id){
    $content = mysql_real_escape_string($content);
    $user_id = intval($user_id);

    $query = "INSERT INTO ".PREFIX."entries(content, user) VALUES ('$content', $user_id);";
      $result = mysql_query($query);
      if(!$result){
            echo "create_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

function create_comment($ip, $email, $username, $content, $entry){
    $ip = validate_string_for_mysql_html($ip);
    $email = validate_string_for_mysql_html($email);
    $username = validate_string_for_mysql_html($username);
    $content = validate_string_for_mysql_html($content);
    $entry = intval($entry);

    $query = "INSERT INTO ".PREFIX."comments(ip, email, username, content, entry) VALUES ('$ip', '$email', '$username', '$content', $entry);";
      $result = mysql_query($query);
      if(!$result){
            echo "create_comment: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

function approve_comment($comment_id, $state = 1){
      $comment_id = intval($comment_id);
      $state = intval($state);
      $query = "UPDATE ".PREFIX."comments SET approved=$state WHERE ID=$comment_id;";
      $result = mysql_query($query);
      if(!$result){
            echo "approve_comment: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

function update_post($id, $content, $user_id){
    $id = intval($id);
    $content = mysql_real_escape_string($content);
    $user_id = intval($user_id);
    
    $query = "UPDATE ".PREFIX."entries SET content='$content', user=$user_id WHERE ID=$id;";
      $result = mysql_query($query);
      if(!$result){
            echo "create_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

function get_post_content_with_id($id){
    $id = intval($id);

    $query = "SELECT *
            FROM ".PREFIX."entries
            WHERE ID = " . mysql_real_escape_string($id);
      $result = mysql_query($query) or die("get_post_content_with_id: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

      while($row = mysql_fetch_array($result)){
            $content    = $row['content'];
            echo $content;
      }

      mysql_free_result($result);
}

function number_of_comments($post_id, $admin=false){
      $post_id = intval($post_id);
      
      $query = "SELECT COUNT(ID) AS count
            FROM ".PREFIX."comments
            WHERE entry=$post_id";
            
      if(!$admin){
            $query .= " AND approved=1";
      }
      
      $query .= ";";
      
      $result = mysql_query($query) or die("number_of_comments: Anfrage fehlgeschlagen: " . mysql_error());
      $row = mysql_fetch_array($result);
      mysql_free_result($result);
      return intval($row['count']);
}

function number_of_unapproved_comments(){
      $query = "SELECT COUNT(ID) AS count
            FROM ".PREFIX."comments
            WHERE approved=0;";

      $result = mysql_query($query) or die("number_of_unapproved_comments: Anfrage fehlgeschlagen: " . mysql_error());
      $row = mysql_fetch_array($result);
      mysql_free_result($result);
      return intval($row['count']);
}


function print_post($id, $username, $timestamp, $content, $admin = false, $comments = false){
      // bugfix for wrong server time
      $date = date_create($timestamp);
      $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
      $fulltime = date_format($date, "Y-m-d H:i:s");
            
      // if in admin interface show edit link
      $editlink = $admin ? " <a href=\"" . BASEDIR . "/admin/post.php?modify=$id\" title=\"edit\"><i class=\"icon-pencil\"></i></a> " : "";
            
      echo '<div class="span12 post">
      <p><a href="'.BASEDIR.'/index.php?id='.$id.'">
      <span class="badge badge-info">'.$id.'</span>
      </a> 
      <b><abbr title="'.$fulltime.'">'.$time.'</abbr></b>
      <span class="label text-right">'.$username.'</span>'.$editlink.'</p>
                
      '.br($content).'
      
      ';
      
      if($comments){
            print_comments($id, $admin);
      }
      
      
                
       echo '</div>
       ';
}

function print_comments($post_id, $admin = false){
      // HTML output
       echo '<div class="accordion" id="comments-'.intval($post_id).'">
       <div class="accordion-group comments">
         <div class="accordion-heading text-right">
          <a class="accordion-toggle" data-toggle="collapse" data-parent="#comments-'.intval($post_id).'" href="#collapse-'.intval($post_id).'">
            Kommentare ('.number_of_comments($post_id, $admin).')
          </a>
         </div>
         <div id="collapse-'.intval($post_id).'" class="accordion-body">
          <div class="accordion-inner">
          ';
      
      $query = "SELECT *
            FROM ".PREFIX."comments
            WHERE entry = " . intval($post_id);
      $result = mysql_query($query) or die("print_comments: Anfrage fehlgeschlagen: " . mysql_error());
      

      while($row = mysql_fetch_array($result)){
            $comment_id = $row['ID'];
            $timestamp  = $row['timestamp'];
            $ip         = $row['ip'];
            $email      = $row['email'];
            $username   = $row['username'];
            $content    = $row['content'];
            $approved   = $row['approved'];
            
            print_single_comment($comment_id, $timestamp, $username, $content, $admin, $approved, $ip, $email);
            
      }
      
      echo '</div>
      <p class="text-right"><a href="'.BASEDIR.'/writecomment.php?id='.$post_id.'" title="Kommentar verfassen">Kommentar verfassen</a></p>
      </div>
</div>
</div>';

      mysql_free_result($result);

}

function print_single_comment($comment_id, $timestamp, $username, $content, $admin, $approved, $ip, $email){
      $date = date_create($timestamp);
      $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
      $fulltime = date_format($date, "Y-m-d H:i:s");
      
      if($approved != '1'){
            $approved = false;
      } else {
            $approved = true;
      }
      
      if($admin){
            $toprow = "<span class='badge'>$comment_id</span>
                       <span class='label'>$username</span>
                       <abbr title='$fulltime'>$time</abbr>
                       <span class='badge'>$ip</span>
                       <span class='badge'>$email</span>";
      } else {
            $toprow = "<span class='label'>$username</span>
                       <abbr title='$fulltime'>$time</abbr>";
      }

      
      if($admin and !$approved){
            echo "<p><span class='text-warning'>" . $toprow . "</span><br/>\n";
            echo br($content)."</p>\n";
      } elseif($approved){
            echo "<p>".$toprow . "<br/>\n";
            echo br($content)."</p>\n";
      }
}

function list_of_comments($approved = 'all'){
      $query = "SELECT * FROM ".PREFIX."comments";
            
      if($approved == 1 or $approved == 0){
            $query .= " WHERE approved=".$approved;
      }
      
      $query .= " ORDER BY ID DESC;;";
      
      $result = mysql_query($query) or die("list_of_comments: Anfrage fehlgeschlagen: " . mysql_error());
      
      while($row = mysql_fetch_array($result)){
            $comment_id = $row['ID'];
            $timestamp  = $row['timestamp'];
            $ip         = $row['ip'];
            $email      = $row['email'];
            $username   = $row['username'];
            $content    = $row['content'];
            $approved   = $row['approved'];
            
            if(intval($approved) == 0){
                  echo "<hr /><i class='icon-arrow-down'></i> <a href='approvecomment.php?approveid=$comment_id' title='Kommentar $comment_id genehmigen'>genehmigen</a>\n";
            } else {
                  echo "<hr /><i class='icon-arrow-down'></i> <a href='approvecomment.php?disapproveid=$comment_id' title='Genehmigung widerrufen (Kommentar $comment_id)'>widerrufen</a>\n";
            }
            print_single_comment($comment_id, $timestamp, $username, $content, true, $approved, $ip, $email);
            
      }
      
}

function get_post_with_id($id, $admin = false){

    $id = intval($id);

    $query = "SELECT *
            FROM ".PREFIX."entries
            WHERE ID = " . mysql_real_escape_string($id);
      $result = mysql_query($query) or die("get_post_with_id: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

      while($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['content'];
            $user       = $row['user'];
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            $username = get_user_by_id($user);
            
            print_post($id, $username, $timestamp, $content, $admin, true);
      }

      mysql_free_result($result);
}

function list_last_comments($count, $admin = false){
      $query = "SELECT * FROM ".PREFIX."comments";
      if(!$admin){
            $query .= " WHERE approved = 1";
      }
      $query .= " ORDER BY ID DESC LIMIT " . intval($count);
      
      $result = mysql_query($query) or die("get_last_posts: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

      while($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['ip'];
            $email      = $row['email'];
            $username   = $row['username'];
            $content    = $row['content'];
            $entry      = $row['entry'];
            $approved   = $row['approved'];
            
            // bugfix for wrong server time
            $date = date_create($timestamp);
            $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
            $fulltime = date_format($date, "Y-m-d H:i:s");
            
            ($admin) ? $class = "text-warning" : $class = "";
            
            echo "<p class='$class'>$username zu <a href='".BASEDIR."/index.php?id=$entry'>#$entry</a> um <abbr title='$fulltime'>$time Uhr</abbr></p>\n";
      }
}


function get_last_posts($count, $admin){
    $count = intval($count);

    $query = "SELECT *
            FROM ".PREFIX."entries
            ORDER BY timestamp DESC
            LIMIT " . mysql_real_escape_string($count);
      $result = mysql_query($query) or die("get_last_posts: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

      while($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['content'];
            $user       = $row['user'];
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            $username = get_user_by_id($user);
            
            print_post($id, $username, $timestamp, $content, $admin, true);
      }

      mysql_free_result($result);
}


function get_posts_since_with_max_id($id){
    echo max_id() . "\n";
    get_posts_since($id);
}

function get_posts_since($id, $admin = false){
    $id = intval($id);

    $query = "SELECT *
            FROM ".PREFIX."entries
            WHERE ID > " . mysql_real_escape_string($id) .
            " ORDER BY timestamp DESC;";
      $result = mysql_query($query) or die("get_posts_since: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output
      
      //echo max_id() . "\n";

      while($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['content'];
            $user       = $row['user'];
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            $username = get_user_by_id($user);
            
            print_post($id, $username, $timestamp, $content, $admin, true);
      }

      mysql_free_result($result);

}

function max_id(){
    $query = "SELECT MAX(ID) AS max
            FROM ".PREFIX."entries";
      $result = mysql_query($query) or die("get_posts_since: Anfrage fehlgeschlagen: " . mysql_error());
      $row = mysql_fetch_array($result);
      mysql_free_result($result);
      return $row['max'];
}

function br($string){
    return str_replace("\n", "<br />", $string);
}

?>
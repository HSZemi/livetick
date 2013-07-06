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
    $user = validate_string_for_mysql_html($user);
    $pass = validate_string_for_mysql_html($pass);
    
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
    $user_id = validate_string_for_mysql_html($user_id);
    $query = "SELECT username FROM ".PREFIX."users WHERE ID='$user_id'";
    $result = mysql_query($query) or die("get_user_by_id: Anfrage fehlgeschlagen: " . mysql_error());
    $row = mysql_fetch_array($result);
    $username = $row['username'];
    
    return $username;
}

function validate_string_for_mysql_html($string){
      //return mysql_real_escape_string(htmlspecialchars($string, ENT_QUOTES | ENT_HTML401));
	return mysql_real_escape_string($string);
}

function create_post($content, $user_id){
    $content = validate_string_for_mysql_html($content);
    $user_id = intval($user_id);

    $query = "INSERT INTO ".PREFIX."entries(content, user) VALUES ('$content', $user_id);";
      $result = mysql_query($query);
      if(!$result){
            echo "create_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

function update_post($id, $content, $user_id){
    $id = intval($id);
    $content = validate_string_for_mysql_html($content);
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

function get_post_with_id($id){

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
            
            // bugfix for wrong server time
            $date = date_create($timestamp);
            $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
            $fulltime = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "Y-m-d H:i:s");
            
            echo '<div class="span12 post">
                <p><a href="'.BASEDIR.'/index.php?id='.$id.'">
                    <span class="badge badge-info">'.$id.'</span>
                </a> 
                <b><abbr title="'.$fulltime.'">'.$time.'</abbr></b> 
                <span class="label">'.$username.'</span></p>
                
                '.br($content).'
                
                </div>
                ';
      }

      mysql_free_result($result);
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
            
            // bugfix for wrong server time
            $date = date_create($timestamp);
            $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
            $fulltime = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "Y-m-d H:i:s");
            
            // if in admin interface show edit link
            $editlink = $admin ? " <a href=\"" . BASEDIR . "/admin/post.php?modify=$id\" title=\"edit\"><i class=\"icon-pencil\"></i></a> " : "";
            
            echo '<div class="span12 post">
                <p><a href="'.BASEDIR.'/index.php?id='.$id.'">
                    <span class="badge badge-info">'.$id.'</span>
                </a> 
                <b><abbr title="'.$fulltime.'">'.$time.'</abbr></b>
                <span class="label text-right">'.$username.'</span>'.$editlink.'</p>
                
                '.br($content).'
                
                </div>
                ';
      }

      mysql_free_result($result);
}


function get_posts_since_with_max_id($id){
    echo max_id() . "\n";
    get_posts_since($id);
}

function get_posts_since($id){
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
            
            // bugfix for wrong server time
            $date = date_create($timestamp);
            $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
            $fulltime = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "Y-m-d H:i:s");
            
            echo '<div class="span12 post">
            <p><a href="'.BASEDIR.'/index.php?id='.$id.'">
                <span class="badge badge-info">'.$id.'</span>
            </a> 
            <b><abbr title="'.$fulltime.'">'.$time.'</abbr></b> 
            <span class="label">'.$username.'</span> </p>
            
            '.br($content).'
            
            </div>
            ';
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
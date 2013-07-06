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

function validate_string_for_mysql_html($string){
      //return mysql_real_escape_string(htmlspecialchars($string, ENT_QUOTES | ENT_HTML401));
	return mysql_real_escape_string($string);
}

function create_post($content){
    $query = "INSERT INTO ".PREFIX."entries(content) VALUES ('" . validate_string_for_mysql_html($content) . "');";
      $result = mysql_query($query);
      if(!$result){
            echo "create_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

function update_post($id, $content){
    $query = "UPDATE ".PREFIX."entries SET content='" . validate_string_for_mysql_html($content) . "' WHERE ID=" . intval($id) . ";";
      $result = mysql_query($query);
      if(!$result){
            echo "create_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
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
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            
            // bugfix for wrong server time
            $date = date_create($timestamp);
            $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
            $fulltime = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "Y-m-d H:i:s");
            
            echo "<div class=\"span12 post\">\n<a href=\"" . BASEDIR . "/index.php?id=" . $id . "\">[" . $id . "]</a> <b><abbr title=\"". $fulltime . "\">" . $time . "</abbr></b> <br />\n" . br($content) . "\n</div>";
      }

      mysql_free_result($result);
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
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            
            // bugfix for wrong server time
            $date = date_create($timestamp);
            $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
            $fulltime = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "Y-m-d H:i:s");
            
            // if in admin interface show edit link
            $editlink = $admin ? " (<a href=\"" . BASEDIR . "/admin/post.php?modify=" . $id . "\">edit</a>) " : "";
            
            echo "<div class=\"span12 post\">\n<a href=\"" . BASEDIR . "/index.php?id=" . $id . "\">[" . $id . "]</a> <b><abbr title=\"". $fulltime . "\">" . $time . "</abbr></b>" . $editlink . "<br />\n" . br($content) . "\n</div>\n";
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
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            
            // bugfix for wrong server time
            $date = date_create($timestamp);
            $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
            $fulltime = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "Y-m-d H:i:s");
            
            echo "<div class=\"span12 post\">\n<a href=\"" . BASEDIR . "/index.php?id=" . $id . "\">[" . $id . "]</a> <b><abbr title=\"". $fulltime . "\">" . $time . "</abbr></b> <br />\n" . br($content) . "\n</div>\n";
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
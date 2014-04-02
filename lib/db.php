<?php

/*
 * The first two methods defined in this file are required to manage a connection
 * with the database. 
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
            echo "set names 'utf8': Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
	
	
	return $link;
}

function db_close($link){
	mysql_close($link);
}


function validate_string_for_mysql_html($string){
      return mysql_real_escape_string(htmlspecialchars($string, ENT_QUOTES | ENT_HTML401));
	//return mysql_real_escape_string($string);
}

// return max of post IDs
function max_post_id(){
    $query = "SELECT MAX(ID) AS max
            FROM ".PREFIX."entries";
      $result = mysql_query($query) or die("max_id: Anfrage fehlgeschlagen: " . mysql_error());
      $row = mysql_fetch_array($result);
      mysql_free_result($result);
      return intval($row['max']);
}

// replace \n by html <br />
function br($string){
    return str_replace("\n", "<br />", $string);
}

?>
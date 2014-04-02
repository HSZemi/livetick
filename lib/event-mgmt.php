<?php 


// create a new post with content as string and user_id as int or string
function create_event($name, $short, $date, $infoline){
    $name = mysql_real_escape_string($name);
    $short = mysql_real_escape_string($short);
    $date = mysql_real_escape_string($date);
    $infoline = mysql_real_escape_string($infoline);

    $query = "INSERT INTO ".PREFIX."events(name, short, date, infoline) VALUES ('$name', '$short', '$date', '$infoline');";
      $result = mysql_query($query);
      if(!$result){
            echo "create_event: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

// update an existing post with given id and replace the content
function update_event($id, $name, $short, $date, $infoline){
    $id = intval($id);
    $name = mysql_real_escape_string($name);
    $short = mysql_real_escape_string($short);
    $date = mysql_real_escape_string($date);
    $infoline = mysql_real_escape_string($infoline);
    
    $query = "UPDATE ".PREFIX."events SET name='$name', short='$short', date='$date', infoline='$infoline' WHERE ID=$id;";
      $result = mysql_query($query);
      if(!$result){
            echo "update_event: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

// print short list of events
function print_list_of_events_short(){
	$query = "SELECT *
		FROM ".PREFIX."events
		ORDER BY ID DESC;";
	$result = mysql_query($query) or die("print_list_of_events_short: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output
	echo "<ul>\n";

      while($row = mysql_fetch_array($result)){
            $id		= $row['ID'];
            $name		= $row['name'];
            $short	= $row['short'];
            $date		= $row['date'];
            $infoline 	= $row['infoline'];
            
            echo "<li><a href='".BASEDIR."/index.php?event=$id'>$name vom $date</a></li>\n";
      }
      
      echo "</ul>\n";

      mysql_free_result($result);

}

// print list of events
function print_list_of_events($admin=false, $selected=null){
	$query = "SELECT *
		FROM ".PREFIX."events
		ORDER BY ID DESC;";
	$result = mysql_query($query) or die("print_list_of_events: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output
	//echo "<ul class='unstyled'>\n";
	echo '<ul class="media-list">';

      while($row = mysql_fetch_array($result)){
            $id		= $row['ID'];
            $name		= $row['name'];
            $short	= $row['short'];
            $date		= $row['date'];
            $infoline 	= $row['infoline'];
            
            if($admin){
			//$editlink = "<a href='".BASEDIR."/admin/events.php?modify=$id' title='edit'><i class='icon-pencil'></i></a>";
			$editlink = "<a href='".BASEDIR."/admin/events.php?modify=$id' title='edit' class='btn'>Bearbeiten</a>";
			$selectlink = "<a href='".BASEDIR."/admin/events.php?select=$id' title='select' class='btn'>Auswählen</a>";
            } else {
			$editlink = '';
			$selectlink = '';
            }
            
            if($id == $selected){
			$selectedclass = ' selectedevent';
			$selectlink = '';
            } else {
			$selectedclass = '';
            }
            
            //echo "<li><a href='".BASEDIR."/index.php?event=$id'><span class='badge'>$id</span></a> $name ($short) - $date <i>$infoline</i> $editlink</li>\n";
            echo "<li class='media$selectedclass'><div class='media-body'>
            <h4 class='media-heading'>$name</h4>
            <p>
            <b>Kurzbezeichnung:</b> $short<br>
            <b>Datum:</b> $date<br>
            <b>Info:</b> $infoline<br>
            <b>ID:</b> $id<br>
            <a href='".BASEDIR."/index.php?event=$id' class='btn btn-inverse'>Aufrufen</a> $selectlink $editlink
            </p>
            </li>";
      }
      
      echo "</ul>\n";

      mysql_free_result($result);

}

function print_event_select($current = null){
	$query = "SELECT *
		FROM ".PREFIX."events
		ORDER BY ID DESC;";
	$result = mysql_query($query) or die("print_event_select: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output
	echo "<select name='event' class='eventselect'>\n";

      while($row = mysql_fetch_array($result)){
            $id		= $row['ID'];
            $name		= $row['name'];
            $short	= $row['short'];
            $date		= $row['date'];
            $infoline 	= $row['infoline'];
            
            if($id == $current){
			echo "<option selected='selected' value='$id'>$short</option>\n";
		} else {
			echo "<option value='$id'>$short</option>\n";
		}
      }
      
      echo "</select>\n";

      mysql_free_result($result);
}

//get array with event info for event id
function get_event_info_by_id($id){
	$id = intval($id);

	$query = "SELECT *
		FROM ".PREFIX."events
		WHERE ID = $id;";
	$result = mysql_query($query);
	if($result){
		$event_info = mysql_fetch_array($result);
		return $event_info;
	} else {
		return false;
	}
}

function get_last_event(){
	$query = "SELECT MAX(ID) AS id
		FROM ".PREFIX."events;";
	$result = mysql_query($query);
	if($result){
		$row = mysql_fetch_array($result);
		return $row['id'];
	} else {
		return -1;
	}
}

function get_event_id_for_post_id($id){
	$id = intval($id);
	$query = "SELECT event
		FROM ".PREFIX."entries
		WHERE ID = $id;";
	$result = mysql_query($query);
	if($result){
		$row = mysql_fetch_array($result);
		return $row['event'];
	} else {
		return -1;
	}
}


?>
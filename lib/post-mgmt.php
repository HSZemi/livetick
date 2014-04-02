<?php 

include 'comment-mgmt.php';
include 'user-mgmt.php';

// create a new post with content as string and user_id as int or string
function create_post($content, $user_id, $event_id){
    $content = mysql_real_escape_string($content);
    $user_id = intval($user_id);
    $event_id = intval($event_id);

    $query = "INSERT INTO ".PREFIX."entries(content, user, event) VALUES ('$content', $user_id, $event_id);";
      $result = mysql_query($query);
      if(!$result){
            echo "create_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

// update an existing post with given id and replace the content
function update_post($id, $content){
    $id = intval($id);
    $content = mysql_real_escape_string($content);
    
    $query = "UPDATE ".PREFIX."entries SET content='$content' WHERE ID=$id;";
      $result = mysql_query($query);
      if(!$result){
            echo "update_post: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

// echoes post content
function print_post_content_with_id($id){
    $id = intval($id);

    $query = "SELECT *
            FROM ".PREFIX."entries
            WHERE ID = " . mysql_real_escape_string($id);
      $result = mysql_query($query) or die("print_post_content_with_id: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

      while($row = mysql_fetch_array($result)){
            $content    = $row['content'];
            echo $content;
      }

      mysql_free_result($result);
}

// print single post with pretty html-gedöns around it
function print_post($id, $username, $timestamp, $content, $admin = false, $comments = false, $event_id = 0){
	$event_id = intval($event_id);
	$event = get_event_info_by_id($event_id);
	$event ? $event_short = $event['short'] : $event_short = '';
      // bugfix for wrong server time
      $date = date_create($timestamp);
      $time = date_format(date_sub($date, date_interval_create_from_date_string('4 minutes')), "H:i");
      $fulltime = date_format($date, "Y-m-d H:i:s");
            
      // if in admin interface show edit link
      $editlink = $admin ? " <a href=\"" . BASEDIR . "/admin/post.php?modify=$id\" title=\"edit\"><i class=\"icon-pencil\"></i></a> " : "";
            
      echo "<div class='span12 post'>
      <p><a href='".BASEDIR."/index.php?id=$id'>
      <span class='badge badge-info'>$id</span>
      </a> 
      <b><abbr title='$fulltime'>$time</abbr></b>
      <span class='label text-right'>$username</span> <a href='".BASEDIR."/index.php?event=$event_id'><span class='label text-right'>$event_short</span></a>$editlink</p>
                
      ".br($content)."
      
      ";
      
      if($comments){
            print_comments($id, $admin);
      }
      
      
                
       echo '</div>
       ';
}

// print a single post
function print_single_post($id, $admin = false){

    $id = intval($id);

    $query = "SELECT *
            FROM ".PREFIX."entries
            WHERE ID = " . mysql_real_escape_string($id);
      $result = mysql_query($query) or die("print_single_post: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

      if($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['content'];
            $user       = $row['user'];
            $event      = $row['event'];
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            $username = get_user_by_id($user);
            
            print_post($id, $username, $timestamp, $content, $admin, true, $event);
      }

      mysql_free_result($result);
}

// print the $count latest posts
function print_latest_posts($count, $admin){
    $count = intval($count);

    $query = "SELECT *
            FROM ".PREFIX."entries
            ORDER BY timestamp DESC
            LIMIT " . mysql_real_escape_string($count);
      $result = mysql_query($query) or die("print_latest_posts: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

      while($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['content'];
            $user       = $row['user'];
            $event      = $row['event'];
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            $username = get_user_by_id($user);
            
            print_post($id, $username, $timestamp, $content, $admin, true, $event);
      }

      mysql_free_result($result);
}

// print posts since $id (exclusive)
function print_posts_since($id, $admin = false, $event = null){
    $id = intval($id);

    $query = "SELECT *
            FROM ".PREFIX."entries
            WHERE ID > " . mysql_real_escape_string($id);
            
	if($event != null){
		$query .= " AND event = " . intval($event);
	}
      
      $query .= " ORDER BY timestamp DESC;";
      $result = mysql_query($query) or die("print_posts_since: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output


      while($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['content'];
            $user       = $row['user'];
            $event      = $row['event'];
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            $username = get_user_by_id($user);
            
            print_post($id, $username, $timestamp, $content, $admin, true, $event);
      }

      mysql_free_result($result);

}

// print posts of event
function print_posts_of_event($event_id, $admin = false){
    $event_id = intval($event_id);

    $query = "SELECT *
            FROM ".PREFIX."entries
            WHERE event = $event_id
            ORDER BY timestamp DESC;";
      $result = mysql_query($query) or die("print_posts_of_event: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output

	$has_entries = false;
      while($row = mysql_fetch_array($result)){
            $id         = $row['ID'];
            $timestamp  = $row['timestamp'];
            $content    = $row['content'];
            $user       = $row['user'];
            $event      = $row['event'];
            
		$has_entries = true;
            
            //$time = date_format(date_create($timestamp), "H:i");
            //$fulltime = date_format(date_create($timestamp), "Y-m-d H:i:s");
            $username = get_user_by_id($user);
            
            print_post($id, $username, $timestamp, $content, $admin, true, $event);
      }
      
      if(!$has_entries){
		echo "<div class='span12 post'>Bislang sind zu diesem <em>großartigen</em> Event leider noch keine Einträge vorhanden.</div>";
      }

      mysql_free_result($result);

}

?>
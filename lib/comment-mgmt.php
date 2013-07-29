<?php 

// create a new comment for $entry
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

// change the state of a certain comment
function approve_comment($comment_id, $state = 1){
      $comment_id = intval($comment_id);
      $state = intval($state);
      $query = "UPDATE ".PREFIX."comments SET approved=$state WHERE ID=$comment_id;";
      $result = mysql_query($query);
      if(!$result){
            echo "approve_comment: Anfrage fehlgeschlagen: " . mysql_error() . "<br/>";
      }
}

// retreive the number of comments for a certain post,
// if not admin only approved ones
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

// return the overall number of unapproved comments
function number_of_unapproved_comments(){
      $query = "SELECT COUNT(ID) AS count
            FROM ".PREFIX."comments
            WHERE approved=0;";

      $result = mysql_query($query) or die("number_of_unapproved_comments: Anfrage fehlgeschlagen: " . mysql_error());
      $row = mysql_fetch_array($result);
      mysql_free_result($result);
      return intval($row['count']);
}

// return ID of last approved comment
function last_approved_comment_id(){
      $query = "SELECT MAX(ID) AS max
            FROM ".PREFIX."comments
            WHERE approved=1;";

      $result = mysql_query($query) or die("last_approved_comment_id: Anfrage fehlgeschlagen: " . mysql_error());
      $row = mysql_fetch_array($result);
      mysql_free_result($result);
      return intval($row['max']);
}

// print comment div for a certain post
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

// print a single comment
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

// print a simplified list of comments for admin backend
function print_list_of_comments($approved = 'all'){
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

// print short list of last comments for frontpage
function print_list_of_last_comments($count, $admin = false){
      $query = "SELECT * FROM ".PREFIX."comments";
      if(!$admin){
            $query .= " WHERE approved = 1";
      }
      $query .= " ORDER BY ID DESC LIMIT " . intval($count);
      
      $result = mysql_query($query) or die("list_last_comments: Anfrage fehlgeschlagen: " . mysql_error());
      
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

?>
<?php
  include 'lib/db.php';
  include 'lib/comment-mgmt.php';
  include 'lib/event-mgmt.php';
  $conn = db_connect();
  if(isset($_GET['id'])){
      $id = $_GET['id'];
      $last = last_approved_comment_id();
      if($id < $last){
            echo $last . "\n";
            print_list_of_last_comments(20);
      }
  }
  db_close($conn);
?>
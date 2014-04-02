<?php
  include 'lib/db.php';
  include 'lib/event-mgmt.php';
  include 'lib/post-mgmt.php';
  include 'lib/statistics.php';
  $conn = db_connect();
  if(isset($_GET['id'])){
      $id = $_GET['id'];
      if(!isset($_GET['event'])){
		$event = null;
      } else {
		$event = $_GET['event'];
      }
      echo max_post_id() . "\n";
      print_posts_since($id, false, $event);
      register_visit($_SERVER['REMOTE_ADDR']);
  }
  db_close($conn);
?>
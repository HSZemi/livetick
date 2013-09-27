<?php
  include 'lib/db.php';
  include 'lib/post-mgmt.php';
  include 'lib/statistics.php';
  $conn = db_connect();
  if(isset($_GET['id'])){
      $id = $_GET['id'];
      echo max_post_id() . "\n";
      print_posts_since($id);
      register_visit($_SERVER['REMOTE_ADDR']);
  }
  db_close($conn);
?>
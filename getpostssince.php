<?php
  include 'lib/db.php';
  include 'lib/post-mgmt.php';
  $conn = db_connect();
  if(isset($_GET['id'])){
      $id = $_GET['id'];
      echo max_post_id() . "\n";
      print_posts_since($id);
  }
  db_close($conn);
?>
<?php
  
  include 'db.php';
  
  $conn = db_connect();
  
  if(isset($_GET['id'])){
        $id = $_GET['id'];
  }
  
  if(isset($id)){
    get_posts_since_with_max_id($id);
  }
  
  db_close($conn);
  
?>
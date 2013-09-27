<?php
session_start();

include '../lib/db.php';
include '../lib/comment-mgmt.php';

$conn = db_connect();

if(!isset($_SESSION['user_id']) or $_SESSION['user_id'] < 0){
    header("Location: login.php");
    db_close($conn);
    die();
}

if(isset($_GET['approveid'])){
      $comment_id = intval($_GET['approveid']);
      approve_comment($comment_id);
      header("Location: comments.php?approved=$comment_id");
} elseif(isset($_GET['disapproveid'])){
      $comment_id = intval($_GET['disapproveid']);
      approve_comment($comment_id, 0);
      header("Location: comments.php?disapproved=$comment_id");
} else {
      header("Location: comments.php");
}

db_close($conn);

?>

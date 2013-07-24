<?php
session_start();

if(!isset($_SESSION['user_id']) or $_SESSION['user_id'] < 0){
    header("Location: login.php");
}
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>livetick - Post erstellen</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="">
    <link href="../css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="../css/style.css">
  </head>
  <body>

  <?php
    include '../db.php';
    
    //echo 'Hello '.$_SESSION['user'].' with ID '.$_SESSION['user_id']."!";
    
    $conn = db_connect();
    
  ?>
  
  <!-- Navigation -->
  <div class="navbar navbar-static-top">
      <div class="navbar-inner">
        <ul class="nav">
            <li><a href="post.php" target="_self"><i class="icon-pencil"></i> Beitrag erstellen</a></li>
            <li class="active"><a href="comments.php" target="_self"><i class="icon-comment"></i> Kommentare (<?php echo number_of_unapproved_comments(); ?>)</a></li>
            <li><a href="../index.php" target="_blank"><i class="icon-arrow-right"></i> Ticker ansehen</a></li>
            <li><a href="user.php" target="_self"><i class="icon-user"></i> Benutzerverwaltung</a></li>
            <li><a href="logout.php" target="_self"><i class="icon-off"></i> Abmelden (<?php echo $_SESSION['user'] ?>)</a></li>
            <li>
                <form class="navbar-form pull-left input-prepend input-append">
                    <span class="add-on" style="margin-top:5px;">ID</span>
                    <input type="text" class="span1" name="modify">
                    <button type="submit" class="btn" style="margin-top:5px;">bearbeiten</button>
                </form>
            </li>
        </ul>
      </div>
  </div>
  <!-- End Navigation -->

  
  <div class="container" id="adminpagecontent">
  
  
  <div class="span6" id="commentsarea">
  <?php 
      if(isset($_GET['approved'])){
            echo '<div class="alert alert-success">Kommentar '.$_GET['approved'].' genehmigt.</div>';
      }
      if(isset($_GET['disapproved'])){
            echo '<div class="alert">Kommentar '.$_GET['disapproved'].' zurückgezogen.</div>';
      }
  ?>
  </div>

        <div class="span6" id="commentsarea">
        <h2>Nicht genehmigte Kommentare</h2>
           <?php list_of_comments(0); ?>
        </div>

  
  <!-- Approved comments -->
    <div class="span6"> 
        <h2>Genehmigte Kommentare</h2>
        <?php list_of_comments(1); ?>
    </div>
    </div>
  <!-- End Approved comments -->
  
  <?php db_close($conn); ?>
  
  </body>
</html>
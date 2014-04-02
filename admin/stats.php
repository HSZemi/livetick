<?php
session_start();

if(!isset($_SESSION['user_id']) or $_SESSION['user_id'] < 0){
    header("Location: login.php");
    die();
}
?>
<!DOCTYPE HTML>
<html>
  <head>
    <title>livetick - Statistik</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="">
    <link href="../css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="../css/style.css">
    
    <script src="../js/Chart.js"></script>
    <script src="../js/jquery-2.0.2.min.js"></script>
  </head>
  <body>

  <?php
    include '../lib/db.php';
    include '../lib/comment-mgmt.php';
    include '../lib/statistics.php';

    
    $conn = db_connect();
    
  ?>
  
  <!-- Navigation -->
  <div class="navbar navbar-static-top">
      <div class="navbar-inner">
        <ul class="nav">
            <li><a href="post.php" target="_self"><i class="icon-pencil"></i> Beitrag erstellen</a></li>
            <li><a href="comments.php" target="_self"><i class="icon-comment"></i> Kommentare (<?php echo number_of_unapproved_comments(); ?>)</a></li>
            <li><a href="../index.php" target="_blank"><i class="icon-arrow-right"></i> Ticker ansehen</a></li>
            <li><a href="events.php" target="_self"><i class="icon-glass"></i> Events</a></li>
            <li><a href="user.php" target="_self"><i class="icon-user"></i> Benutzerverwaltung</a></li>
            <li class="active"><a href="stats.php" target="_self"><i class="icon-align-left"></i> Statistik</a></li>
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
  
  
  <div class="span6" id="statsarea">
  
  <h2>Posts</h2>
  <p>Es wurden <b><?php echo get_number_of_posts(); ?> Posts</b> geschrieben.</p>
  <?php print_table_posts_per_user(); ?>
  
  <h2>Kommentare</h2>
  <p>Es wurden <b><?php  echo get_number_of_comments(); ?> Kommentare</b> geschrieben.</p>
  <?php print_table_comments_per_name(); ?>
  
  
  <h2>Besucher</h2>
  <p>In den letzten 10 Minuten <?php $count = get_current_number_of_visitors(); $count == 1 ? $out = "war <b>$count" : $out = "waren <b>$count"; echo $out; ?> Besucher</b> aktiv.</p>
  
  </div>
  <div class="span12" id="chartarea">
	<h3>Letzte Stunde</h3>
	<?php print_visitor_chart(1, 1); ?>
	
	<h3>Letzte 6 Stunden</h3>
	<?php print_visitor_chart(2, 6); ?>
	
	<h3>Letzte 30 Tage</h3>
	<?php print_visitor_chart(3, 30, "days"); ?>
  </div>
  </div>
  
  <?php db_close($conn); ?>
  
  </body>
</html>
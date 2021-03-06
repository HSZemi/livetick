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
    <title>livetick - Post erstellen</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="">
    <link href="../css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="../css/style.css">
  </head>
  <body>
  
    <script src="../js/jquery-2.0.2.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>

  <?php
    include '../lib/db.php';
    include '../lib/post-mgmt.php';
    include '../lib/event-mgmt.php';
    
    $conn = db_connect();
    
    //echo 'Hello '.$_SESSION['user'].' with ID '.$_SESSION['user_id']."!";
    
    if(isset($_POST['textfeld'])){
        $content = $_POST['textfeld'];
    }
    
    if(isset($_POST['id'])){
        $id_p = $_POST['id'];
    }
    
    if(isset($_GET['modify'])){ // Post zum Bearbeiten laden
        if(intval($_GET['modify']) > 0){
            $modify_id = intval($_GET['modify']);
        }
    }
    
    if(!isset($_SESSION['event_id'])){
	$_SESSION['event_id'] = get_last_event();
    }
    if(isset($_POST['event'])){
	$_SESSION['event_id'] = $_POST['event'];
    }
    
    if(isset($content) and $content != ''){
        
        
        if(isset($id_p)){ // Post aktualisieren
            update_post($id_p, $content);
        } else {          // neuen Post erstellen
            create_post($content, $_SESSION['user_id'], $_SESSION['event_id']);
        }
        
       
    }
    
  ?>
  
  <!-- Navigation -->
  <div class="navbar navbar-static-top">
      <div class="navbar-inner">
        <ul class="nav">
            <li class="active"><a href="post.php" target="_self"><i class="icon-pencil"></i> Beitrag erstellen</a></li>
            <li><a href="comments.php" target="_self"><i class="icon-comment"></i> Kommentare (<?php echo number_of_unapproved_comments(); ?>)</a></li>
            <li><a href="../index.php" target="_blank"><i class="icon-arrow-right"></i> Ticker ansehen</a></li>
            <li><a href="events.php" target="_self"><i class="icon-glass"></i> Events</a></li>
            <li><a href="user.php" target="_self"><i class="icon-user"></i> Benutzerverwaltung</a></li>
            <li><a href="stats.php" target="_self"><i class="icon-align-left"></i> Statistik</a></li>
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
  
  <div class="row-fluid" id="adminpagecontent">

        <div class="span5 offset1" id="postingarea">
            <?php 
                if(isset($modify_id)){
                    echo '<h1>Beitrag bearbeiten</h1>';
                }else { 
                    echo '<h1>Neuen Beitrag verfassen</h1>';
                } 
            ?>

            <form action="post.php" method="post">
                <?php 
                    
                    if(isset($modify_id)){
                        echo '<input type="hidden" name="id" value="' . $modify_id . '">';
                    } 

                    echo '<textarea class="span11" cols="100" rows="10" name="textfeld" autofocus>';  
                    if(isset($modify_id)){
                        
                        print_post_content_with_id($modify_id);
                        
                    }
                    echo '</textarea>
                    <br />
                    
                    <button class="btn btn-primary" type="submit">Senden</button> 
                    ';
                    print_event_select($_SESSION['event_id']);
                    
                    ?>
            </form>
        </div>
        
        <div class="span5" id="formathelp">
            <?php include 'formathelp.html' ?>
        </div>
    </div>

  
  <!-- Posts -->
    <div class="span6 offset1">  
        <?php
        
            print_latest_posts(100, true);
          
        ?>
    </div>
  <!-- End Posts -->
  <script type="text/javascript">
  $('.accordion-body').collapse('hide');
  </script>
  
  <?php  db_close($conn); ?>
  
  
  </body>
</html>
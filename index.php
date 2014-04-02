<?php

include 'lib/config.php';
include 'lib/db.php';
include 'lib/event-mgmt.php';
include 'lib/post-mgmt.php';
include 'lib/statistics.php';

$conn = db_connect();

$event_id = 0;
if(isset($_GET['id'])){
	$id = intval($_GET['id']);
	$event_id = get_event_id_for_post_id($id);
}elseif(isset($_GET['event'])){
	$event_id = intval($_GET['event']);
	$comment_event_id = $event_id;
	$event_url_addon = "'&event=$event_id'";
} else {
	$comment_event_id = null;
	$event_id = get_last_event();
	$event_url_addon = "''";
}

$event_info = get_event_info_by_id($event_id);
$event_info ? $event_name 	= $event_info['name'] 		: $event_name 	= '-';
$event_info ? $event_short 	= $event_info['short'] 		: $event_short 	= '-';
$event_info ? $event_date 	= $event_info['date'] 		: $event_date 	= '-';
$event_info ? $event_infoline	= $event_info['infoline'] 	: $event_infoline	= '-';

?>
<!DOCTYPE html>
<html>
 <head>
    <title>livetick - SP-Sitzung</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <!-- Bootstrap -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="css/style.css">
 </head>
 <body>
  
    <script src="js/jquery-2.0.2.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

    <div class="container">
	<div id="header">
		<h4>livetick - simple liveticker application</h4>

		<h1><a href="<?php echo BASEDIR; ?>/index.php">Liveticker zur SP-Sitzung</a></h1>
		
		<h5><?php echo $event_name .' - '. $event_date; ?></h5>
		
		 <?php echo $event_infoline; ?> | <a href="<?php echo BASEDIR; ?>/archiv.php" title="Archiv">Alle Liveticker</a><span class="visible-phone"><a href="<?php echo BASEDIR; ?>/comments.php" title="Letzte Kommentare">Letzte Kommentare</a></span>
	</div>
        
        
        <?php
        
        
        if(isset($_GET['id'])){
                $singleid = $_GET['id'];
                echo "<hr /><br />\n";
                echo print_single_post($singleid);
                register_visit($_SERVER['REMOTE_ADDR']);
        } else {
        
        echo '<div id="options">

        <button id="b_load" class="btn">Neue Posts laden</button>
        <button id="b_toggle_auto" class="btn btn-primary">Automatische Aktualisierung: AN</button>
        <a name="top" href="#bottom">zum&nbsp;Anfang&nbsp;↓</a><br />
        
        </div>
        
        <div id="latest_comments" class="pull-right well hidden-phone">
        <h4>Letzte Kommentare:</h4>';
        print_list_of_last_comments(20, false, $comment_event_id);
        echo '</div>
        
        <div id="updates" style="border:none; background-color: none;"> </div>
        
        <div class="span6"><hr /></div>

        
        ';
            echo print_posts_of_event($event_id, false);
            
            echo '<div class="span12"><a name=bottom href="#top">Nach Oben ↑</a></div>';
        }
        
        db_close($conn);
        
        ?>
        
        <?php include 'footer.html' ?>
    </div>
  
  
  <?php if(!isset($_GET['id'])){ ?>
    <script type="text/javascript">
        var last_post_id = <?php $conn = db_connect();echo max_post_id();db_close($conn); ?>;
        var last_comment_id = <?php $conn = db_connect();echo last_approved_comment_id();db_close($conn); ?>;
        
        var refresh_interval = 20000;
        
        var refresh = true;
        var refresh_auto = window.setInterval("loadContent()", refresh_interval);
        
        // Buttons
        $("#b_load").click( function () {
            loadContent();
        });
        
        $("#b_toggle_auto").click( function () {
            if (refresh) {
                window.clearInterval(refresh_auto);
                $("#b_toggle_auto").html("Automatische Aktualisierung: AUS");
                $("#b_toggle_auto").removeClass("btn-primary");
                refresh = false;
            } else {
                window.setInterval("loadContent()", refresh_interval);
                $("#b_toggle_auto").html("Automatische Aktualisierung: AN");
                $("#b_toggle_auto").addClass("btn-primary");
                refresh = true;
            }
            
        });
        
        // Kommentare
        $('.accordion-body').collapse('hide');
        

        function loadContent()
        {
            $.get('getpostssince.php?id=' + last_post_id + <?php echo $event_url_addon;?>, function(data) {
                  var new_max = data.split("\n", 1);
                  last_post_id = parseInt(new_max[0]);
                  $('#updates').prepend(data.slice(new_max[0].length + 1));
            });
            $.get('lastcomments.php?id=' + last_comment_id + <?php echo $event_url_addon;?>, function(data) {
                  if(!(data=="")){
                        var new_max = data.split("\n", 1);
                        last_comment_id = parseInt(new_max[0]);
                        $('#latest_comments').html('<h4>Letzte Kommentare:</h4>' + data.slice(new_max[0].length + 1));
                  }
            });
        }

        

    </script>
    
    <?php } ?>
  
 </body>
</html>
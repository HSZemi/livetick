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
    <title>livetick - Eventverwaltung</title>
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
    
    $event_name = '';
    $event_short = '';
    $event_date = '';
    $event_infoline = '';
    
	if(isset($_POST['modify_id'])){
		$event_id = intval($_POST['modify_id']);
		
		$event_name = $_POST['event_name'];
		$event_short = $_POST['event_short'];
		$event_date = $_POST['event_date'];
		$event_infoline = $_POST['event_infoline'];
		
		if($event_id == 0){ // neues Event erstellen
			create_event($event_name, $event_short, $event_date, $event_infoline);
		} else {            // Event aktualisieren
			update_event($event_id, $event_name, $event_short, $event_date, $event_infoline);
		}
	}
    
    if(isset($_GET['modify'])){ // Event zum Bearbeiten laden
        if(intval($_GET['modify']) > 0){
            $modify_id = intval($_GET['modify']);
            $event = get_event_info_by_id($modify_id);
            if($event){
			$event_name = $event['name'];
			$event_short = $event['short'];
			$event_date = $event['date'];
			$event_infoline = $event['infoline'];
            }
        }
    }
    
    if(!isset($_SESSION['event_id'])){
	$_SESSION['event_id'] = get_last_event();
    }
    if(isset($_GET['select'])){
	$_SESSION['event_id'] = $_GET['select'];
    }

    
  ?>
  
  <!-- Navigation -->
  <div class="navbar navbar-static-top">
      <div class="navbar-inner">
        <ul class="nav">
            <li><a href="post.php" target="_self"><i class="icon-pencil"></i> Beitrag erstellen</a></li>
            <li><a href="comments.php" target="_self"><i class="icon-comment"></i> Kommentare (<?php echo number_of_unapproved_comments(); ?>)</a></li>
            <li><a href="../index.php" target="_blank"><i class="icon-arrow-right"></i> Ticker ansehen</a></li>
            <li class="active"><a href="events.php" target="_self"><i class="icon-glass"></i> Events</a></li>
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
  
  <div id="adminpagecontent">
  <div class="row-fluid">

	<div class="span11 offset1">
		<h1>Eventverwaltung</h1>
	</div>
  </div>
  <div class="row-fluid">
	<div class="span5 offset1" id="postingarea">
	
		<form action="events.php" method="post">
			<fieldset>
				 <?php 
					if(isset($modify_id)){
						echo '<legend>Event bearbeiten</legend>';
						echo '<input type="hidden" name="modify_id" value="' . $modify_id . '">';
					}else { 
						echo '<legend>Neues Event erstellen</legend>';
						echo '<input type="hidden" name="modify_id" value="0">';
					}
				?>
				<label>Name</label>
				<input class="input-block-level" type="text" name="event_name" placeholder="X. ordentliche Sitzung des Studierendenparlaments" value="<?php echo $event_name; ?>">
				<label>Kurzbezeichnung</label>
				<input class="input-block-level" type="text" name="event_short" placeholder="SP-Sitzung X" value="<?php echo $event_short; ?>">
				<label>Datum</label>
				<input class="input-block-level" type="text" name="event_date" placeholder="dd.mm.yyyy" value="<?php echo $event_date; ?>">
				<label>Infozeile</label>
				<input class="input-block-level" type="text" name="event_infoline" placeholder="Twitter: &lt;a href='https://twitter.com/oeffref'&gt;@oeffref&lt;/a&gt;" value="<?php echo $event_infoline; ?>">
				<button type="submit" class="btn">Senden</button>
			</fieldset>
		</form>
            
            
        </div>
        <div class="span5">
		<?php print_list_of_events(true, $_SESSION['event_id']); ?>
        </div>

    </div>
  </div>
  </div>

  
  
  <?php  db_close($conn); ?>
  
  
  </body>
</html>
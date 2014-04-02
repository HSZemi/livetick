<?php
include 'lib/config.php';
include 'lib/db.php';
include 'lib/event-mgmt.php';

$link = db_connect();
?>
<!DOCTYPE html>
  <head>
    <title>livetick - SP-Sitzung</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="">
    <link href="css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap-responsive.css" rel="stylesheet">
    
    <link rel="stylesheet" type="text/css" href="css/style.css">
  </head>
  <body>
    <div class="container">
    <div id="header">
        <h4>livetick - simple liveticker application</h4>

        <h1><a href="<?php echo BASEDIR; ?>/index.php">Liveticker zur SP-Sitzung</a></h1>
        
        <h5>Archiv</h5>
        
        <span class="b">Twitter: <a href="https://twitter.com/oeffref" target="_blank" alt="@oeffref auf Twitter">@oeffref</a></span> | <a href="<?php echo BASEDIR; ?>/archiv.php" alt="Archiv">Alle Liveticker</a>
    </div>
    <div class="span6"><hr /></div>
    <div class="span12">
    <?php print_list_of_events_short(); ?>
    </div>
    </div>

</body>
<?php db_close($link);?>
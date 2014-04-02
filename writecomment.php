<?php

include 'lib/config.php';
include 'lib/db.php';
include 'lib/post-mgmt.php';
include 'lib/event-mgmt.php';
include 'lib/statistics.php';

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
        <?php include 'header.php' ?>
        
        
        <?php
                
        $conn = db_connect();
        
        $error = false;
        $captchaerror = false;
        if(isset($_POST['post_id']) and isset($_POST['email']) and isset($_POST['username']) and isset($_POST['comment']) and isset($_POST['kaptscha'])){
            $post_id = intval($_POST['post_id']);
            $email = $_POST['email'];
            $username = $_POST['username'];
            $comment = $_POST['comment'];
            $captcha = $_POST['kaptscha'];

            if(strcasecmp($captcha,'Fengler') != 0){
                  $captchaerror = true;
            } elseif(substr_count($email, '@', 1) != 1) {
                  $error = true;
            } elseif(strlen($username) < 1) {
                  $error = true;
            } elseif(strlen($comment) < 1) {
                  $error = true;
            } else {
                  create_comment($_SERVER['REMOTE_ADDR'], $email, $username, $comment, $post_id);
                  echo '<div class="alert"><strong>Kommentar gesendet</strong><br />Dein Kommentar muss nun noch freigeschaltet werden.</div>';
            }
        } elseif(isset($_POST['post_id'])){
            $error = true;
        }
        
        if($error){
            echo '<div class="alert alert-error"><strong>Fehler!</strong><br /> Bitte alle Felder ordentlich ausfüllen.</div>';
        }
        if($captchaerror){
            echo '<div class="alert alert-error"><strong>Lernen Sie zunächst den Namen des großen SP-Präsidenten, bevor Sie hier kommentieren!</strong></div>';
        }
        
        if(isset($_GET['id'])){
                $singleid = $_GET['id'];
                echo "<hr /><br />\n";
                echo print_single_post($singleid);
                
                ?>
      
      <form class="span6" method="post" action="writecomment.php?id=<?php echo $singleid; ?>">
            <h2>Kommentar verfassen</h2>
            <input type="hidden" name="post_id" value="<?php echo $singleid; ?>">
            
            <label class="control-label" for="inputEmail">Email (wird nicht veröffentlicht)</label>
            <input type="text" id="inputEmail" name="email" placeholder="Email">
            
            <label class="control-label" for="inputUsername">Name (wird veröffentlicht)</label>
            <input type="text" id="inputUsername" name="username" placeholder="">
            
            <label class="control-label" for="inputComment">Kommentar</label>
            <textarea class="span6" rows="3" id="inputComment" name="comment"></textarea>
            
            <label class="control-label" for="inputKaptscha">Sicherheitsfrage: Hier den Nachnamen des SP-Präsidenten eingeben</label>
            <input type="text" id="inputKaptscha" name="kaptscha" placeholder="der heißt nämlich Michael *******">

            
            <p>Zusätzlich wird intern noch deine aktuelle IP-Adresse gespeichert.</p>
            
            <button type="submit" class="btn btn-primary">Absenden</button>
      </form>

        <?php } else {
            echo 'Kein Beitrag ausgewählt.<br />
            <a href="index.php" title="zurück">Zurück</a>';
        }
        
        db_close($conn);
        
        ?>
        
        <?php include 'footer.html' ?>
    </div>
  
  
  
    <script type="text/javascript">
        
        // Kommentare
        $('.accordion-body').collapse('show');


    </script>
  
 </body>
</html>
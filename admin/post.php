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
    
    if(isset($_POST['textfeld'])){
        $content = $_POST['textfeld'];
    }
    
    if(isset($_POST['id'])){
        $id_p = $_POST['id'];
    }
    
    if(isset($_GET['modify'])){ // Post zum bearbeiten laden
        if(intval($_GET['modify']) > 0){
            $modify_id = intval($_GET['modify']);
        }
    }
    
    if(isset($content)){
        $conn = db_connect();
        
        if(isset($id_p)){ // Post aktualisieren
            update_post($id_p, $content);
        } else {          // neuen Post erstellen
            create_post($content);
        }
        
        db_close($conn);
    }
    
  ?>
  
  <div class="row-fluid" style="padding:1em;">
        <div class="span6" id="postingarea">
            <a href="../index.php" target="_blank">Ticker ansehen</a> | 
            <a href="post.php?modify=">&raquo; Beitrag bearbeiten</a>
  
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
                        $conn = db_connect();
                        get_post_content_with_id($modify_id);
                        db_close($conn);
                    }
                    
                    echo '</textarea>';
                ?>

                <br />

                <button class="btn btn-primary" type="submit">Senden</button>
            </form>
        </div>
        
        <div class="span6" id="formathelp">
            <?php include 'formathelp.html' ?>
        </div>
    </div>

  
    <div class="container">
        <div class="span12"><br />
            <?php
            
                $conn = db_connect();
                
                
                get_last_posts(100, true);
                
                db_close($conn);
                
            ?>
        </div>
    </div>
  
  
  </body>
</html>
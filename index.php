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
        
        include 'lib/db.php';
        include 'lib/post-mgmt.php';
        
        $conn = db_connect();
        
        if(isset($_GET['id'])){
                $singleid = $_GET['id'];
                echo "<hr /><br />\n";
                echo print_single_post($singleid);
        } else {
            echo '<div id="options">

        <button id="b_load" class="btn">Neue Posts laden</button>
        <button id="b_toggle_auto" class="btn btn-primary">Automatische Aktualisierung: AN</button>
        <a name="top" href="#bottom">zum&nbsp;Anfang&nbsp;↓</a><br />
        
        </div>
        
        <div id="latest_comments" class="pull-right well hidden-phone">
        <h4>Letzte Kommentare:</h4>';
        print_list_of_last_comments(20, false);
        echo '</div>
        
        <div id="updates" style="border:none; background-color: none;"> </div>
        
        <div class="span6"><hr /></div>

        
        ';
            echo print_posts_since(298, false);
            
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
            $.get('getpostssince.php?id=' + last_post_id, function(data) {
                  var new_max = data.split("\n", 1);
                  last_post_id = parseInt(new_max[0]);
                  $('#updates').prepend(data.slice(new_max[0].length + 1));
            });
            $.get('lastcomments.php?id=' + last_comment_id, function(data) {
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
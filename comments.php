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
        
        echo ' <div id="latest_comments" class="well">
        <h4>Letzte Kommentare:</h4>';
        print_list_of_last_comments(20, false);
        echo '</div>';
        
        include 'footer.html' ?>
    </div>
  
  
    <script type="text/javascript">
        var last_comment_id = <?php $conn = db_connect();echo last_approved_comment_id();db_close($conn); ?>;
        
        var refresh_interval = 20000;
        
        var refresh = true;
        var refresh_auto = window.setInterval("loadContent()", refresh_interval);
        
        

        function loadContent()
        {
            $.get('lastcomments.php?id=' + last_comment_id, function(data) {
                  if(!(data=="")){
                        var new_max = data.split("\n", 1);
                        last_comment_id = parseInt(new_max[0]);
                        $('#latest_comments').html('<h4>Letzte Kommentare:</h4>' + data.slice(new_max[0].length + 1));
                  }
            });
        }

        

    </script>

  
 </body>
</html>
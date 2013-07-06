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
        
        include 'db.php';
        
        $conn = db_connect();
        
        if(isset($_GET['id'])){
                $singleid = $_GET['id'];
                echo "<hr /><br />\n";
                echo get_post_with_id($singleid);
        } else {
            echo '<div id="options">

        <button id="b_load" class="btn">Neue Posts laden</button>
        <button id="b_toggle_auto" class="btn btn-primary">Automatische Aktualisierung: AN</button>
        <a name="top" href="#bottom">zum&nbsp;Anfang&nbsp;↓</a><br />
        
        </div>
        
        <div id="updates" style="border:none; background-color: none;"> </div>
        
        <div class="span6"><hr /></div>
        
        ';
            echo get_posts_since(0);
            
            echo '<div class="span12"><a name=bottom href="#top">Nach Oben ↑</a></div>';
        }
        
        db_close($conn);
        
        ?>
        
        <?php include 'footer.html' ?>
    </div>
  
  
  
    <script type="text/javascript">
        var last_post_id = <?php $conn = db_connect();echo max_id();db_close($conn); ?>;

        var xmlHttpObject = false;
        
        var refresh_interval = 20000;
        
        var refresh = true;
        var refresh_auto = window.setInterval("loadContent()", refresh_interval);
        
        // Buttons
        document.getElementById("b_load").onclick = function () {
            loadContent();
        }
        
        document.getElementById("b_toggle_auto").onclick = function () {
            if (refresh) {
                window.clearInterval(refresh_auto);
                document.getElementById("b_toggle_auto").childNodes[0].nodeValue = "Automatische Aktualisierung: AUS";
                $("#b_toggle_auto").removeClass("btn-primary");
                refresh = false;
            } else {
                window.setInterval("loadContent()", refresh_interval);
                document.getElementById("b_toggle_auto").childNodes[0].nodeValue = "Automatische Aktualisierung: AN";
                $("#b_toggle_auto").addClass("btn-primary");
                refresh = true;
            }
            
        }
        
        // ActiveX-Kram
        
        if (typeof XMLHttpRequest != 'undefined') 
        {
            xmlHttpObject = new XMLHttpRequest();
        }
        if (!xmlHttpObject) 
        {
            try 
            {
                xmlHttpObject = new ActiveXObject("Msxml2.XMLHTTP");
            }
            catch(e) 
            {
                try 
                {
                    xmlHttpObject = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch(e) 
                {
                    xmlHttpObject = null;
                }
            }
        }

        function loadContent()
        {
            xmlHttpObject.open('get','getpostssince.php?id=' + last_post_id);
            xmlHttpObject.onreadystatechange = handleContent;
            xmlHttpObject.send(null);
            return false;
        }

        function handleContent()
        {
            if (xmlHttpObject.readyState == 4)
            {
                //document.getElementById('updates').innerHTML = xmlHttpObject.responseText;
                var new_max = xmlHttpObject.responseText.split("\n", 1);
                last_post_id = parseInt(new_max[0]);
                $('#updates').prepend(xmlHttpObject.responseText.slice(new_max[0].length + 1));
            }
        }

    </script>
  
 </body>
</html>
<!DOCTYPE HTML>
<html>
  <head>
    <title>livetick - Datenbank installieren</title>
    <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
    <meta content="">
    <link href="../css/bootstrap.css" rel="stylesheet" media="screen">
    <link href="../css/bootstrap-responsive.css" rel="stylesheet">
  </head>
  <body>
  <div class="container" style="margin-top: 2em;">
        <h1>Livetick installieren</h1>
  
  <?php
  
  
    if(is_readable('../config.php') or file_exists('../config.php')){ // install
    
        include '../db.php';

    
        $conn = db_connect();
        
        $user = $_POST['user'];
        $pass = $_POST['pass'];
        
        
    
    
        // install db
        echo 'Erstelle Tabelle für Einträge <br />';
        $query = "CREATE TABLE ".PREFIX."entries (
            ID              int         AUTO_INCREMENT PRIMARY KEY,
            timestamp       timestamp   DEFAULT NOW(),
            content         text,
            user            int         REFERENCES ".PREFIX."users(ID)
        );";
        
        $result = mysql_query($query) or die("Anfrage fehlgeschlagen: " . mysql_error());
        
        echo 'Erstelle Tabelle für Benutzer <br />';
        $query = "CREATE TABLE ".PREFIX."users (
            ID              int           AUTO_INCREMENT PRIMARY KEY,
            username        VARCHAR(255)  UNIQUE,
            password        text
        );";
        
        $result = mysql_query($query) or die("Anfrage fehlgeschlagen: " . mysql_error());
        
        echo 'Erstelle Benutzer '. $user .' <br />';
        
        create_or_update_user($user, $pass);
        
        echo 'Installation abgeschlossen.<br /> Sie können die Seite nun schließen.';

    
        db_close($conn);
    } elseif(isset($_POST['dbuser'], $_POST['dbpass'], $_POST['dbname'], $_POST['dbprefix'], $_POST['dbprefix'], $_POST['basedir'])) { // create file 

        
        $file = fopen('../config.php', 'w');
        
        if($file){
        
        $filecontent = '<?php
/* Imports for username, password, database, database prefix and base directory */
 define("USER", "'.$_POST['dbuser'].'");
 define("PASS", "'.$_POST['dbpass'].'");
 define("DATABASE", "'.$_POST['dbname'].'");
 define("PREFIX", "'.$_POST['dbprefix'].'");
 define("BASEDIR", "'.$_POST['basedir'].'");
 define("PASSSALT", "'.$_POST['passsalt'].'");
 define("EVENT", "'.$_POST['event'].'");
?>';
        
        fwrite($file, $filecontent);
        
        fclose($file);
        ?>
        <form method="post" action="install.php">
        <input type="hidden" name="user" value="<?php echo $_POST['user']; ?>">
        <input type="hidden" name="pass" value="<?php echo $_POST['pass']; ?>">
        
        <button class="btn">Weiter</button>
        </form>
        <?php
        } else {
            echo 'Fehler beim Öffnen der Datei! Eventuell fehlende Schreibrechte.';
        }
        
        echo $_POST['dbuser'] . '<br />';
        echo $_POST['dbpass'] . '<br />';
        echo $_POST['dbname'] . '<br />';
        echo $_POST['dbprefix'] . '<br />';
        echo $_POST['basedir'] . '<br />';
        echo $_POST['passsalt'] . '<br />';
        echo $_POST['event'] . '<br />';
        echo $_POST['user'] . '<br />';
        
        echo '<br />Installation wird gestartet...';
    } else {
    ?>
        
        <form method="post" action="install.php">
            <p>Benutzername für Datenbank<br /><input name="dbuser" type="text" size="30" maxlength="120" value="dbuser" /></p>
            <p>Passwort für Datenbank<br /><input name="dbpass" type="text" size="30" maxlength="120" value="dbpass" /></p>
            <p>Name der Datenbank<br /><input name="dbname" type="text" size="30" maxlength="120" value="livetickdb" /></p>
            <p>Datenbank-Präfix<br /><input name="dbprefix" type="text" size="30" maxlength="120" value="livetick_" /></p>
            <p>Verzeichnis auf Webserver<br /><input name="basedir" type="text" size="30" maxlength="120" value="/livetick" /></p>
            <p>Passwort-Salt<br /><input name="passsalt" type="text" size="30" maxlength="120" value="d78ea1ad4c75af2123391fce7da0c374" /></p>
            <p>Event<br /><input name="event" type="text" size="30" maxlength="120" value="SP-Sitzung vom 01.01.1970" /></p>
            <div class="control-group">
                <label class="control-label" for="inputUser">Benutzername</label>
                <div class="controls">
                    <input type="text" id="inputUser" name="user" placeholder="Benutzername">
                </div>
            </div>
            <div class="control-group">
                <label class="control-label" for="inputPassword">Passwort</label>
                <div class="controls">
                    <input type="password" id="inputPassword" name="pass" placeholder="Passwort">
                </div>
            </div>
            <button class="btn">Erstellen</button>
        </form>
        
    
    <?php
    }
  
  ?>
    </div>
  </body>
</html>
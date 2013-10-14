<?php 

function get_number_of_posts(){

    $query = "SELECT COUNT(ID) AS count
            FROM ".PREFIX."entries;";
      $result = mysql_query($query) or die("print_table_posts_per_user: Anfrage fehlgeschlagen: " . mysql_error());
      
      if($row = mysql_fetch_array($result)){
            $count    = $row['count'];
            return intval($count);
      } else {
		return -1;
      }

      mysql_free_result($result);
}

function get_number_of_comments($approved='both'){
	
	if($approved === 'both'){
		$query = "SELECT COUNT(ID) AS count FROM ".PREFIX."comments;";
	} elseif($approved === 'yes'){
		$query = "SELECT COUNT(ID) AS count FROM ".PREFIX."comments WHERE approved = 1;";
	} elseif($approved === 'no'){
		$query = "SELECT COUNT(ID) AS count FROM ".PREFIX."comments  WHERE approved = 0;";
	} else {
		return -1;
	}
	
      $result = mysql_query($query) or die("get_number_of_comments: Anfrage fehlgeschlagen: " . mysql_error());
      
      if($row = mysql_fetch_array($result)){
            $count    = $row['count'];
            return intval($count);
      } else {
		return -1;
      }

      mysql_free_result($result);
}

// echoes a table showing post count per registered poster
function print_table_posts_per_user(){

    $query = "SELECT username, COUNT(user) AS posts
            FROM ".PREFIX."entries JOIN ".PREFIX."users ON ".PREFIX."entries.user = ".PREFIX."users.ID
            GROUP BY username
            ORDER BY posts DESC;";
      $result = mysql_query($query) or die("print_table_posts_per_user: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output
      echo "<table class='table table-hover table-bordered table-condensed'><tr><th>User</th><th>Posts</th></tr>\n";

      while($row = mysql_fetch_array($result)){
            $username    = $row['username'];
            $posts    = $row['posts'];
            echo "<tr><td>$username</td><td>$posts</td></tr>\n";
      }
      
      echo "</table>\n";

      mysql_free_result($result);
}

// echoes a table showing comment count per entered username
function print_table_comments_per_name(){

    $query = "SELECT username, COUNT(ID) AS comments
            FROM ".PREFIX."comments
            GROUP BY username
            ORDER BY comments DESC;";
      $result = mysql_query($query) or die("print_table_comments_per_name: Anfrage fehlgeschlagen: " . mysql_error());
      
      // HTML output
      echo "<table class='table table-hover table-bordered table-condensed'><tr><th>Name</th><th>Posts</th></tr>\n";

      while($row = mysql_fetch_array($result)){
            $username    = $row['username'];
            $comments    = $row['comments'];
            echo "<tr><td>$username</td><td>$comments</td></tr>\n";
      }
      
      echo "</table>\n";

      mysql_free_result($result);
}

function register_visit($ip){
	// anonymize ip
	$visitor = crypt($ip, VISITSALT);
	
	// calculate timestamp mod 10
	$time = time();
	$mins = substr(date('i:s', $time), 0, 1);
	$timestamp = date('Y-m-d H:', $time);
	$timestamp .= $mins . '0:00';

	// check if already exists
	$query = "SELECT ID FROM ".PREFIX."visits WHERE visitor LIKE '$visitor' AND timestamp = '$timestamp';";
      $result = mysql_query($query) or die("register_visit: Anfrage 1 fehlgeschlagen: " . mysql_error());
      // if not: enter into db
      if($row = mysql_fetch_array($result)){
		//nothing
		mysql_free_result($result);
      } else {
		mysql_free_result($result);
		//insert it
		$query = "INSERT INTO ".PREFIX."visits(visitor, timestamp) VALUES ('$visitor', '$timestamp');";
		$result = mysql_query($query) or die("register_visit: Anfrage fehlgeschlagen: " . mysql_error());
		if(!$result){
			echo "register_visit: Anfrage 2 fehlgeschlagen: " . mysql_error() . "<br/>";
		}
      }
}

function get_current_number_of_visitors(){
	
	// calculate timestamp mod 10
	$time = time();
	$mins = substr(date('i:s', $time), 0, 1);
	$timestamp = date('Y-m-d H:', $time);
	$timestamp .= $mins . '0:00';
	
	$query = "SELECT COUNT(ID) AS count FROM ".PREFIX."visits  WHERE timestamp = '$timestamp';";
	
      $result = mysql_query($query) or die("get_current_number_of_visitors: Anfrage fehlgeschlagen: " . mysql_error());
      
      if($row = mysql_fetch_array($result)){
            $count    = $row['count'];
            return intval($count);
      } else {
		return -1;
      }

      mysql_free_result($result);
}

function labels_last_hours($count){
	$count = intval($count) * 6;
	// calculate timestamp mod 10
	$time = time();
	$mins = substr(date('i:s', $time), 0, 1);
	$timestamp = date('Y-m-d H:', $time);
	$timestamp .= $mins . '0:00';
	
	$tenminutes = date_interval_create_from_date_string('10 minutes');
	
	$labels = Array();
	
	$timestamp = date_create($timestamp);
	
	for($i = $count; $i >= 0; $i = $i - 1){
		$labels[$i] = date_format($timestamp, "H:i");
		$timestamp = date_sub($timestamp, $tenminutes);
	}
	
	$out = "labels: ['";
	for($i = 0; $i < $count; $i = $i + 1){
		$out .= $labels[$i]."','";
	}
	$out .= $labels[$count]."'],\n";
	return $out;
}

function data_last_hours($count){
	$count = intval($count) * 6;
	// calculate timestamp mod 10
	$time = time();
	$mins = substr(date('i:s', $time), 0, 1);
	$timestamp = date('Y-m-d H:', $time);
	$timestamp .= $mins . '0:00';
	
	$tenminutes = date_interval_create_from_date_string('10 minutes');
	
	$stamps = Array();
	
	$timestamp = date_create($timestamp);
	
	for($i = $count; $i >= 0; $i = $i - 1){
		$stamps[$i] = date_format($timestamp, "Y-m-d H:i:s");
		$timestamp = date_sub($timestamp, $tenminutes);
	}
	
	$visitors = Array();
	
	$query = "SELECT timestamp, COUNT(visitor) AS count FROM ".PREFIX."visits WHERE timestamp >= '".$stamps[0]."' GROUP BY timestamp ORDER BY timestamp ASC;";
	$result = mysql_query($query) or die("register_visit: Anfrage 1 fehlgeschlagen: " . mysql_error());
	
	$index = 0;
      while($row = mysql_fetch_array($result)){
		$ts    = $row['timestamp'];
		$r_count = $row['count'];
		
		while($stamps[$index] != $ts){
			$visitors[$index] = 0;
			$index = $index + 1;
		} 
		$visitors[$index] = intval($r_count);
		$index = $index + 1;
      }
      
      for(; $index <= $count; $index = $index + 1){
		$visitors[$index] = 0;
      }
	
	
	$out = "data: ['";
	for($i = 0; $i < $count; $i = $i + 1){
		$out .= $visitors[$i]."','";
	}
	$out .= $visitors[$count]."'],\n";
	return $out;
}

function labels_last_days($count){
	$count = intval($count);
	// calculate timestamp mod 10
	$time = time();
	$timestamp = date('Y-m-d', $time);
	
	$oneday = date_interval_create_from_date_string('1 day');
	
	$labels = Array();
	
	$timestamp = date_create($timestamp);
	
	for($i = $count; $i >= 0; $i = $i - 1){
		$labels[$i] = date_format($timestamp, "Y-m-d");
		$timestamp = date_sub($timestamp, $oneday);
	}
	
	$out = "labels: ['";
	for($i = 0; $i < $count; $i = $i + 1){
		$out .= $labels[$i]."','";
	}
	$out .= $labels[$count]."'],\n";
	return $out;
}

function data_last_days($count){
	$count = intval($count);
	$time = time();
	$timestamp = date('Y-m-d', $time);
	
	$oneday = date_interval_create_from_date_string('1 day');
	
	$stamps = Array();
	
	$timestamp = date_create($timestamp);
	
	for($i = $count; $i >= 0; $i = $i - 1){
		$stamps[$i] = date_format($timestamp, "Y-m-d");
		$timestamp = date_sub($timestamp, $oneday);
	}
	
	$visitors = Array();
	
	$query = "SELECT date(timestamp) AS date, COUNT(DISTINCT visitor) AS count FROM livetickspbn_visits WHERE timestamp >= '".$stamps[0]."' GROUP BY year(timestamp), month(timestamp), day(timestamp) ORDER BY date ASC";
	$result = mysql_query($query) or die("data_last_days: Anfrage 1 fehlgeschlagen: " . mysql_error());
	
	$index = 0;
      while($row = mysql_fetch_array($result)){
		$ts    = $row['date'];
		$r_count = $row['count'];
		
		while($stamps[$index] != $ts){
			$visitors[$index] = 0;
			$index = $index + 1;
		} 
		$visitors[$index] = intval($r_count);
		$index = $index + 1;
      }
      
      for(; $index <= $count; $index = $index + 1){
		$visitors[$index] = 0;
      }
	
	
	$out = "data: ['";
	for($i = 0; $i < $count; $i = $i + 1){
		$out .= $visitors[$i]."','";
	}
	$out .= $visitors[$count]."'],\n";
	return $out;
}

function print_visitor_chart($id, $count, $type="hours"){
	if($type === "days"){
		$labels = labels_last_days($count);
		$data = data_last_days($count);
	} else {
		$labels = labels_last_hours($count);
		$data = data_last_hours($count);
	}
	
	$id = intval($id);
	$count = intval($count);
	echo "<canvas id='visitorChart_$id' height='400' width='800'></canvas>\n";
	echo "<script type='text/javascript'>\n";
	echo '	//Get context with jQuery - using jQuerys .get() method.
	var ctx_'.$id.' = $("#visitorChart_'.$id.'").get(0).getContext("2d");
	//This will get the first returned node in the jQuery collection.
	var myVisitorChart = new Chart(ctx_'.$id.');';
	
	echo '	var data_'.$id.' = {
		'.$labels.'
		datasets : [
			{
				fillColor : "rgba(220,220,220,0.5)",
				strokeColor : "rgba(220,220,220,1)",
				pointColor : "rgba(220,220,220,1)",
				pointStrokeColor : "#fff",
				'.$data.'
			}
		]
	}
	
	';
	
	($type === "days") ? $scaleStepWidth = '5' : $scaleStepWidth = '1';
	
	echo '	var options_'.$id.' = {
				
		//Boolean - If we show the scale above the chart data			
		scaleOverlay : false,
		
		//Boolean - If we want to override with a hard coded scale
		scaleOverride : true,
		
		//** Required if scaleOverride is true **
		//Number - The number of steps in a hard coded scale
		scaleSteps : 20,
		//Number - The value jump in the hard coded scale
		scaleStepWidth : '. $scaleStepWidth .',
		//Number - The scale starting value
		scaleStartValue : 0,

		//String - Colour of the scale line	
		scaleLineColor : "rgba(0,0,0,.1)",
		
		//Number - Pixel width of the scale line	
		scaleLineWidth : 1,

		//Boolean - Whether to show labels on the scale	
		scaleShowLabels : true,
		
		//Interpolated JS string - can access value
		scaleLabel : "<%=value%>",
		
		//String - Scale label font declaration for the scale label
		scaleFontFamily : "\'Arial\'",
		
		//Number - Scale label font size in pixels	
		scaleFontSize : 12,
		
		//String - Scale label font weight style	
		scaleFontStyle : "normal",
		
		//String - Scale label font colour	
		scaleFontColor : "#666",	
		
		///Boolean - Whether grid lines are shown across the chart
		scaleShowGridLines : true,
		
		//String - Colour of the grid lines
		scaleGridLineColor : "rgba(0,0,0,.05)",
		
		//Number - Width of the grid lines
		scaleGridLineWidth : 1,	
		
		//Boolean - Whether the line is curved between points
		bezierCurve : true,
		
		//Boolean - Whether to show a dot for each point
		pointDot : true,
		
		//Number - Radius of each point dot in pixels
		pointDotRadius : 3,
		
		//Number - Pixel width of point dot stroke
		pointDotStrokeWidth : 1,
		
		//Boolean - Whether to show a stroke for datasets
		datasetStroke : true,
		
		//Number - Pixel width of dataset stroke
		datasetStrokeWidth : 2,
		
		//Boolean - Whether to fill the dataset with a colour
		datasetFill : true,
		
		//Boolean - Whether to animate the chart
		animation : true,

		//Number - Number of animation steps
		animationSteps : 60,
		
		//String - Animation easing effect
		animationEasing : "easeOutQuart",

		//Function - Fires when the animation is complete
		onAnimationComplete : null
	}
	
	';
	
	
	echo "	new Chart(ctx_$id).Line(data_$id,options_$id);";
	
	echo "</script>\n";
	
}

?>
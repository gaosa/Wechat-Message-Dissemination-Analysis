<?php
if(!empty($_POST['uid'])){
	$dbname = $_POST['db'];

        if($dbname == "share"){
		$db = mysqli_connect("127.0.0.1", "root", "", "db_GSA");
    		$query = "INSERT INTO share(User_ID, Time) VALUES('".$_POST['uid']."',".time().")";
    		$result = mysqli_query($db, $query);
    		if (!$result) {
			print "AError - the query could not be executed";
			$error = mysqli_error($db);
			print "<p>" . $error . "</p>";
			exit;
    		}
	}
	else if($dbname == "pages"){
		$db = mysqli_connect("127.0.0.1", "root", "", "db_GSA");
    		$query = "SELECT * FROM pages WHERE User_ID='".$_POST['uid']."'";
    		$result = mysqli_query($db, $query);
		if (!$result){
			print "EError - the query could not be executed";
			$error = mysqli_error($db);
			print "<p>" . $error . "</p>";
			exit;
		}
    		if (mysqli_num_rows($result) == 0) {
			$query = "INSERT INTO pages(User_ID, User_Index, Time) VALUES('".$_POST['uid']."',".$_POST['index'].",".time().")";
    			$result = mysqli_query($db, $query);
			if (!$result){
				print "CError - the query could not be executed";
				$error = mysqli_error($db);
				print "<p>" . $error . "</p>";
				exit;
			}
    		}
		else{
			$row=mysqli_fetch_array($result);
			$prev_index = $row["User_Index"];
			if($prev_index < $_POST['index']){
				$query = "UPDATE pages SET User_Index=".$_POST['index'].", Time=".time()." WHERE User_ID='".$_POST['uid']."'";
    				$result = mysqli_query($db, $query);
				if (!$result){
					print "DError - the query could not be executed";
					$error = mysqli_error($db);
					print "<p>" . $error . "</p>";
					exit;
				}
			}
		}
	}
}
?>

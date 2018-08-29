<?php
	$db = mysqli_connect("127.0.0.1", "root", "", "db_GSA");
	$query = "SELECT * from pages";
	$result = mysqli_query($db, $query);
	$myfile = fopen("pages.txt", "w") or die("Unable to open file!");
	$num_rows = mysqli_num_rows($result);
	for($row_num=0; $row_num < $num_rows; $row_num ++){
		$row=mysqli_fetch_array($result);
		$txt = $row["User_ID"].",".$row["User_Index"].",".$row["Time"]."\n";
		fwrite($myfile, $txt);
	}
	fclose($myfile);
		
	$query = "SELECT * from share";
	$result = mysqli_query($db, $query);
	$myfile = fopen("share.txt", "w") or die("Unable to open file!");
	$num_rows = mysqli_num_rows($result);
	for($row_num=0; $row_num < $num_rows; $row_num ++){
		$row=mysqli_fetch_array($result);
		$txt = $row["User_ID"].",".$row["Time"]."\n";
		fwrite($myfile, $txt);
	}
	fclose($myfile);

	$query = "SELECT * from path";
	$result = mysqli_query($db, $query);
	$myfile = fopen("path.txt", "w") or die("Unable to open file!");
	$num_rows = mysqli_num_rows($result);
	for($row_num=0; $row_num < $num_rows; $row_num ++){
		$row=mysqli_fetch_array($result);
		$txt = $row["From_ID"].",".$row["To_ID"].",".$row["Time"]."\n";
		fwrite($myfile, $txt);
	}
	fclose($myfile);
?>

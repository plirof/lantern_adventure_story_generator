<html>
<head>
<title>worklog - class lesson logger  v005 160302</title>
<!--
210201 - http://192.168.1.200/class-worklog/index.php?backup=true


-->

<script>
	 function autoScrolling() { window.scrollTo(0,document.body.scrollHeight); }
	//setInterval(autoScrolling, 1000); //added by jon 160218 autoscroll bottom of page
	//autoScrolling()
</script>
<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
<meta http-equiv="content-type" content="application/xhtml+xml; charset=UTF-8" />
</head>
<?php

// Database file, i.e. file with real data
$data_file = 'worklog.txt';

// Database definition file. You have to describe database format in this file.
// See flatfile.inc.php header for sample.
$structure_file = 'worklog.def';

// Fields delimiter
$delimiter = '|';

// Number of header lines to skip. This is needed if you have some heder saved in the 
// database file, like comment or description
$skip_lines = 0;

//Backup using parameter http://192.168.1.200/class-worklog/index.php?backup=true
$backup=false;
if (@$_REQUEST["backup"]){
	if (file_exists($data_file) ){
		//echo "<h3>EXISTS BACKUP $data_file</h3>";
		$backup=true;
		if($backup) echo copy($data_file,$data_file."_backup_".date("Ymd").".txt");
	}

}// else echo "<h3>NOT EXISTS BACKUP $data_file</h3>";


// run flatfile manager
include ('flatfile.inc.php');

?>
<script>
autoScrolling();
</script>

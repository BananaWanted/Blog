<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<title>PHP TEST</title>
</head>

<body>
<div>

<h1>PHP BASIC TESTING...</h1>
	<?php
		echo("<br /><h2>\$_SERVER</h2>");
		foreach($_SERVER as $name=>$value)
		{
			echo($name."=".$value."<br />");
		}
		echo("<br /><h2>\$_REQUEST</h2>");
		foreach($_REQUEST as $name=>$value)
		{
			echo($name."=".$value."<br />");
		}
		echo("<br /><h2>\$_POST</h2>");
		foreach($_POST as $name=>$value)
		{
			echo($name."=".$value."<br />");
		}
		echo("<br /><h2>\$_GET</h2>");
		foreach($_GET as $name=>$value)
		{
			echo($name."=".$value."<br />");
		}
                echo("<br /><h2>\$_FILES</h2>");
		foreach($_FILES as $name=>$value)
		{
                    var_dump($_FILES);
		}
	?>
<hr />
<h1>Date Testing...</h1>
	<?php
		echo("__FILE__=".__FILE__."<br />");
		
		#date_default_timezone_set('Asia/Shanghai');
		echo ('现在时间：'.date('Y年m月d日, h:i:s a, l', $_SERVER[REQUEST_TIME]));
		#$myfile = fopen("webdictionary.txt", "r") or die("Unable to open file!");
		#echo $myfile;
	?>
</div>
</body>
</html>
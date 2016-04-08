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
		$arraytest=array("abc",4,'x',0xdf,012,"胖子是傻逼");
		echo("<h2>for</h2>");
		for($i=0;$i<count($arraytest);$i++)
		{
			echo($arraytest[$i]."<br />");
		}
		echo("<h2>foreach</h2>");
		foreach($arraytest as $index=>$value)
		{
			echo("key=$index, value=$value"."<br />");
		}
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
	
<br /><hr /><br /><h1>File Testing...</h1>
	<?php
        /*
		$file = fopen('000.a',r) or die('Can not open file');
	?>
<h2>File Begining...</h2>
	<?php
		while(!feof($file)) {
			echo '<pre>'.htmlspecialchars(fgets($file)).'<br /></pre>';
		}
		fclose($file);
	?>
<h2>File Ending...</h2>
<h2>Writting File...</h2>
	<?php
		$file=fopen('000.a',wb) or die('Can not open file');
		fwrite($file,"a line with \\n end\n");
		fwrite($file,"a line with \\r\\n end\r\n");
		#fwrite($file,"a line with \\n\\r end\n\r");  #Error
		fwrite($file,"a line with \\r end\r");
         */
	?>
<hr />
<h1>File Uploding Testing...</h1>
	<?php
		//phpinfo();
	?>
	
<form action="fileupload.php" method="post" enctype="multipart/form-data">
<div style="border:1px solid; padding:0; margin:0;" ><label style="border:1px solid" for="file1">Filename1:</label>
<input style="border:1px solid" type="file" name="file1" id="file1" /> </div>
<div style="border:1px solid; padding:0; margin:0;" ><label style="border:1px solid" for="file2">Filename2:</label>
<input style="border:1px solid" type="file" name="file2" id="file2" /> </div>
<br />
<input type="submit" name="submit" value="upload" />
</form>

<hr />
<pre>
<?php
$part='abc.def.ghi.jkl.mno.pqr.stu.vwx.yz';
$epart=explode('.', $part);
$epartlist=new ArrayIterator($epart);
foreach ($epartlist as $value)
    echo $value;
$rererere=array_pop($epart);
echo is_array($rererere)?'true':'false';
echo $rererere;
?>

<?php

echo (int)false;
error_reporting('reporting_costom_error...');
echo 'over';
?>
</pre>

</div>
</body>
</html>
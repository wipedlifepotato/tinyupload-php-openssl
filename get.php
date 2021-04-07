<?php
error_reporting(0);
session_start();
?>

<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
<title>CHUDO.I2P | MIRACLE MAKER</title>
<link href="../chudo.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="chudo.svg">
</head>
<body>

<div id="container">
<div id="top">
<div id="header">
<span id="logo"><span id="ident"><a href="/">CHUDO.I2P</a></span></span>
</div>
<div id="nav">
<a href="/development.html">Development</a>
<a href="/tinyupload/get.php">Uploader</a>
<a href="/xmpp.html">XMPP</a>
<a href="/links.html">Links</a>
</div>
</div>
<div id="main">
<?php
error_reporting(0);
session_start();
?>
<form action=get.php enctype="multipart/form-data" method="post">
	Your file: <input type=file name="inputs"><br/>
	Key if want(none is no key...): <input type=text value=none name=key><br/>
	Chipher: <select name=chipher> </br>
		<?php
			foreach( openssl_get_cipher_methods() as $method ){
				echo "<option>$method</option>";
			}
		?>
	</select><br>
<!--	<img id=captcha src="captcha.php" alt="captcha"><br>
	<input type="textarea" name=captcha placeholder="Write what you see on picture."><br>-->
	<input type=submit><br/>
	<a href='https://github.com/wipedlifepotato/tinyupload-php-openssl'> Source Code </a>
</form>
<?php
	$iv="";
	$tag="";
	$exitContent = "";
	$filename="";
	if(!isset($_POST['key']) ) $_POST['key'] = 'none';
	$key=$_POST['key'];
	$fileUploadDir='./uploads';
	$chipher = 'none';
	if(isset($_FILES['inputs']) && $_FILES['inputs']['error'] == 0 && isset($_POST['captcha'])){
		//if(! (hash("sha256",$_POST['captcha']) == $_SESSION['SCAPTCHA']) ) die("uncorrect captcha");
		//var_dump($_FILES['inputs']);
		$filename=$_FILES['inputs']['name'];
		echo 'openning: '.$filename."<hr/>";

		$f = fopen($_FILES['inputs']['tmp_name'], "rb");
		if(!$f) die("can't read tmp file");
		$content =   fread($f, filesize($_FILES['inputs']['tmp_name'])) ;
		$exitfilename=sha1($content.$_FILES['inputs']['name']);

		if(file_exists( "$fileUploadDir/$exitfilename" ) ) die('already uploaded. change any byte in file... or name...');

		if($content === FALSE) die('cant read tmp file');
		fclose($f);
		if( isset($_POST['key']) && $_POST['key'] != "none" ){

			$chipher = $_POST['chipher'];
			if ( in_array( $chipher, openssl_get_cipher_methods() ) ){
				echo "encoding .. by key - ".$_POST['key']."</br>";
				$ivlen = openssl_cipher_iv_length($chipher);
				$iv = openssl_random_pseudo_bytes($ivlen);
				$exitContent  = base64_encode(
					openssl_encrypt($content, $chipher, $key, $options=0, $iv, $tag) 
					);
				
			}	
		}
		if ( $exitContent == "" ) $exitContent = base64_encode($content);

		$f=fopen( "$fileUploadDir/$exitfilename", "wb" );
		fwrite($f, $exitContent) or die('cant write exit file');
		fclose($f);

		printf(
	"Your link is (without key): <a href=\"send.php?file_name=%s&iv=%s&tag=%s&exitfilename=%s&ch=%s\">link</a> <hr/>"
	,$filename, base64_encode($iv), base64_encode($tag), $exitfilename, $chipher ); 
		printf(
	"Your link is (with key): <a href=\"send.php?file_name=%s&iv=%s&tag=%s&exitfilename=%s&key=%s&ch=%s\">link</a> <hr/>"
	,$filename, base64_encode($iv), base64_encode($tag), $exitfilename, urlencode($key), $chipher ); 
		
		
	}else{
		echo "WaIt FoR FiLe(30 MB max)";
	}

?>

<div id="footer">&copy; 2050 CHUDO.I2P &nbsp;&bullet;&nbsp; ALL RIGHTS CONSUMED ON THE PREMISES</div>
</div>
</body>
</html>


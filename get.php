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
	<img id=captcha src="captcha.php" alt="captcha"><br>
	<input type="textarea" name=captcha placeholder="Write what you see on picture."><br>
	<input type=submit><br/>
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
		if(! (hash("sha256",$_POST['captcha']) == $_SESSION['SCAPTCHA']) ) die("uncorrect captcha");
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
	,$filename, base64_encode($iv), base64_encode($tag), $exitfilename, $key, $chipher ); 
		
		
	}else{
		echo "WaIt FoR FiLe(30 MB max)";
	}

?>



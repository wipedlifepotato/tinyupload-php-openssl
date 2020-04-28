<?php
	if(!isset($_GET['exitfilename']) || !isset($_GET['file_name'] ) ) exit(0);
	$fileUploadDir='./uploads';
	$exitfilename = $_GET['exitfilename'];
	if ( ! file_exists( "$fileUploadDir/$exitfilename" ) ) die("file not found");
	$f = fopen ( "$fileUploadDir/$exitfilename", "rb");
	if(!$f) die("cant open file");
	$Content = base64_decode( fread($f, filesize("$fileUploadDir/$exitfilename")) );
	fclose($f);
	if($Content  === FALSE) die('cant read file');
	//echo filesize("$fileUploadDir/$exitfilename");
	//Your link is (with key): 
	//<a href=\"send.php?=%s&iv=%s&=%s&=%s&=%s&ch='%s'\"

	$file_name=$_GET['file_name'];


	
	if( ! isset( $_GET['ch'] ) ) die('CHIPHER?');
	/*
	*/
	if( $_GET['ch'] == 'none' ){
		if($Content === FALSE) die("its encrypted...");
	}else{
		if(!isset($_GET['iv']) || !isset($_GET['tag']) || !isset($_GET['key']) ) die('not exists key or tag or iv');
		$iv=base64_decode($_GET['iv']);
		$tag=base64_decode($_GET['tag']);
		$key=$_GET['key'];
		if ( !in_array( $_GET['ch'], openssl_get_cipher_methods() ) ) die("unknown chipher");
		$Content = openssl_decrypt($Content, $_GET['ch'], $key, $options=0, $iv, $tag);
	}
	if( strlen($Content) == 0 ) die("Not good key maybe... Not decrypted... raw file is: <a href='$fileUploadDir/$exitfilename'>click</a> ");
	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');	
	header('Content-Disposition: attachment; filename="'.basename($file_name).'"');
	header('Cache-Control: must-revalidate');
	header('Expires: 0');
	header('Pragma: public');
	header('Content-Length: ' . strlen($Content)); 	
	echo $Content;	    		

?>

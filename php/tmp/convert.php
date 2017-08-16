<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
* 
*/
class ConversionChat {
	
	function __construct($uri) {
		$this->uri = "json/conversaciones/$uri.csv";
		if (!is_file($this->uri)){
			$file = fopen($this->uri, "c+");
			fclose($file);
		}
	}

	public function sendMsg($msg) {
		$this->file = fopen($this->uri, "c+");
		$len = filesize($this->uri);
		echo "Len: $len";
		fseek($this->file, $len);
		$msg = date("ymd,His,") . $msg ."\n";
		fwrite($this->file, $msg);

	}

	// public function readMsg() {
	// 	$this
	// }


}


$chat = new ConversionChat($_POST["name"]);
if ($_POST['fun'] == "enviar")
	$chat->sendMsg($_POST["msg"]);

fclose($chat->file);

?>
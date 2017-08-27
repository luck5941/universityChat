#!/usr/bin/env php7.0
<?php

/* Permitir al script esperar para conexiones. */
set_time_limit(0);

/* Activar el volcado de salida implícito, así veremos lo que estamo obteniendo
* mientras llega. */
ob_implicit_flush();
$address = '127.0.0.1';
$port = 10000;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

if (!($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) 
	echo "socket_create() falló: razón: " . socket_strerror(socket_last_error()) . "\n";
elseif (!socket_bind($sock, $address, $port))
	echo "socket_bind() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
elseif (!socket_listen($sock))
	echo "socket_listen() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";

$clients = [];

while(true) {	
	$read = [$sock];
	$read = array_merge($read,$clients);
	$write = NULL;
	$except = NULL;
	$tv_sec = 5;
	
	$socketSelect = socket_select($read, $write, $except, $tv_sec);
	if ($socketSelect <1) continue;
	$msgsock = socket_accept($sock);
	$clients[] = $msgsock;	
	echo "Se ha añadido un nuevo cliente con la clave: ". (count($clients) - 1) . "\n";
	$header = socket_read($msgsock, 1024);
	perform_handshaking($header, $msgsock);

}

function perform_handshaking($receved_header, $msgsock){
	global $host, $port;
	$headers = array();
	$lines = preg_split("/\r\n/", $receved_header);
	foreach($lines as $line){
		$line = chop($line);
		if(preg_match('/\A(\S+): (.*)\z/', $line, $matches)){
			$headers[$matches[1]] = $matches[2];
		}
	}

	$secKey = $headers['Sec-WebSocket-Key'];
	$secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
	//hand shaking header
	$upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
	"Upgrade: websocket\r\n" .
	"Connection: Upgrade\r\n" .
	"WebSocket-Origin: $host\r\n" .
	"WebSocket-Location: ws://$host:$port/demo/shout.php\r\n".
	"Sec-WebSocket-Accept:$secAccept\r\n\r\n";
	socket_write($msgsock,$upgrade,strlen($upgrade));
}



?>
#!/usr/bin/env php
<?php
error_reporting(E_ALL);

/* Permitir al script esperar para conexiones. */
set_time_limit(0);

/* Activar el volcado de salida implícito, así veremos lo que estamo obteniendo
* mientras llega. */
ob_implicit_flush();

$address = '127.0.0.1';
$port = 10000;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() falló: razón: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
}

//clients array
$clients = array();

while(true) {
    $read = array();
    $read[] = $sock;
   
    $read = array_merge($read,$clients);
	$write = NULL;
	$except = NULL;
	$tv_sec = 5;
   
    // Set up a blocking call to socket_select
	$socketSelect = socket_select($read, $write, $except, $tv_sec);
    if( $socketSelect < 1) {
        //    SocketServer::debug("Problem blocking socket_select?");
        continue;
    }
   
    // Handle new Connections
    if (in_array($sock, $read)) {    
       
        if (($msgsock = socket_accept($sock)) === false) {
            echo "socket_accept() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
            break;
        }
        $clients[] = $msgsock;
        $header = socket_read($msgsock, 1024);
        perform_handshaking($header, $msgsock);
        $key = array_keys($clients, $msgsock);
        /* Enviar instrucciones. */
        /*$msg = "\nBienvenido al Servidor De Prueba de PHP. \n" .
        "Usted es el cliente numero: {$key[0]}\n" .
        "Para salir, escriba 'quit'. Para cerrar el servidor escriba 'shutdown'.\n";
        socket_write($msgsock, $msg, strlen($msg));*/
       
    }
   
    // Handle Input
    foreach ($clients as $key => $client) { // for each client       
        if (in_array($client, $read)) {
            if (false === ($buf = socket_read($client, 2048, PHP_NORMAL_READ))) {
                echo "socket_read() falló: razón: " . socket_strerror(socket_last_error($client)) . "\n";
                break 2;
            }
            $buf = unmask($buf);
            if (!$buf = trim($buf)) {
                continue;
            }
            if ($buf == 'quit') {
                unset($clients[$key]);
                socket_close($client);
                break;
            }
            if ($buf == 'shutdown') {
                socket_close($client);
                break 2;
            }
            $talkback = "Cliente {$key}: Usted dijo '$buf'.\n";
            socket_write($client, $talkback, strlen($talkback));
            echo "$buf\n";
        }
       
    }       
}

socket_close($sock);
function perform_handshaking($receved_header,$msgsock){
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

function unmask($text) {
	$length = ord($text[1]) & 127;
	if($length == 126) {
		$masks = substr($text, 4, 4);
		$data = substr($text, 8);
	}
	elseif($length == 127) {
		$masks = substr($text, 10, 4);
		$data = substr($text, 14);
	}
	else {
		$masks = substr($text, 2, 4);
		$data = substr($text, 6);
	}
	$text = "";
	for ($i = 0; $i < strlen($data); ++$i) {
		$text .= $data[$i] ^ $masks[$i%4];
	}
	return $text;
}



?>

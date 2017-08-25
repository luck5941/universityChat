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
	echo "Se ha añadido un nuevo cliente con la clave: ". (count($clients) - 1);


}




?>
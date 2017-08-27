#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import socket, base64, threading
from sys import stdin
# from binascii import unhexlify, hexlify
from select import select
from re import split as reSplit
from re import match as reMatch
from re import sub

from hashlib import sha1, sha256

def changePort():
	f = open('js/main.js', "r")
	r = f.read()
	f.close()
	r = sub(r'port = \d*', 'port = %s' %port, r)
	f = open('js/main.js', "w")
	f.write(r)
	f.close()



sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
address = '127.0.0.1'
port = 10000;
inputMe = [sock, stdin]
LOCK = threading.Lock()
while True:
	try:		
		sock.bind((address, port))
		break
	except:
		port += 1

changePort()

print ("Empezando a levantar el server en el puerto: %s" %port)
sock.listen(128)

clients = []

def perform_handshaking(h, c):
	headers = {};
	lines = reSplit("\r\n", h)
	for line in lines:
		line = line.strip().split(': ')
		try:
			headers[line[0]] = line[1]
		except:
			continue
		# if preg_match('/\A(\S+): (.*)\z/', $line, $matches)
		# 	$headers[$matches[1]] = $matches[2];
		
	secKey = headers['Sec-WebSocket-Key']
	secAccept = secKey + "258EAFA5-E914-47DA-95CA-C5AB0DC85B11"
	s = sha1()
	s.update(secAccept.encode())
	secAccept = base64.encodestring(s.digest()).decode()
	upgrade = "HTTP/1.1 101 Web Socket Protocol Handshake\r\nUpgrade: websocket\r\nConnection: Upgrade\r\nWebSocket-Origin: %s\r\nWebSocket-Location: ws://%s:%s/serverSocket.py\r\nSec-WebSocket-Accept:%s\r\n\r\n" %(address, address, str(port), secAccept)
	
	try:
		c.send(upgrade.encode(), len(upgrade.encode()))
		f = open("headers", "ba+")
		f.write(upgrade.encode())
		f.close()
		return upgrade.encode()
		# print("Despues: " + len(secAccept))
		# print("Ya se ha enviado:\n%s"%upgrade)
	except Exception as e:
		raise e		
		print("el error es: %s aunque no tenga sentido" %e)

def un_mask(msg):
	print("Empezando a desenmascarar")
	f = open('tmp', "w")
	f.write("Antes: " + str(msg))
	f.close()
	if len(msg) <6:
		print("No se ha recivido el mensaje correctamente")
		return
	datalen = (0x7F & msg[1])
	print("datalen vale: %s y msg[1] es: %s" %(datalen, msg[1]) )
	str_data = ''
	if(datalen > 0):
		mask_key = msg[2:6]
		print("mask_key: " + str(mask_key))
		masked_data = msg[6:(6+datalen)]
		print("masked_data: "+str(masked_data))
		unmasked_data = [masked_data[i] ^ mask_key[i%4] for i in range(len(masked_data))]
		print("unmasked_data: "+str(unmasked_data))
		str_data = bytearray(unmasked_data).decode('utf-8')
	f = open('tmp', "a+")
	f.write("\nDespues: " + str(str_data))
	f.close()
	return str_data

def mask(data):
	"""resp = bytearray([0b10000001, len(msg)])
	# append the data bytes
	for d in bytearray(msg	.encode()):
		resp.append(d)

	f = open("toSend", "a+")
	f.write(str(resp)+"\n")
	f.close()
	return resp"""
	print(data)
	l = 0x80 | (0x1 & 0x0f);
	try:
		resp = str(l) + str(len(data)) + data.decode()
		# resp = resp.encode()
	except Exception as e:
		print(e)
		print(type(b'\x80'))
		print(type(bytes(len(data))))
		print(type(data))
		exit()

	# print("Se acabo")
	# socket.close()
	# exit()
	# for d in bytearray(data):
	# 	resp.append(d)
		

	#resp = bytearray([l, len(data)])

	print("Tenemos %s clientes" %len(clients))
	print(resp)
	
	for client in clients:
		try:
			client.send(resp, len(resp))
			print("Enviando")
		except Exception as e:
			print("error sending to a client")
			print(e)



while True:
	print("Esperando conectarse")
	try:
		select(inputMe,[],[])
		# read = [sock]
		# read.update(clients)
		connection, client_address = sock.accept()
		clients.append(connection)
		header = connection.recv(1024)
		# f = open('tmp2', "bw")
		# f.write(header)
		# f.close()
		header = header.decode()
		header = perform_handshaking(header, connection)
		print("Se ha establecido la conexion numero: " + str(len(clients)))		
	except Exception as e:
		print (e)
		sock.close()
		exit()

	msg = connection.recv(1024)
	mask(msg)
	print("Ya se ha enviado")
	#print("Hemos revicido: %s"%msg_)
	#msg = un_mask(msg_)
	#msg = mask(msg)
	# print("\nVamos a enviar la mierda:\n%s"%str(msg))
	# try:
	# 	connection.send(msg, len(msg))
	# 	print("Se ha enviado")
	# except Exception as e:
	# 	print("No se envia nada de nada :/")
	# 	print("por: %s" %e)
	# print(type(msg))
	# f = open('tmp', "bw")
	# f.write(msg)
	# f.close()
	# # print("El mensaje es: "+ msg)




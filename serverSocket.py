#!/usr/bin/env python3
# -*- coding: utf-8 -*-
import socket, sys

sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
address = ''
port = 10000
inputMe = [sock, sys.stdin]
server_addres = ('', 10000)
sock.bind(server_addres)
#sock.bind((address, port))
print ("Empezando a levantar el server en el puerto: %s" %port)
sock.listen()

clients = []

while True:
	inputready,outputready,exceptready = select.select(inputMe,[],[])

	read = [sock]
	read.update(clients)
	msgsock = sock.accept()
	clients.append(msgsock)
	print("Se ha establecido una nueva conexion")

	

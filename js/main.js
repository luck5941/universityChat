'use strict'
var form = $('#send');
var contact = $('#contactos li');
var chatTitle = $('#nombre');
var message = $('input#mensaje');
var messages = $('#conversacion');
var php = "php/process.php" 
var chatName = "";
//----------------------------------------
var log = console.log
var socket = new WebSocket("ws://127.0.0.1:10000/serverSocket.php");
socket.onopen = function(evt) { log('conexion establecida'); }; //on open event
socket.onclose = function(evt) { log('conexion cerrada'); }; //on close event
socket.onmessage = function(evt) { /* do stuff */ }; //on message event
socket.onerror = function(evt) { /* do stuff */ }; //on error event
//socket.send("message"); //send method
//socket.close();

//----------------------------------------------

contact.click(function() {
	chatName = $(this).text()
	chatTitle.text(chatName);
	contact.removeAttr('style');
	$(this).css('background-color', '#a9a9fb');
	window[chatName+ "_class"] = new Chat(chatName);

});

form.submit(function(e) {
	e.preventDefault();
	let toSend = {
		msg : message.val(),
		dest: chatTitle.text()
	}
	var newMessage = '<div class="send">' + message.val() + '</div>';
	messages.append(newMessage);
	window[chatName + "_class"].sendMessage(message.val())
	message.val("");

});

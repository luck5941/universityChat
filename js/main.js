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

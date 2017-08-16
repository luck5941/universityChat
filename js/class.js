'use strict'

class Chat {
	constructor(name) {
		/*
		 *Este metodo se encarga de cargar la conversacion;
		 */
		this.file = 'php/convert.php';
		this.name = name;
	}

	sendMessage(msg) {
		console.log(msg)
		$.post(this.file, { 'fun': "enviar", "msg": msg, "name": this.name })
			.done(function(d) {
				console.log(d)
			})
			.fail(function(xhr, status, error) {
				console.log(xhr);
				console.log(status);
				console.log(error);
			})
	}
}

class Login {

	constructor(method = '') {
		this.method = method;
		this.formPage = 'php/login.php';
		$('#loginPage form').css('display', 'none');
		if (this.method == '') return;
		this.form = $('#' + this.method);
		this.form.css('display', 'block');
	}

	displayCorrectForm() {
		$('#loginPage form').css('display', 'none');
		this.form = $('#' + this.method);
		this.form.css('display', 'block');
	}

	// signin(name, lastName, nick, pssword, mail, cours) {
	sendForm(data) {
		$.post(this.formPage, {'fun': this.method, 'data': data})
		.done(processData())
		.fail(processData(0));
	}
}



'use strict'


class Chat {
	constructor(name) {
		/*
		 *Este metodo se encarga de cargar la conversacion;
		 */
		var her = this
		this.file = 'php/proccess.php';
		$.post(this.file, { 'fun': "newChat", "name": name })
		.done(function(d){
			log(d)
			d = parseInt(d);
			if (d >0)
				her.id = d;
			else her.id = 'noId';
		})
		.fail(function(xhr, status, error) {
			console.log(xhr);
			console.log(status);
			console.log(error);
		});

	}

	sendMessage(msg) {
		
		log(msg)
		log(this)
		$.post(this.file, { 'fun': "send", "msg": msg, "id": this.id })
			.done(function(d) {
				console.log(d)
			})
			.fail(function(xhr, status, error) {
				console.log(xhr);
				console.log(status);
				console.log(error);
			});
		
		/*log(msg)
		log(msg.length)
		var m = {};
		m['msg'] = msg
		m = JSON.stringify(m)
		log(msg)
		socket.send(msg)*/
	}


}

class Login {

	constructor(method = '') {
		this.method = method;
		this.formPage = 'php/proccess.php';
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
		let her = this;
		$.post(this.formPage, {'fun': this.method, 'data': data})
		.done(her.proccessCode)
		.fail(function(a, b ,c){
			console.log(a);
			console.log(b);
			console.log(c);
			console.log('d')
		});
		//.fail(processData(0));
	}

	proccessCode(d) {
		console.log(d)
		d = parseInt(d);
		console.log(d)
		switch (d) {
			case 2:
				alert("Se ha realizado el login correctamente");
				break;
			case 1:
				location = "index.html";
				break;
			case 0:
				break;
			case -1:
				alert("El usuario no está registrado");
				break;
			case -2:
				alert("La contraseña no está en la base de datos");
				break;
			case -3:
				alert("La contraseña no es valida");
				break;
			case -4:
				alert("Rellena todos  los campos");
				break;
			case -5:
				alert("Las contraseñas introducidas no coinciden");
				break;
			case -6:
				alert("El nick ya está registrado");
				break;
			case -7:
				alert("El mail ya está registrado");
				break;
			case -8:
				alert("Estamos teniendo problemas técnicos");
				break;
		}

	}
}



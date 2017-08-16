'use strict'

var form = new Login();
var forms = $('#loginPage form');

$('button').click(function(){
	let method = $(this).index('button')
	if (method == 0)
		form.method = 'singin';
	else if (method == 1)
		form.method = 'login';
	else return;
	form.displayCorrectForm();
});

forms.submit(function(e){
	e.preventDefault();
	let input = $(this).find('input');
	let datas = {};
	for (let i = 0; i< input.length-1; i++)
			datas[$(input[i]).attr('id')] = $(input[i]).val();
	form.sendForm(datas);

});


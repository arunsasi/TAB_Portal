(function(){
	'use strict';

	function checkLogin(){
		var loginAPI = 'login_url';
		var objParam = {
			uname : $('#loginForm #officialemail').val(),
			pswrd : $('#loginForm #password').val(),
			rememberme : ($('#loginForm #rememberme').is(':checked') == true)?'1':'0'
		};

		$.ajax({
			type : "POST",
			url : loginAPI,
			data : objParam,
			dataType : 'json',
			success : function(result) {
				console.log(result);
				if(result.status == 1000){
					//login success. redirect to home
				} else {
					//login failed. show error message
				}
			},
			error : function(err) {
				alert(err.message);
			}
		});
	}

	function addUser(){
		var email = $('#registrationSignUp #officialemail').val();
		var confirmEmail = $('#registrationSignUp #confirmEmail').val();
		if(email !== confirmEmail){
			alert("Email IDs doesn't match");
			return false;
		}
		var pswrd = $('#registrationSignUp #password').val();
		var confirmPswrd = $('#registrationSignUp #confirmPassword').val();
		if(pswrd !== confirmPswrd){
			alert("Passwords doesn't match");
			return false;
		}

		var addUserAPI = 'add_user_url';
		var objParam = {
			email : email,
			password : pswrd,
			name : $('#registrationSignUp #name').val(),
			contact_no : $('#registrationSignUp #contact_no').val();
		};

		$.ajax({
			type : "POST",
			url : addUserAPI,
			data : objParam,
			dataType : 'json',
			success : function(result) {
				console.log(result);
				if(result.status == 1000){
					//login success. redirect to home
				} else {
					//login failed. show error message
				}
			},
			error : function(err) {
				alert(err.message);
			}
		});
	}

	function addEvent(){
		var addEventAPI = 'add_event_url';
		var objParam = {
			event : $('#eventForm #eventName').val(),
			contact_person : $('#eventForm #poc').val(),
			status : ($('#eventForm #status').is(':checked') == true)?'1':'0'
		};

		$.ajax({
			type : "POST",
			url : addEventAPI,
			data : objParam,
			dataType : 'json',
			success : function(result) {
				console.log(result);
				if(result.status == 1000){
					//login success. redirect to home
				} else {
					//login failed. show error message
				}
			},
			error : function(err) {
				alert(err.message);
			}
		});
	}


	$(document).off('click').on('click','#loginButton', checkLogin);

})();
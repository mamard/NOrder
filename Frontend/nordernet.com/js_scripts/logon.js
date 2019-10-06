function submit_logon_request() {
                
	var user_login = document.getElementById("logon_login").value;
	var user_pwd = document.getElementById("logon_password").value;

	var fparams = [];
	fparams.push(user_login);
	fparams.push(user_pwd);
	$.ajax({
		url: "server/scripts/user_logon.php",
		type: "POST",
		dataType: "json",
		data: {action: "user_logon", params: fparams},
		success: function(response) { 
			var result = response.result;
			if (result != null){
				if (result){
					sessionStorage.setItem("user",user_login);
					window.location.href = "home.php";
				}
				else{
					alert("Login et/ou mot de passe incorrect !");
				} 
			}
		},
		error: function(response, statut, erreur) { alert(erreur); }
	});
}

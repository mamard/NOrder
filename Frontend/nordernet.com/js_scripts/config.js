function load_config_page_data(){
	var user_login = sessionStorage.getItem("user");
	if (user_login == null || user_login == ""){
		window.location.href = "logon.php";
	}
	else{
		user_login_element = document.getElementById("user_login");
		user_login_element.innerHTML = user_login;
	}
};

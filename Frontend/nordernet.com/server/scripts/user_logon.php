<?php
	$config = parse_ini_file('config/norder.ini');

	$action = "";
	$retval = "";
	if(isset($_POST['action']) && !empty($_POST['action'])) {
    	$action = $_POST['action'];
		$params = [];
		if(isset($_POST['params']) && !empty($_POST['params'])){
			//$params = json_decode($_POST['params'], true);	
			$params = $_POST['params'];
		}
    	switch($action) {
		    case 'user_logon' : $retval = user_logon($params[0], $params[1]);break;
		    case 'user_register' : user_register();break;
		    default: break;
		}
	
	}

	$response = array('result'=>$retval);
	echo json_encode($retval);

	function user_register($user_infos)
	{
		
	}

	function user_logon($user_login, $user_pwd)
	{
		$retval = [];
		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " user=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());
		$query = "SELECT count(*) FROM users where login = '".$user_login."' and password =  crypt('".$user_pwd."', password)";
		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$ucount = pg_fetch_result($q_result, 0, 0);

		if ($ucount != "0"){
			$retval = array('result'=> true);
		}
		else
		{
			$retval = array('result'=> false);
		}

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $retval;
		
		
		//echo json_encode($retval);
	}

	function get_user_first_name($user_login) {
		
		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " user=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());
		$query = "SELECT first_name FROM users where login = '".$user_login."'";
		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$fetch_res = pg_fetch_result($q_result, 0, 0);

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $fetch_res;
	}

?>

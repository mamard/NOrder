<?php
	$config = parse_ini_file('config/norder.ini');
	
	$action = "";
	$retval = [];
	if(isset($_POST['action']) && !empty($_POST['action'])) {
    	$action = $_POST['action'];
		$params = [];
		if(isset($_POST['params']) && !empty($_POST['params'])){
			//$params = json_decode($_POST['params'], true);	
			$params = $_POST['params'];
		}
    	switch($action) {
		    case 'user_erecipients_list' : 
		    	$retval = user_erecipients_list($params[0]);
		    	break;
		    default: break;
		}

		echo json_encode($retval);
		 
	}

	function user_erecipients_list($user_login) {
		$response = array('status' => 0,
						'erecipients' => array());
		$item_result = array( 
		    'recipient_id' => '', 
		    'configuration_state' => '',
		    'battery_level' => 0,
		    'product_name' => '',
		    'product_brand' => '',
		    'last_connection_date' => '',
		    'color_sticker' => '',	
		    'awakening_period' => '',
		    'pending_parameter_settings' => 0
		);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " user=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "select e.id as recipient_id, 
						ecs.code as configuration_state,  
						e.battery_level as battery_level, 
						case WHEN epb.product_id is null THEN 'NA'
						     ELSE p.name
						     END as product_name,
						case WHEN epb.product_id is null THEN 'NA'
						     ELSE p.brand
						     END as product_brand,
						to_char(e.last_connection_date, 'YYYY-MM-DD" . "\"T\"" . "HH24:MI:SS') as last_connection_date, 
						uep1.parameter_value as color_sticker, 
						uep2.parameter_value as awakening_period,
						count(epps.id) as pending_parameter_settings
						from users u
						inner join erecipients e on u.id = e.owner_id
						left join erecipient_product_binding epb on e.id = epb.erecipient_id
						left join products p on epb.product_id = p.id
						inner join erecipient_configuration_state ecs on e.configuration_state_id = ecs.id
						inner join user_erecipient_preferences uep1 on e.id = uep1.erecipient_id and u.id = uep1.user_id
						inner join erecipient_preference_parameter epp1 on uep1.parameter_id = epp1.id
						inner join user_erecipient_preferences uep2 on e.id = uep2.erecipient_id and u.id = uep2.user_id
						inner join erecipient_preference_parameter epp2 on uep2.parameter_id = epp2.id
						left outer join erecipient_pending_parameter_settings epps on e.id  = epps.erecipient_id
						where u.login = '" . $user_login . "' and epp1.name = 'sticker_color' and epp2.name = 'erecipient_awakening_period'
						group by (e.id, ecs.code, e.battery_level, e.last_connection_date, uep1.parameter_value, uep2.parameter_value, epps.id, p.name, p.brand, epb.product_id);";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$n_rows = pg_num_rows($q_result);
		$response['status'] = 1;

		for ($i = 0; $i < $n_rows; $i++) {
			
			$item_result['recipient_id'] = pg_fetch_result($q_result, $i, 'recipient_id');
			$item_result['configuration_state'] = pg_fetch_result($q_result, $i, 'configuration_state');
			$item_result['battery_level'] = pg_fetch_result($q_result, $i, 'battery_level');
			$item_result['product_name'] = pg_fetch_result($q_result, $i, 'product_name');
			$item_result['product_brand'] = pg_fetch_result($q_result, $i, 'product_brand');
			$item_result['last_connection_date'] = pg_fetch_result($q_result, $i, 'last_connection_date');
			$item_result['color_sticker'] = pg_fetch_result($q_result, $i, 'color_sticker');
			$item_result['awakening_period'] = pg_fetch_result($q_result, $i, 'awakening_period');
			$item_result['pending_parameter_settings'] =  pg_fetch_result($q_result, $i, 'pending_parameter_settings');

			//array_push($response['erecipients'], $item_result);
			$response['erecipients'][] =  $item_result;
		}


		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}

?>
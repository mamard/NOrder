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
		    case 'user_product_stores' : 
		    	$retval = user_product_stores($params[0]);
		    	break;
		    case 'update_low_quantity_threshold':
		    	$retval = update_low_quantity_threshold($params[0], $params[1], $params[2]);
		    	break;
		    case 'update_high_quantity_threshold':
		    	$retval = update_high_quantity_threshold($params[0], $params[1], $params[2]);
		    	break;
		    default: break;
		}

		echo json_encode($retval);
		 
	}
	function user_product_stores($user_login) {
		$response = array('status' => 0,
						'erecipient_stores' => array());
		$item_result = array( 
		    'recipient_id' => '', 
		    'product_id' => '', 
		    'product_name' => '',
		    'product_brand' => '',
		    'product_image_url' => '',
		    'store_quantity' => 0,
		    'low_quantity_threshold' => '',
		    'high_quantity_threshold' => ''
		);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " user=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "select e.id as recipient_id, 
					p.id as product_id,
					p.name as product_name,
					p.brand as product_brand,
					p.image_url as product_image_url,
					case WHEN pis.id is null THEN null
					ELSE pis.quantity
					END as store_quantity,
					uep1.parameter_value as low_quantity_threshold,
					uep2.parameter_value as high_quantity_threshold 
					from users u
					inner join erecipients e on u.id = e.owner_id
					left outer join erecipient_product_binding epb on e.id = epb.erecipient_id
					left outer join products p on epb.product_id = p.id
					left outer join product_item_store pis on epb.id = pis.erecipient_product_binding_id
					inner join user_erecipient_preferences uep1 on e.id = uep1.erecipient_id and u.id = uep1.user_id
					inner join erecipient_preference_parameter epp1 on uep1.parameter_id = epp1.id
					inner join user_erecipient_preferences uep2 on e.id = uep2.erecipient_id and u.id = uep2.user_id
					inner join erecipient_preference_parameter epp2 on uep2.parameter_id = epp2.id
					where u.login = '" . $user_login . "'
					and epp1.name = 'low_quantity_threshold'
					and epp2.name = 'high_quantity_threshold'";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$n_rows = pg_num_rows($q_result);
		$response['status'] = 1;

		for ($i = 0; $i < $n_rows; $i++) {
			
			$item_result['recipient_id'] = pg_fetch_result($q_result, $i, 'recipient_id');
			$item_result['product_id'] = pg_fetch_result($q_result, $i, 'product_id');
			$item_result['product_name'] = pg_fetch_result($q_result, $i, 'product_name');
			$item_result['product_brand'] = pg_fetch_result($q_result, $i, 'product_brand');
			$item_result['product_image_url'] = pg_fetch_result($q_result, $i, 'product_image_url');
			$item_result['store_quantity'] = pg_fetch_result($q_result, $i, 'store_quantity');
			$item_result['low_quantity_threshold'] = pg_fetch_result($q_result, $i, 'low_quantity_threshold');
			$item_result['high_quantity_threshold'] = pg_fetch_result($q_result, $i, 'high_quantity_threshold');
			
			//array_push($response['erecipients'], $item_result);
			$response['erecipient_stores'][] =  $item_result;
		}


		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}

	function update_low_quantity_threshold($user_login, $eRecipientId, $newValue)
	{
		$response = array('status' => 0);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " user=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "update user_erecipient_preferences as uep 
				set parameter_value = '" . $newValue . "' FROM  erecipient_preference_parameter epp, erecipients e, users u 
				where uep.parameter_id = epp.id 
				and uep.erecipient_id = e.id 
				and  uep.user_id = u.id
				and e.id = " . $eRecipientId . " and u.login = '" . $user_login . 
				"' and epp.name = 'low_quantity_threshold';";
				
		error_log("[update_low_quantity_threshold] update_low_quantity_threshold request : " . $query);

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$response['status'] = 1;

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}

	function update_high_quantity_threshold($user_login, $eRecipientId, $newValue)
	{
		$response = array('status' => 0);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " user=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "update user_erecipient_preferences as uep 
				set parameter_value = '" . $newValue . "' FROM  erecipient_preference_parameter epp, erecipients e, users u 
				where uep.parameter_id = epp.id 
				and uep.erecipient_id = e.id 
				and  uep.user_id = u.id
				and e.id = " . $eRecipientId . " and u.login = '" . $user_login . 
				"' and epp.name = 'high_quantity_threshold';";

		error_log("[update_high_quantity_threshold] update_high_quantity_threshold request : " . $query);

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$response['status'] = 1;

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}
?>
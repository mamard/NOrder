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
		    case 'all_user_shopping_list_items' : 
		    	$retval = all_user_shopping_list_items($params[0]);
		    	break;
		    case 'selected_user_shopping_list_items' :
		    	$retval = selected_user_shopping_list_items($params[0]);
		    	break;
		    case 'update_item_selected_state' :
		    	$retval = update_item_selected_state($params[0], $params[1]);
		    	break;
		    case 'update_item_comment' :
		    	$retval = update_item_comment($params[0], $params[1]);
		    	break;
		    case 'update_item_quantity' :
		    	$retval = update_item_quantity($params[0], $params[1]);
		    	break;
		    case 'update_item_purchased_state' :
		    	$retval = update_item_purchased_state($params[0], $params[1]);
		    	break;
		    default: break;
		}

		echo json_encode($retval);		 
	}

	function prepareUserProductsList($user_login){
		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " User=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "select pau.id as p_id from product_added_by_user pau
		inner join users u on  pau.user_id = u.id
		where u.login = '"  . $user_login . "';";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$n_rows = pg_num_rows($q_result);
		
		for ($i = 0; $i < $n_rows; $i++) {
			$id = pg_fetch_result($q_result, $i, 'p_id');

			$subquery = "select count(*) as has_list_item from user_shopping_items where  addition_reference_id = " .  $id . ";"; 

			$sq_result = pg_query($subquery) or die('Échec de la requête : ' . pg_last_error());

			$has_item = pg_fetch_result($sq_result, 0, 'has_list_item');

			if ($has_item == 0) {
				$insertQuery = "insert into user_shopping_items values(DEFAULT," . $id .  ");";
				$insertResult = pg_query($insertQuery);
				pg_free_result($insertResult);
			}

			pg_free_result($sq_result);
		}

		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);
	}

	function all_user_shopping_list_items($user_login){
		$response = array('status' => 0,
						'product_list' => array());
		$item_result = array(  
			'item_id' => 0,
		    'product_name' => '',
		    'product_brand' => '',
		    'product_image_url' => '',
		    'product_quantity' => 0,
		    'item_selected' => false,
		    'item_quantity' => 0,
		    'item_comment' => '',
		    'norderisable' => false   
		);

		prepareUserProductsList($user_login);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " User=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "select distinct usi.id as item_id,
					p.name as product_name,
					p.brand as product_brand,
					p.image_url as product_image_url,
					p.quantity as product_quantity,
					usi.to_be_purchased as item_selected,
					usi.quantity as item_quantity,
					case WHEN usi.comment is NULL THEN ''
						 ELSE usi.comment
						 END as item_comment,
					case WHEN count(epb.id) = 0 THEN false
					     ELSE true
					     END as norderisable
					from users u
					inner join product_added_by_user pau on u.id = pau.user_id 
					inner join user_shopping_items usi on pau.id = usi.addition_reference_id
					inner join products p on pau.product_id = p.id
					inner join erecipients e on u.id = e.owner_id 
					left join erecipient_product_binding epb on epb.erecipient_id =  e.id and epb.product_id = p.id
					where u.login = '"  . $user_login . "' 
					group by (usi.id, p.name, p.brand, p.image_url, p.quantity, usi.to_be_purchased, usi.quantity, usi.comment)
					order by norderisable DESC;";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$n_rows = pg_num_rows($q_result);
		$response['status'] = 1;

		for ($i = 0; $i < $n_rows; $i++) {
			$item_result['item_id'] = pg_fetch_result($q_result, $i, 'item_id');
			$item_result['product_name'] = pg_fetch_result($q_result, $i, 'product_name');
			$item_result['product_brand'] = pg_fetch_result($q_result, $i, 'product_brand');
			$item_result['product_image_url'] = pg_fetch_result($q_result, $i, 'product_image_url');
			$item_result['product_quantity'] = pg_fetch_result($q_result, $i, 'product_quantity');
			$item_result['item_selected'] = pg_fetch_result($q_result, $i, 'item_selected');
			$item_result['item_quantity'] = pg_fetch_result($q_result, $i, 'item_quantity');
			$item_result['item_comment'] = pg_fetch_result($q_result, $i, 'item_comment');
			$item_result['norderisable'] = pg_fetch_result($q_result, $i, 'norderisable');
			//array_push($response['erecipients'], $item_result);
			$response['product_list'][] =  $item_result;
		}

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}

	function selected_user_shopping_list_items($user_login){
		$response = array('status' => 0,
						'items_list' => array());
		$item_result = array(  
			'item_id' => 0,
		    'product_name' => '',
		    'product_brand' => '',
		    'product_quantity' => 0,
		    'item_quantity' => 0,
		    'item_comment' => '',
		    'item_purchased' => false
		);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " User=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "select distinct usi.id as item_id,
					p.name as product_name,
					p.brand as product_brand,
					p.quantity as product_quantity,					
					usi.quantity as item_quantity,
					case WHEN usi.comment is NULL THEN ''
						 ELSE usi.comment
						 END as item_comment,
					usi.has_been_purchased as item_purchased
					from users u
					inner join product_added_by_user pau on u.id = pau.user_id 
					inner join user_shopping_items usi on pau.id = usi.addition_reference_id
					inner join products p on pau.product_id = p.id 
					where usi.to_be_purchased = 't' and u.login = '"  . $user_login . "';";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$n_rows = pg_num_rows($q_result);
		$response['status'] = 1;

		for ($i = 0; $i < $n_rows; $i++) {
			$item_result['item_id'] = pg_fetch_result($q_result, $i, 'item_id');
			$item_result['product_name'] = pg_fetch_result($q_result, $i, 'product_name');
			$item_result['product_brand'] = pg_fetch_result($q_result, $i, 'product_brand');
			$item_result['product_quantity'] = pg_fetch_result($q_result, $i, 'product_quantity');
			$item_result['item_quantity'] = pg_fetch_result($q_result, $i, 'item_quantity');
			$item_result['item_comment'] = pg_fetch_result($q_result, $i, 'item_comment');
			$item_result['item_purchased'] = pg_fetch_result($q_result, $i, 'item_purchased');
			//array_push($response['erecipients'], $item_result);
			$response['items_list'][] =  $item_result;
		}

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;

	}


	function update_item_selected_state($shopping_item_id, $item_selected)
	{
		$response = array('status' => 0);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " User=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "update user_shopping_items set to_be_purchased = " . $item_selected . " where id = " . $shopping_item_id . ";";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$response['status'] = 1;

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}

	function update_item_comment($shopping_item_id, $item_comment)
	{
		$response = array('status' => 0);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " User=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "update user_shopping_items set comment = '" . $item_comment . "' where id = " . $shopping_item_id . ";";

		error_log("[update_item_comment] Update comment request : " . $query);

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$response['status'] = 1;

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}

	function update_item_quantity($shopping_item_id, $item_quantity)
	{
		$response = array('status' => 0);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " User=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "update user_shopping_items set quantity = " . $item_quantity . " where id = " . $shopping_item_id . ";";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$response['status'] = 1;

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}

	function update_item_purchased_state($shopping_item_id, $item_purchased)
	{
		$response = array('status' => 0);

		$dbconn = pg_connect("host=localhost dbname=" . $config['database_name']  . " User=" . $config['user_login']  . " password=" . $config['user_password'])
    or die('Connexion impossible : ' . pg_last_error());

		$query = "update user_shopping_items set has_been_purchased = " . $item_purchased . " where id = " . $shopping_item_id . ";";

		$q_result = pg_query($query) or die('Échec de la requête : ' . pg_last_error());

		$response['status'] = 1;

		// Free the result
		pg_free_result($q_result);

		// Close the connection
		pg_close($dbconn);

		return $response;
	}
	
?>
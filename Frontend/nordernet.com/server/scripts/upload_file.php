<?php

$response = array( 
    'status' => 0, 
    'message' => '',
    'image' => ''
);

$pictureFileName = $_POST['custom_name'];
$uploadfile = '/var/www/nordernet.com/server/uploads/' . $pictureFileName;// $_FILES['image_input']['name'];

if (move_uploaded_file($_FILES['image_input']['tmp_name'], $uploadfile)) {
    $response['status'] = 1;
    $response['image'] =  $pictureFileName;
} else {
    $response['status'] = 0;
	$response['message'] = 'Le transfert du fichier image a échoué !';
}

echo json_encode($response);



?>


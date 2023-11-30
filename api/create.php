<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once('../core/initialize.php');

$post = new Post($db);

$data = json_decode(file_get_contents("php://input"));

$post->phone = $data->phone;
$post->email = $data->email;

if ($post->create()) {
    echo json_encode(array('status' => 'success', 'message' => 'User registered.'));
} else {
    echo json_encode(array('status' => 'error', 'message' => 'User had not registered.'));
}
?>

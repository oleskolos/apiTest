<?php

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once('../core/initialize.php');

$post = new Post($db);

$post->id = isset($_GET['id']) ? $_GET['id'] : die();
$post->read_single();

$post_arr = array(
    'id'            => $post->id,
    'phone'         => $post->phone,
    'email'          => $post->email,  
);

print_r(json_encode($post_arr));
<?php 
require_once "urlhandler.php";

$req = new URLHandler();

$req->get('/api/', function(){
	echo 'Welcome to the API !';
});

$req->get('/api/devices/', function(){
	//do stuff here... (eg: list all devices)
});

$req->get('/api/devices/{id}', function($args){
	//do stuff here... (eg: return all info about {id})
});

$req->put('/api/devices/{id}', function($args){
	//do stuff here... (eg: modify device {id} )
});

try{
	$req->handle();
}catch(Exception $e){
	http_response_code($e->getCode());
	echo json_encode( array( 'status' => 'error', 'error' => array('code' => $e->getCode(), 'message' => $e->getMessage()) ) );
}

?>

<?php
// Routes

$app->get('/[{name}]', function ($request, $response, $args) {
    // Sample log message
    $this->logger->info("Slim-Skeleton '/' route");

    // Render index view
    return $this->renderer->render($response, 'index.phtml', $args);
});

// get order
$app->get('/getorder/orderId/[{orderId}]', function ($request, $response, $args) {	
    $sth = $this->db->prepare("SELECT A.*, B.`FirstName`, B.`LastName`, B.`Email`, B.`Phone` FROM orders A LEFT JOIN users B ON B.`userId` = A.`userId` WHERE A.`orderId` = ".$args['orderId']);
    $sth->execute();
    $query = $sth->fetchAll();
    
    if (count($query) == 0){
		//---no record---//
    	$query = array();
    	$query['result'] = "No orders with this orderId.";
    }else if ($query[0]['orderStatus']==2){
		//---cancelled record---//
    	$query = array();
    	$query['result'] = "This order is already cancelled.";
    }
    return $this->response->withJson($query);
});

// cancel order
$app->get('/cancelorder/orderId/[{orderId}]', function ($request, $response, $args) {	
   	$input = $request->getParsedBody();
    $sql = "UPDATE orders SET orderStatus=2 WHERE orderId=:orderId";
    $sth = $this->db->prepare($sql);
    $sth->bindParam("orderId", $args['orderId']);
    $sth->execute();
    
    //---cancell order---//
	$query = array();
	$query['result'] = "OrderId".$args['orderId']." has just been cancelled.";

    return $this->response->withJson($query);
});

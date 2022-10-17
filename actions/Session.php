<?php

$ZD = new RuckusZD($GLOBALS['ZD']['Config']);

if(HTTPHelper::isPost()){
    $Request = HTTPHelper::getJsonAsArray();
    $Session = $ZD->Session($Request['username'], $Request['password']);
    HTTPHelper::headerJson();
    if(!empty($Session)){
        setcookie('JSESSIONID', $Session);
        echo json_encode(['controllerVersion'=>'EMU']);
        HTTPHelper::closeConnection();
    }

    HTTPHelper::responseCode(401);
    echo json_encode([
        'message'=>'Login denied',
        'errorCode'=>202,
        'errorType'=>'Login denied'
    ]);
    HTTPHelper::closeConnection();
}
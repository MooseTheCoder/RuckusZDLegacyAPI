<?php

$ZD = new RuckusZD($GLOBALS['ZD']['Config']);

if(HTTPHelper::isGet()){
    $Wlans = $ZD->GetWlans($ZONEID);
    HTTPHelper::headerJson();
    echo json_encode($Wlans);
    HTTPHelper::closeConnection();
}

if(HTTPHelper::isPost()){
    /* 
        Strange firmware issue here.
        Add things manually in the API for now.
    */
    // $Request = HTTPHelper::getJsonAsArray();
    // $ZD->CreateWlan($Request);
    echo json_encode(['success'=>true]);
    HTTPHelper::closeConnection();
}
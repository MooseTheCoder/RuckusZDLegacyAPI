<?php

require_once('config.php');

require_once('classes/Logger.php');
require_once('classes/HTTPHelper.php');
require_once('classes/RuckusZD.php');

require_once('router.php');

if($GLOBALS['debug']){
    $RequestUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    Logger::Log($RequestUrl);
    Logger::Log(json_encode(HTTPHelper::method()));
    Logger::Log(json_encode(HTTPHelper::getJsonAsArray()));
}

post('/$version/session', 'actions/Session.php'); // User Login
get('/$version/rkszones','actions/Zones.php'); // Zones
get('/$version/rkszones/$ZONEID/wlangroups','actions/WLANGroups.php'); // WLAN Groups
get('/$version/rkszones/$ZONEID/wlans','actions/WLANs.php'); // WLANs
post('/$version/rkszones/$ZONEID/wlans','actions/WLANs.php'); // WLAN Add
get('/$version/rkszones/$ZONEID/wlans/$WLANID','actions/WLAN.php'); // WLAN ID
post('/$version/rkszones/$ZONEID/wlangroups/$WLANGROUP/members','actions/WLANGroupMember.php'); // WLAN Member Add
delete('/$version/rkszones/$ZONEID/wlangroups/$WLANGROUP/members/$WLANID','actions/WLANGroupMember.php'); // WLAN Member Add
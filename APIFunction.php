<?php

Function APIGet($QueryString){
// Source : https://support.liveagent.com/061754-How-to-make-REST-calls-in-PHP
$myBase =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/API.php?";
$service_url =  $myBase.$QueryString;
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
}
curl_close($curl);
$decoded = json_decode($curl_response, true);
return $decoded;
// var_export($decoded);
}


Function APIPost($curl_post_data){
$service_url =  ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/API.php?";
$curl = curl_init($service_url);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
$curl_response = curl_exec($curl);
if ($curl_response === false) {
    $info = curl_getinfo($curl);
    curl_close($curl);
    die('error occured during curl exec. Additioanl info: ' . var_export($info));
}
curl_close($curl);
$decoded = json_decode($curl_response, true);
return $decoded;
// var_export($decoded);		
}

?>
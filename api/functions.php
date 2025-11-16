<?php

// bdbulksms.net SMS API integration
function sendSMS($to, $message) {
    
    $token = "109451845141733661914aa3b8fbd868b0da6e2a5c16939ee6f9a";

    $url = "https://api.bdbulksms.net/api.php?json";
    $data= array(
    'to'=>"$to",
    'message'=>"$message",
    'token'=>"$token"
    ); 
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_ENCODING, '');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $smsresult = curl_exec($ch);
    
    //Result
    //echo $smsresult;
    
    //Error Display
    //echo curl_error($ch);
}

?>
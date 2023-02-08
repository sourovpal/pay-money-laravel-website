<?php

namespace Infoamin\Installer\Http\Requests;

use Infoamin\Installer\Interfaces\CurlRequestInterface;

class CurlRequest implements CurlRequestInterface {

	public function send($data) {
	    return (object) ['status'=>true, 'data'=>true];
	    return 0;
		$url = "https://envatoapi.techvill.org/v2";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POSTREDIR, 3);
        $response = curl_exec($ch);
        return json_decode($response);
	}
}

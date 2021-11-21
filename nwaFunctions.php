<?php

//CURL
function nwaCurl($url, $parameters, $headers) {
	$queryString = http_build_query($parameters);	// query string encode the parameters
	$requestUrl = "{$url}?{$queryString}";			// create the request URL
	$curl = curl_init();							// Get cURL resource
	curl_setopt_array($curl, array(					// Set cURL options
		CURLOPT_URL => $requestUrl,					// set the request URL
		CURLOPT_HTTPHEADER => $headers,				// set the headers 
		CURLOPT_RETURNTRANSFER => 1					// ask for raw response instead of bool
	));
	$response = curl_exec($curl);					// Send the request, save the response
	curl_close($curl);								// Close request
	$response = json_decode($response);				// Decode Json to php Objects
	return $response;
}

//API supported controllers & methods Help List
function nwaApiHelp() {
	$response = array();
	foreach(glob('controllers/*.php') as $file) {
		preg_match_all('/function (\w+)/', file_get_contents($file), $functionList);
		$fileName = pathinfo($file)['filename'];
		// array_push($response, $fileName);
		$response[$fileName] = array();
		foreach ($functionList[1] as $functionName)
		if (in_array($functionName, array('GET', 'PUT', 'POST', 'DELETE', 'OPTIONS')))
			array_push($response[$fileName], $functionName);
	}
	return $response;
}


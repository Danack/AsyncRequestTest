<?php

require_once "../vendor/autoload.php";

$startTime = microtime(true);

// Generate a request URI for each letter a-z
$requests = array_map(function($alpha) { return 'http://www.bing.com/search?q=' . $alpha; }, range('a', 'z'));

// What to do when an individual response in the batch completes
$onResponse = function($requestKey, Artax\Response $response) {
    //echo 'Response: (', $requestKey, ') ', $response->getStatus(), "\n";
    //echo $request->getUri(), ' -- ';
    echo $requestKey;
    echo 'HTTP/', $response->getProtocol(), ' ', $response->getStatus(), ' ', $response->getReason(), "\n";
};

// What to do if an individual request in the batch fails
$onError = function($requestKey, Exception $error) {
    echo 'Error: (', $requestKey, ') ', get_class($error), "\n";
};


$client = new Artax\Client;
$client->requestMulti($requests, $onResponse, $onError);

$client->setOption('maxConnectionsPerHost', 10);

$timeTaken = microtime(true) - $startTime;
echo "Time taken = ".round(($timeTaken), 4)."\n";
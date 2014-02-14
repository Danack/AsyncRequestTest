<?php

require_once "../vendor/autoload.php";

$startTime = microtime(true);

$reactor = (new Alert\ReactorFactory)->select();
$client = new Artax\AsyncClient($reactor);

// Generate a request URI for each letter a-z
$requests = array_map(function($alpha) { return 'http://www.bing.com/search?q=' . $alpha; }, range('a', 'z'));

// We need to track how many requests remain so we can stop the program when they're all finished
$unfinishedRequests = count($requests);

// What to do when an individual request completes
$onResponse = function(Artax\Response $response, Artax\Request $request) use (&$unfinishedRequests, $reactor) {
    echo $request->getUri(), ' -- ';
    echo 'HTTP/', $response->getProtocol(), ' ', $response->getStatus(), ' ', $response->getReason(), "\n";
    if (!--$unfinishedRequests) {
        $reactor->stop();
    }
};

// What to do if a request encounters an exceptional error
$onError = function(Exception $e, Artax\Request $request) use (&$unfinishedRequests, $reactor) {
    echo $request->getUri(), " failed (", get_class($e), ") :(\n";
    if (!--$unfinishedRequests) {
        $reactor->stop();
    }
};

// Schedule this to happen as soon as the reactor starts
$reactor->immediately(function() use ($client, $requests, $onResponse, $onError) {
    echo 'Requesting ', count($requests), ' URIs ...', "\n";
    foreach ($requests as $uri) {
        $client->request($uri, $onResponse, $onError);
    }
});

// The reactor IS our task scheduler and the program runs inside it. Nothing will happen until the
// event reactor is started, so release the hounds!
$reactor->run();

$timeTaken = microtime(true) - $startTime;
echo "Time taken = ".round(($timeTaken), 4)."\n";
<?php

use Guzzle\Http\Exception\MultiTransferException;

require_once "../vendor/autoload.php";

echo "starting.\n";
$startTime = microtime(true);

$client = new Guzzle\Http\Client();


try {

    $makeRequest = function($alpha) use ($client) {
        $url = 'http://www.bing.com/search?q=' . $alpha;
        return $client->get($url);
    };
    
    $requests = array_map($makeRequest, range('a', 'z'));
    $responses = $client->send($requests);

    $timeTaken = microtime(true) - $startTime;
    echo "Time taken = ".round(($timeTaken), 4)."\n";
} catch (MultiTransferException $e) {

    echo "The following exceptions were encountered:\n";
    foreach ($e as $exception) {
        /** @var $exception \Exception */
        echo $exception->getMessage() . "\n";
    }

    echo "The following requests failed:\n";
    foreach ($e->getFailedRequests() as $request) {
        echo $request . "\n\n";
    }

    echo "The following requests succeeded:\n";
    foreach ($e->getSuccessfulRequests() as $request) {
        echo $request . "\n\n";
    }
}

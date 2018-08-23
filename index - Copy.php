<?php
// Load the Google API PHP Client Library.
require_once __DIR__ . '/vendor/autoload.php';

// Start a session to persist credentials.
session_start();

// Create the client object and set the authorization configuration
// from the client_secretes.json you downloaded from the developer console.
$client = new Google_Client();
$client->setAuthConfig(__DIR__ . '/client_secrets.json');
$client->setAccessType('online'); // default: offline
$client->setIncludeGrantedScopes(true);   // incremental auth
$client->setApplicationName('Content API for Shopping Samples');
$client->setScopes(Google_Service_ShoppingContent::CONTENT);

$client->setClientId('43212426181-1g65j41m17r5418ugr707qun3u66gtfv.apps.googleusercontent.com');
$client->setClientSecret('hnJLUmEuvSUt-GCHne6G1XU4');
$client->setDeveloperKey('AIzaSyBWseTr0NQoxthxnC-4wCiKcHF-FhEYfbE'); // API key
$client->setIncludeGrantedScopes(true);   // incremental auth

// If the user has already authorized this app then get an access token
// else redirect to ask the user to authorize access to Google Analytics.
$merchantId = '123316573';
if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
  // Set the access token on the client.
  $client->setAccessToken($_SESSION['access_token']);
  $service = new Google_Service_ShoppingContent($client);
  $products = $service->products->listProducts($merchantId);
   
  $parameters = array();

  while (!empty($products->getResources())) {
     foreach ($products->getResources() as $product) {
       printf("%s %s\n", $product->getId(), $product->getTitle());
       echo "<br/>";
     }
     if (!empty($products->getNextPageToken())) {
       break;
     }
     $parameters['pageToken'] = $products->nextPageToken;
     $products = $service->products->listProducts($merchantId, $parameters);
  }
  echo "<pre>"; print_r($products); die;
} else {
  $redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . '/gcss/oauth2callback.php';
  header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}

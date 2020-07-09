<?php

use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\TokenInterface;
use ActiveCollab\SDK\Exceptions\Authentication;
use ActiveCollab\SDK\Token;
use ActiveCollabToJiraMigrator\Process\MigrationManager;

/**
 * @file
 */

require_once 'bootstrap.php';

$_token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
$_attachmentId = filter_input(INPUT_GET, 'attachmentId', FILTER_SANITIZE_NUMBER_INT);

try {
  $token = new Token($_token, $acUrl);
  if ($token instanceof TokenInterface) {
    // Login successful!
    $acUrl = $token->getUrl();
  }
  else {
    throw new \Exception('Invalid response from ActiveCollab API Authentication. Are your credentials and configuration correct?');
  }
  // Create a client instance:
  $client = new Client($token);
  $info = $client->get('info')->getJson();
  if ($info['application'] != 'ActiveCollab') {
    throw new \Exception('Retrieving ActiveCollab info failed.');
  }
}
catch (Authentication $e) {
  throw new \Exception('Invalid response from ActiveCollab API Authentication. Are your credentials and configuration correct?');
}

// TODO - Instead of Login retrieve the token here!
$manager = MigrationManager::createInstance($client, $settings);
return $manager->proxyAttachmentAccess($_attachmentId);

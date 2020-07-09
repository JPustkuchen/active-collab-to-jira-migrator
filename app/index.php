<?php

/**
 * @file
 */

use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\TokenInterface;
use ActiveCollab\SDK\Authenticator\SelfHosted;
use ActiveCollab\SDK\Exceptions\Authentication;
use ActiveCollabToJiraMigrator\Export\JiraJsonExporter;
use ActiveCollabToJiraMigrator\Export\JiraJsonDownloadExporter;
use ActiveCollabToJiraMigrator\Process\MigrationManager;

require_once 'bootstrap.php';

// Login form was submitted:
$_username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_EMAIL);
$_password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING);
if (empty($_username) || empty($_password)) {
  // Show login and exit.
  $form = file_get_contents('form.html');
  // Set the given secret in the form by string replace:
  $form = str_replace('{{ secret }}', $_secret, $form);
  // Show the form and exit here.
  echo $form;
  exit(0);
}
// LOGIN FORM WAS SUBMITTED!

if (empty($_username)) {
  throw new \Exception('Missing username (from user input)');
}
if (empty($_password)) {
  throw new \Exception('Missing password (from user input)');
}

try {
  // Construct a self-hosted authenticator. Last parameter is URL
  // where your Active Collab.
  $authenticator = new SelfHosted('ActiveCollab to Jira Migration Export',
      'ActiveCollab to Jira Migration Export',
      $_username,
      $_password,
      $acUrl);

  // Issue a token.
  $token = $authenticator->issueToken();
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

if (!empty($settings['debug'])) {
  error_reporting(E_ALL);
  ini_set("display_errors", 1);

  debug('!! Debugging is enabled !!');
  debug('Settings: ' . print_r($settings, 1));
}

$_projectLimit = filter_input(INPUT_POST, 'projectLimit', FILTER_SANITIZE_NUMBER_INT);
$settings['project_limit'] = $_projectLimit !== '' ? $_projectLimit : $settings['project_limit'];

$_projectOffset = filter_input(INPUT_POST, 'projectOffset', FILTER_SANITIZE_NUMBER_INT);
$settings['project_offset'] = $_projectOffset !== '' ? $_projectOffset : $settings['project_offset'];

$_userLimit = filter_input(INPUT_POST, 'userLimit', FILTER_SANITIZE_NUMBER_INT);
$settings['user_limit'] = $_userLimit !== '' ? $_userLimit : $settings['user_limit'];

$_userOffset = filter_input(INPUT_POST, 'userOffset', FILTER_SANITIZE_NUMBER_INT);
$settings['user_offset'] = $_userOffset !== '' ? $_userOffset : $settings['user_offset'];

debug('Login successful at: "' . $acUrl . '" with user "' . $_username . '"');
$start_time = microtime(TRUE);

$manager = MigrationManager::createInstance($client, $settings);
if (!empty($settings['debug']) && !empty($settings['debug_show_project_list'])) {
  debug('=============== PROJECT LIST START ===================');
  debug(print_r($manager->getProjectsList(), 1));
  debug('=============== PROJECT LIST END ===================');
}

// Additional info in file name about limit / offset:
$filenameAppendix = '';
// Process users:
if (!empty($settings['import_users'])) {
  debug('Starting to process ' . $settings['user_limit'] . ' User(s), starting from offset: ' . $settings['user_offset']);
  $manager->processUsers($settings['user_limit'], $settings['user_offset']);
  $filenameAppendix .= 'users_' . 'l' . $settings['user_limit'] . '-o' . $settings['user_offset'];
  debug('Finished processing ' . $settings['user_limit'] . ' User(s), starting from offset: ' . $settings['user_offset']);
}
// Process projects:
debug('Starting to process ' . $settings['project_limit'] . ' Project(s), starting from offset: ' . $settings['project_offset']);
$manager->processProjects($settings['project_limit'], $settings['project_offset']);
$filenameAppendix .= 'projects_' . 'l' . $settings['project_limit'] . '-o' . $settings['project_offset'];
debug('Finished processing ' . $settings['project_limit'] . ' Project(s), starting from offset: ' . $settings['project_offset']);

// Send file download:
if (!$settings['debug']) {
  $manager->export(JiraJsonDownloadExporter::class, ['filenameAppendix' => $filenameAppendix]);
}
else {
  debug('=============== START EXPORT ===================');
  debug($manager->export(JiraJsonExporter::class,
  [
    'prettyPrint' => $settings['export_json_exporter_pretty_print'],
    'validateJson' => $settings['export_json_exporter_validate_json_schema'],
    'validateJsonSchemaPath' => $settings['export_json_exporter_validate_json_schema_path'],
  ]));
  debug('=============== END EXPORT ===================');
  $end_time = microtime(TRUE);
  debug("Duration: " . bcsub($end_time, $start_time, 4) . ' Seconds');

  $mempeak = memory_get_peak_usage(TRUE);
  for ($i = 0; ($mempeak / 1024) > 0.9; $i++, $mempeak /= 1024) {}
  $mempeakHr = round($mempeak, 2) . ['B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'][$i];
  debug("Max memory consumption: " . $mempeakHr);
}

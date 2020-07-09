<?php

/**
 * @file
 * ActiveCollab to Jira JSON Import.
 */

require_once '../vendor/autoload.php';

$_secret = filter_var($_REQUEST['secret'], FILTER_SANITIZE_STRING);
if (empty($_secret)) {
  // No secret given. Exit!
  throw new \Exception('Missing secret parameter.');
}

// Load config values from .config.php.
$acUrl = NULL;
$secret = NULL;
$settings = [];
require_once '../config/config.php';

if (empty($acUrl)) {
  throw new \Exception('Missing ActiveCollab URL (from config.php)');
}
if (empty($settings)) {
  throw new \Exception('Missing global settings (in config.php)');
}
if (empty($secret)) {
  throw new \Exception('Missing secret from settings (in config.php)');
}
if ($_secret != $secret) {
  throw new \Exception('Given secret is incorrect! Aborting!');
}

if ($settings['debug']) {
  /**
   * Handle a debug message.
   *
   * @param string $message
   */
  function debug($message) {
    // TODO - Improve later?
    echo '<pre>##';
    print_r($message);
    echo '##[debug]</pre>';
  }
}
else {
  /**
   * No debugging.
   *
   * @param string $message
   */
  function debug($message) {
    // Don't output anything!
    // Log to errorlog instead:
    // TODO - Move into setting
    error_log('ActiveCollabToJiraMigrator Debug: ' . $message);
  }

}

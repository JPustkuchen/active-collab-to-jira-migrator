<?php

/**
 * @file
 * Individual configuration settings.
 */

/**
 * The ActiveCollab instance base URL with trailing slash.
 *
 * Example: 'https://ac.mycompany.com/', 'https://www.mycompany.com/ac/'
 *
 * @var $acUrl String
 */
$acUrl = 'https://ac.mycompany.com/';

/**
 * The secret key to append to the URL to respond to any request.
 *
 * Example: urlencode('fjkwejF4v5k2G3j4tjwekrfgwe!1f§$gwFadsfawe51gasf');
 * @var $secret string
 */
// TODO - SET THIS VALUE WITH A COMPLEX SECRET STRING!
// $secret = urlencode('');

// PHP Settings:
// Set a high memory limit because we keep many values in memory:
// Default: ini_set('memory_limit', '2GB');
ini_set('memory_limit', '4GB');

// The script will run very long.
// Default: ini_set('max_execution_time', 900); // 15min
ini_set('max_execution_time', 900); // 15min
// The script will run very long.
// Default: set_time_limit(900); // 15min
set_time_limit(900); // 15min

/**
 * General settings defaults. Some may be overridden by user input.
 *
 * @var $settings array
 */
$settings = [
  /*
   * Enable debugging globally.
   *
   * This will output E_ALL and further debug information.
   * With debug enabled, no file download is possible, instead the results will
   * be outout as text.
   */
  // Default: 'debug' => FALSE,.
  'debug' => FALSE,

  // Show a list of all projects if debugging is enabled:
  // 'debug_show_project_list' => FALSE,
  'debug_show_project_list' => FALSE,

  // The Jira username which will be used as fallback owner / creator
  // of elements whose original creator doesn't exist in jira
  // or can not be determined. Must exist in Jira at import!
  // Default: 'username_fallback' => 'admin',.
  'username_fallback' => 'myjiraadminuserasfallback',

  /*
   * Whitelist AC usernames in fnmatch() shell wildcard pattern.
   * Blacklisted user names will not be imported and assigned. Instead the
   * username_fallback is used.
   * Default: 'username_whitelist' => [].
   */
  'username_whitelist' => [
    'mike@example.com',
    'jim@customer.com',
    'tom@example.com',
    '*@bestcustomer.com',
  ],

  /*
   * Blacklist AC usernames for import and assigns in fnmatch() shell wildcard pattern.
   * Blacklisted user names will not be imported and assigned. Instead the
   * username_fallback is used.
   * Blacklisting runs after whitelisting. All whitelisted users are no more processed by blacklist.
   * To ONLY allow whitelisted users, use ['*'] as blacklist.
   * Default: 'username_blacklist' => ['*']. // Only import whitelisted!
   */
  'username_blacklist' => ['*'],

  // Import AC user accounts explicitely. Password will never be migrated.
  // Password reset required for each newly imported user.
  // If you create users in Jira manually before the import with their
  // AC eMail Address as usernames, you can leave this FALSE to only
  // import assets and assign them to the existing users.
  // Otherwise Jira will automatically create User accounts for them!
  // Default: 'import_users' => FALSE,. Only use existing accounts.
  'import_users' => FALSE,

  // LATER: Not yet implemented because Jira ServiceDesk doesn't support import.
  // HAS NO EFFECT!
  // Default: 'import_companies' => FALSE,.
  'import_companies' => FALSE,

  // Default project offset per run (overridden in form)
  // Default: 'project_offset' => 0,.
  'project_offset' => 0,

  // Default project limit per run (overridden in form)
  // Default: 'project_limit' => 99999,.
  'project_limit' => 25,

  // Default user offset per run (overridden in form)
  // Default: 'user_offset' => 0,.
  'user_offset' => 0,

  // Default user limit per run (overridden in form)
  // Default: 'user_limit' => 99999,.
  'user_limit' => 25,

  // JSON Exporter (with debug enabled):
  // Enable pretty printing for json direct output
  // Default: 'export_json_exporter_pretty_print' => TRUE,.
  'export_json_exporter_pretty_print' => TRUE,

  // JSON Exporter (with debug enabled):
  // Validate json against schema file
  // Default: 'export_validate_schema' => TRUE,.
  'export_json_exporter_validate_json_schema' => TRUE,

  // JSON Exporter (with debug enabled):
  // The validation schema path.
  // Default: 'export_json_exporter_validate_json_schema_path' => '../schema/jira-import.schema.json',.
  'export_json_exporter_validate_json_schema_path' => '../schema/jira-import.schema.json',

  // Set default jira usergroups like ['jira-users']
  // which will be assigned to all exported users.
  // Default: 'default_user_groups' => [],.
  'default_user_groups' => [],

  // Default project versions which will be assigned to all exported projects:
  // Default: 'default_project_versions' => ['1.0-acimport'],.
  'default_project_versions' => ['1.0-acimport'],

  // The prefix for project Keys. Must be uppercase and start with a letter A-Z0-9:
  // Will be set in front of all exported project keys followed by the ID.
  // Default: 'project_import_key_prefix' => 'AC'
  'project_import_key_prefix' => 'AC',

  // The attachment proxy URL including secret.
  // Is used as to allow Jira access to AC
  // !! IMPORTANT !! Keep this secret and use https! With knowledge about this
  // !! path and the secret, anyone will be able to fetch ALL AC files without login!
  // Default: 'attachment_proxy_url' => 'https://thisactivecollabtojiramigrator.example.com/attachmentProxy.php?secret=' . $secret,.
  'attachment_proxy_url' => 'https://thisactivecollabtojiramigrator.example.com/attachmentProxy.php?secret=' . $secret,

  // The Jira technical name of type for tasks. Typically "Task".
  // The issue type when exporting tasks from AC. Examples: "Bug", "Task", ...
  // Default: 'issue_default_type' => 'Task',
  'issue_default_type' => 'Task',
];

<?php

namespace ActiveCollabToJiraMigrator\Process;

/**
 *
 */
class UserMapper {
  /**
   * MigrationManager instance.
   *
   * @var \ActiveCollabToJiraMigrator\Process\MigrationManager
   */
  protected $migrationManager;

  /**
   * The ActiveCollab user array indexed by user id.
   *
   * @var array
   */
  protected $acUserRecords = [];

  /**
   * The settings array from config.php and in parts modified by user input.
   *
   * @var array
   */
  protected $settings = [];

  /**
   * Creates a new UserMapper Instance.
   *
   * @param \ActiveCollabToJiraMigrator\Fetch\AcApiFetcher $acApiFetcher
   * @param array $settings
   *
   * @return UserMapper
   */
  public static function createInstance(MigrationManager $migrationManager, array $settings) {
    return new self($migrationManager, $settings);
  }

  /**
   * Constructor.
   *
   * @param MigrationManager $migrationManager
   * @param array $settings
   */
  protected function __construct(MigrationManager $migrationManager, array $settings) {
    $this->settings = $settings;
    $this->migrationManager = $migrationManager;
    $acApiFetcher = $migrationManager->getAcApiFetcher();

    // Load all users indexed by array into our variable for fast fetch.
    $fetchedUsers = $acApiFetcher->fetchUsers();

    if (!empty($fetchedUsers)) {
      foreach ($fetchedUsers as $fetchedUserRecord) {
        if (is_array($fetchedUserRecord)) {
          $acUserId = $fetchedUserRecord['id'];
          if (!is_int($acUserId)) {
            throw new \Exception('Expected user ID integer, but was "' . $acUserId . '" ' . gettype($acUserId));
          }
          $this->acUserRecords[$acUserId] = $fetchedUserRecord;
        }
        else {
          throw new \Exception('Expected user record array but was "' . gettype($fetchedUserRecord) . '"');
        }
      }
    }
    else {
      if ($settings['debug']) {
        user_error('No user records found to fetch.');
      }
    }
  }

  /**
   * Maps the given $acUserId to the Jira username. Returns the fallback username, if the username could not be detected.
   *
   * If null given, fallback user is returned.
   *
   * @see self::getFallbackUsername()
   * @param int $acUserId
   *
   * @return string
   *   The mapped Jira username.
   */
  public function mapAcUserIdToJiraUsername(int $acUserId = NULL) {
    if (empty($acUserId)) {
      debug('Using fallback user "' . $this->getUsernameFallback() . '" because given user id was empty (typically because the user had been deleted).');
      // For deleted users the user ID is NULL in ActiveCollab database.
      return $this->getUsernameFallback();
    }

    if (isset($this->acUserRecords[$acUserId])) {
      $userRecord = $this->acUserRecords[$acUserId];
      // Email is username.
      $acUsername = $userRecord['email'];
      if (empty($acUsername)) {
        throw new \Exception('AC Username could not be determined, while there was a user array: "' . var_export($userRecord, 1) . '". You should check for the reasons.');
      }
      if ($this->allowsAcUsername($acUsername)) {
        // TODO - Implement mapping logic here and provide all values from $userRecord.
        return $acUsername;
      }
      else {
        debug('Using fallback user "' . $this->getUsernameFallback() . '" because ActiveCollab user "' . $acUsername . '" is not allowed due to white / blacklist.');
        return $this->getUsernameFallback();
      }
    }
    else {
      if ($this->settings['debug']) {
        user_error('User ID "' . $acUserId . '" could not be mapped to AC account data. Not found in AC records, while referenced in records.');
      }
      return FALSE;
    }

  }

  /**
   * Helper function to return the first(!) Ac email matching the given display name.
   * This is (sadly) required for mentions, because they do not use the username or ID,
   * but full name.
   * Danger! If two people have the same DisplayName, this function will always return the first!
   *
   * @param string $acDisplayName The ActiveCollab display_name (Firstname Lastname).
   *
   * @return int|bool
   *   The first matching ActiveCollab User ID or false if not found.
   */
  public function mapAcDisplayNameToAcEmail(string $acDisplayName) {
    if (!empty($this->acUserRecords)) {
      foreach ($this->acUserRecords as $acUserRecord) {
        if ($acUserRecord['display_name'] == $acDisplayName) {
          return $acUserRecord['email'];
        }
      }
    }
    return FALSE;
  }

  /**
   * Returns the fallback_username (=email) from settings.
   */
  public function getUsernameFallback() {
    return $this->settings['username_fallback'];
  }

  /**
   * Returns true if the given AC Username (=email) is allowed for assigns and import.
   * Therefore combines isAcUsernameWhitelisted() or !isAcUsernameBlacklisted().
   *
   * @param [type] $acUsername
   */
  public function allowsAcUsername(string $acUsername) {
    return $this->isAcUsernameWhitelisted($acUsername) || !$this->isAcUsernameBlacklisted($acUsername);
  }

  /**
   * Returns true if the given AC Username (=email) is whitelisted.
   *
   * @param string $acUsername
   *
   * @return bool
   */
  public function isAcUsernameWhitelisted(string $acUsername) {
    $usernameWhitelist = $this->settings['username_whitelist'];
    if (!is_array($usernameWhitelist)) {
      throw new \Exception('Setting "username_whitelist" must be an array!');
    }
    if (empty($usernameWhitelist)) {
      return FALSE;
    }
    if (array_search('*', $usernameWhitelist)) {
      // All whitelisted.
      return TRUE;
    }
    foreach ($usernameWhitelist as $fnpattern) {
      if (fnmatch((string) $fnpattern, $acUsername)) {
        // Username found in whitelist.
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * Returns true if the given AC Username (=email) is blacklisted.
   *
   * @param string $acUsername
   *
   * @return bool
   */
  public function isAcUsernameBlacklisted(string $acUsername) {
    $usernameBlacklist = $this->settings['username_blacklist'];
    if (!is_array($usernameBlacklist)) {
      throw new \Exception('Setting "username_blacklist" must be an array!');
    }
    if (empty($usernameBlacklist)) {
      return FALSE;
    }
    if (array_search('*', $usernameBlacklist)) {
      // All whitelisted.
      return TRUE;
    }
    foreach ($usernameBlacklist as $fnpattern) {
      if (fnmatch((string) $fnpattern, $acUsername)) {
        // Username found in whitelist.
        return TRUE;
      }
    }
    return FALSE;
  }

}

<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;

/**
 * Interface for Jira import entities.
 */
interface JiraImportEntityInterface extends SerializableInterface {

  /**
   * Creates an instance from the ActiveCollab API array.
   *
   * @param array $array
   * @param \ActiveCollabToJiraMigrator\Process\MigrationManager $migrationManager
   *   The parent migration manager.
   *
   * @return JiraImportEntityInterface
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager);

  /**
   * Returns the array representation.
   *
   * @return array
   *   The array representation.
   */
  public function toArray();

  /**
   * Returns the JSON representation.
   *
   * @param int $options Bitwise!
   *   See https://www.php.net/manual/de/function.json-encode.php.
   *
   * @return string The JSON representation.
   */
  public function toJson(int $options = 0);

}

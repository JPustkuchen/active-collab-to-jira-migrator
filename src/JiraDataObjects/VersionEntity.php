<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;

/**
 * Data object for versions.
 */
class VersionEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The version name.
   *
   * @required
   * @var string
   */
  protected $name;

  /**
   * Indicator if the version has been released.
   *
   * @var bool
   */
  protected $released;

  /**
   * When released?
   *
   * @var string
   */
  protected $releaseDate;

  /**
   * Optional description.
   *
   * @var string
   */
  protected $description;

  /**
   * {@inheritDoc}
   *
   * @return VersionEntity
   *
   * @throws Exception
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    throw new \Exception('createInstanceFromAcApiArray not supported. AC does not know this type. createInstance for indirect use.');
  }

  /**
   * Returns a new VersionEntity.
   *
   * @param string $name
   * @param bool $released
   * @param string $releaseDate
   * @param string $description
   */
  public static function createInstance(string $name, bool $released, string $releaseDate, string $description = '') {
    return new self($name, $released, $releaseDate, $description);
  }

  /**
   * Constructor.
   *
   * @param string $name
   * @param bool $released
   * @param string $releaseDate
   * @param string $description
   */
  protected function __construct(string $name, bool $released, string $releaseDate, string $description = '') {
    $this->name = $name;
    $this->released = $released;
    $this->releaseDate = $releaseDate;
    $this->description;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    {
    "name": "1.0",
    "released": true,
    "releaseDate": "2012-08-31T15:59:02.161+0100"
    },
     */
    return $array;
  }

}

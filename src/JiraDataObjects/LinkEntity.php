<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;

/**
 * Data object for links.
 */
class LinkEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The link name.
   *
   * @var string
   */
  protected $name;

  /**
   * The source id.
   *
   * @var int
   */
  protected $sourceId;

  /**
   * The destination id.
   *
   * @var int
   */
  protected $destinationId;

  /**
   * {@inheritDoc}
   *
   * @return LinkEntity
   *
   * @throws Exception
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    throw new \Exception('createInstanceFromAcApiArray not supported. AC does not know this type. createInstance for indirect use.');
  }

  /**
   * Returns a new LinkEntity of type 'sub-task-link'.
   *
   * @return LinkEntity
   *
   * @throws Exception
   */
  public static function createSubtaskLinkInstance(int $subtaskExternalId, int $parentTaskExternalId) {
    return self::createInstance('sub-task-link', $subtaskExternalId, $parentTaskExternalId);
  }

  /**
   * Returns a new LinkEntity.
   *
   * @param string $name
   * @param int $sourceId
   * @param int $destinationId
   *
   * @return LinkEntity
   */
  public static function createInstance(string $name, int $sourceId, int $destinationId) {
    return new self($name, $sourceId, $destinationId);
  }

  /**
   * Constructor.
   *
   * @param string $name
   * @param int $sourceId
   * @param int $destinationId
   */
  protected function __construct(string $name, int $sourceId, int $destinationId) {
    $this->name = $name;
    $this->sourceId = $sourceId;
    $this->destinationId = $destinationId;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    return $array;
  }

}

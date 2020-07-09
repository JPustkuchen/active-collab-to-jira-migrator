<?php

namespace ActiveCollabToJiraMigrator\Export;

use ActiveCollabToJiraMigrator\JiraDataObjects\JiraMasterImportEntity;

/**
 * Abstraction if JiraExporterInterface classes.
 */
abstract class JiraExporterAbstract implements JiraExporterInterface {
  /**
   * The master import entity.
   *
   * @var ActiveCollabToJiraExportJson\JiraDataObjects\JiraMasterImportEntity
   */
  protected $jiraMasterImportEntity;

  /**
   * {@inheritDoc}
   */
  public static function createInstance(JiraMasterImportEntity $jiraMasterImportEntity) {
    return new static($jiraMasterImportEntity);
  }

  /**
   * Constructor.
   *
   * @param \ActiveCollabToJiraMigrator\JiraDataObjects\JiraMasterImportEntity $jiraMasterImportEntity
   */
  protected function __construct(JiraMasterImportEntity $jiraMasterImportEntity) {
    $this->jiraMasterImportEntity = $jiraMasterImportEntity;
  }

  /**
   * Get the master import entity.
   *
   * @return ActiveCollabToJiraExportJson\JiraDataObjects\JiraMasterImportEntity
   */
  protected function getJiraMasterImportEntity() {
    return $this->jiraMasterImportEntity;
  }

  /**
   * Set the master import entity.
   *
   * @param ActiveCollabToJiraExportJson\JiraDataObjects\JiraMasterImportEntity $jiraMasterImportEntity
   *   The master import entity.
   *
   * @return self
   */
  protected function setJiraMasterImportEntity(ActiveCollabToJiraExportJson\JiraDataObjects\JiraMasterImportEntity $jiraMasterImportEntity) {
    $this->jiraMasterImportEntity = $jiraMasterImportEntity;

    return $this;
  }

}

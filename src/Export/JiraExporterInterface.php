<?php

namespace ActiveCollabToJiraMigrator\Export;

use ActiveCollabToJiraMigrator\JiraDataObjects\JiraMasterImportEntity;

/**
 * JiraExporter Interface.
 */
interface JiraExporterInterface {

  /**
   * Factory.
   *
   * @param \ActiveCollabToJiraMigrator\JiraDataObjects\JiraMasterImportEntity $jiraMasterImportEntity
   *
   * @return self
   */
  public static function createInstance(JiraMasterImportEntity $jiraMasterImportEntity);

  /**
   * Runs the export.
   *
   * @param $options
   *   Options array for individual settings
   *
   * @return mixed The export result.
   */
  public function export(array $options = []);

}

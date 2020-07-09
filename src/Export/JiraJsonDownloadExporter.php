<?php

namespace ActiveCollabToJiraMigrator\Export;

/**
 * Handles export to Jira JSON.
 */
class JiraJsonDownloadExporter extends JiraJsonExporter implements JiraExporterInterface {

  /**
   * Returns the export json string.
   *
   * @return string
   */
  public function export(array $options = []) {
    return $this->toJsonFileDownload($options);
  }

  /**
   * Returns the filename incl. type suffix.
   *
   * @return string Generated filename
   */
  protected static function createFilename(array $options = []) {
    $filenameAppendix = '';
    if (!empty($options['filenameAppendix'])) {
      $filenameAppendix = '-' . $options['filenameAppendix'];
    }
    $filename = 'ActiveCollabToJiraExportJson-' . date('c') . $filenameAppendix . '.json';
    return $filename;
  }

  /**
   * Exports the json as file download.
   * Sends the required headers and creates the filename.
   *
   * @return string
   */
  protected function toJsonFileDownload(array $options = []) {
    $json = $this->toJson($options);
    $filename = self::createFilename($options);
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . strlen($json));
    echo $json;
    exit(0);
  }

}

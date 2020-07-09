<?php

namespace ActiveCollabToJiraMigrator\Export;

/**
 * Handles export to Jira JSON.
 */
class JiraRestExporter extends JiraExporterAbstract implements JiraExporterInterface {

  /**
   * {@inheritDoc}
   */
  public function export(array $options = []) {
    throw new \Exception('Not yet implemented!');
    // TODO - See https://developer.atlassian.com/cloud/jira/platform/rest/v2/
    // Implement API.
  }

}

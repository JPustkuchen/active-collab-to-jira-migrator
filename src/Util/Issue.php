<?php

namespace ActiveCollabToJiraMigrator\Util;

/**
 * Utility functions for issues.
 */
class Issue {

  /**
   * Helper function to map the ActiveCollab issue labels to Jira label.
   *
   * @param array $acIssueLabels An array of issue label arrays.
   *
   * @return array
   */
  public static function mapIssueLabels(array $acIssueLabels) {
    // TODO - Allow mapping in modifications.php.
    $result = ['acimport'];
    if (!empty($acIssueLabels)) {
      foreach ($acIssueLabels as $acIssueLabel) {
        if (is_array($acIssueLabel)) {
          $result[] = self::mapIssueLabel($acIssueLabel);
        } else {
          debug('Label could not be mapped, because it was not in expected array format: ' . var_export($acIssueLabel, 1));
        }
      }
    }
    return $result;
  }

  /**
   * Returns the label name from a single ActiveCollab Issue labels array.
   *
   * @param array $acIssueLabelArray
   *
   * @return string
   */
  public static function mapIssueLabel(array $acIssueLabelArray) {
    /*
    {
    "id": 9,
    "name": "FIXED",
    "color": "#98B57C",
    "darker_text_color": "#647157",
    "lighter_text_color": "#819F65",
    "is_default": false,
    "is_global": true,
    "position": "8",
    "url_path": "/labels/9"
    }
     */
    if (empty($acIssueLabelArray['name'])) {
      throw new Exception('Label name could not be determined from label array');
    }
    return $acIssueLabelArray['name'];
  }

}

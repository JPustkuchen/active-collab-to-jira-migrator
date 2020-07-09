<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;
use ActiveCollabToJiraMigrator\Util\Date;
use ActiveCollabToJiraMigrator\Util\Issue;

/**
 * Data object for issue subtasks.
 */
class IssueSubtaskEntity extends IssueEntity implements JiraImportEntityInterface {

  /**
   * Override type: Sub-task. Everything else is like an issue.
   */
  protected $issueType = 'Sub-task';

  /**
   * {@inheritDoc}
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueSubtaskEntity
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    /*
    {
    "single": {
    "id": 1,
    "class": "Subtask",
    "url_path": "\/projects\/1\/tasks\/1\/subtasks\/1",
    "assignee_id": 2,
    "delegated_by_id": 1,
    "completed_on": null,
    "completed_by_id": null,
    "is_completed": false,
    "is_trashed": true,
    "trashed_on": 1430164360,
    "trashed_by_id": 1,
    "created_on": 1430164359,
    "created_by_id": 1,
    "updated_on": 1430164360,
    "name": "Subtask #1",
    "task_id": 1,
    "project_id": 1,
    "due_on": null
    }
    }
     */
    // TODO - Improve this later?
    if (!empty($array['single'])) {
      throw new \Exception('NON-Detail array with NO "single" key at first level expected. Instead keys were: "' . implode(', ', array_keys($array)) . '"');
    }

    $settings = $migrationManager->getSettings();
    if ($array['is_trashed']) {
      // Do not import trashed.
      return FALSE;
    }
    // No custom priority in subtasks:
    $priority = 'Medium';
    $description = $array['name'];
    $status = !empty($array['is_completed']) ? 'Closed' : 'Open';
    $reporter = $migrationManager->mapAcUserIdToJiraUsername($array['created_by_id']);
    // Subtasks have no labes in AC:
    $issueLabels = [];
    $labels = Issue::mapIssueLabels($issueLabels);
    $created = Date::convertTimestampToSimpleDateFormat($array['created_on']);
    $issueType = 'Sub-task';
    $resolution = !empty($array['is_completed']) ? 'Resolved' : NULL;
    $created = Date::convertTimestampToSimpleDateFormat($array['created_on']);
    $updated = Date::convertTimestampToSimpleDateFormat($array['updated_on']);
    $duedate = Date::convertTimestampToSimpleDateFormat($array['due_on']);
    $affectedVersions = !empty($settings['default_project_versions']) ? $settings['default_project_versions'] : NULL;
    $summary = $description;
    $assignee = !empty($array['assignee_id']) ? $migrationManager->mapAcUserIdToJiraUsername($array['assignee_id']) : NULL;

    // No fixed versions.
    $fixedVersions = !empty($array['is_completed']) ? $affectedVersions : NULL;

    // No components.
    $components = [];
    $externalId = $array['id'];
    $externalProjectId = $array['project_id'];

    // No time tracking for subtasks:
    $originalEstimate = NULL;
    $timeSpent = NULL;
    $estimate = NULL;
    $duedate = NULL;

    // No subentries for subtasks! Set explicitely to null:
    $watchers = NULL;
    $worklogsEntities = NULL;
    $customFieldValuesEntities = NULL;
    $attachmentsEntities = NULL;
    $commentsEntities = NULL;
    $subtaskEntities = NULL;

    return new self(
      $priority,
      $description,
      $status,
      $reporter,
      $labels,
      $watchers,
      $issueType,
      $resolution,
      $created,
      $updated,
      $duedate,
      $affectedVersions,
      $summary,
      $assignee,
      $fixedVersions,
      $components,
      $externalId,
      $externalProjectId,
      $originalEstimate,
      $timeSpent,
      $estimate,
      $worklogsEntities,
      $customFieldValuesEntities,
      $attachmentsEntities,
      $commentsEntities,
      $subtaskEntities);
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    return parent::postprocessToArray($array);
  }

}

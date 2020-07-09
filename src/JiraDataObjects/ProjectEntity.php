<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;
use ActiveCollabToJiraMigrator\Util\Markup;

/**
 * Data object for a single project.
 */
class ProjectEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The project name.
   *
   * @var string
   */
  protected $name;

  /**
   * The unique project key.
   *
   * @var string
   */
  protected $key;

  /**
   * The project type.
   *
   * Always "software". Otherwise the import will fail if Jira Core is enabled,
   * see https://jira.atlassian.com/browse/JRASERVER-45676
   *
   * @var string
   */
  protected $type = 'software';

  /**
   * Project description.
   *
   * @var string
   */
  protected $description = '';

  /**
   * Array of versions. E.g. ['1.0'].
   *
   * @var array
   */
  protected $versions = [];

  /**
   * Array with strings of components to assign to this project.
   *
   * @var arraystringThecomponents
   */
  protected $components = ['UI / UX', 'SW-Development', 'Operations / Hosting', 'Project Management', 'Marketing'];

  /**
   * Array with elements of type <IssueEntity>.
   *
   * @mapTo issues
   * @var arrayIssueEntity
   */
  protected $issuesEntities = [];

  /**
   * {@inheritDoc}
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    /*
    {
    "single": {
    "id": 1,
    "class": "Project",
    "url_path": "\/projects\/1",
    "name": "New Project",
    "completed_on": null,
    "completed_by_id": null,
    "is_completed": false,
    "members": [ // TODO - Project members import not supported by Jira yet!
    1,
    2
    ],
    "category_id": 0,
    "label_id": 0, // TODO - Project label import not supported by Jira yet!
    "is_trashed": false,
    "trashed_on": null,
    "trashed_by_id": 0,
    "created_on": 1430164507,
    "created_by_id": 1, // TODO - Project role import not supported by Jira yet!
    "updated_on": 1430164508,
    "updated_by_id": 1,
    "body": null,
    "body_formatted": "",
    "company_id": 2,
    "leader_id": 1, // TODO Project role import not supported by Jira yet!
    "currency_id": 1,
    "template_id": 0,
    "based_on_type": null,
    "based_on_id": null,
    "email": "notifications+m2p-pNjJamX@mail.manageprojects.com",
    "is_tracking_enabled": true,
    "is_client_reporting_enabled": false,
    "budget": null,
    "count_tasks": 0,
    "count_discussions": 0,
    "count_files": 0,
    "count_notes": 0
    },
    "category": null,
    "hourly_rates": {
    "1": 100
    },
    "label_ids": [],
    "task_lists": null
    }
     */
    // TODO - Improve this later?
    if (empty($array['single'])) {
      throw new \Exception('Detail array with "single" key at first level expected. Instead keys were: "' . implode(', ', array_keys($array)) . '"');
    }

    if (!empty($array['single']['is_trashed']) || !empty($array['single']['is_sample'])) {
      // Do not create if trashed.
      return FALSE;
    }
    $settings = $migrationManager->getSettings();

    $name = $array['single']['name'];
    if(empty($settings['project_import_key_prefix'])){
      throw new \Exception('Missing setting "project_import_key_prefix".');
    }
    $key = $settings['project_import_key_prefix'] . $array['single']['id'];
    // Markup to markdown:
    $description = Markup::toJiraWikiSyntax($array['single']['body_formatted'], $migrationManager->getUserMapper());

    $issuesEntities = [];
    if (!empty($array['single']['count_tasks'])) {
      $issuesEntities = self::buildIssueEntities($array['single']['id'], $migrationManager);
      foreach ($issuesEntities as $issueEntity) {
        $subtaskEntities = $issueEntity->getSubtaskEntities();
        if (!empty($subtaskEntities)) {
          foreach ($subtaskEntities as $subtaskEntity) {
            $issuesEntities[] = $subtaskEntity;
          }
        }

        $subtaskLinkEntities = $issueEntity->getSubtaskEntitiesLinkEntities();
        // Add link entity
        // TODO - This could be improved later - it's not good that this JiraDataObject acts on the MasterImportEnttity.
        if (!empty($subtaskLinkEntities)) {
          foreach ($subtaskLinkEntities as $subtaskLinkEntity) {
            $migrationManager->getJiraMasterImportEntity()->addLink($subtaskLinkEntity);
          }
        }
      }
    }

    // We always use version 1.0 because AC doesn't know versions but allow
    // modifications.
    if (!empty($array['_versions'])) {
      // Info: This is typically not existing, but this way we allow modifications
      // to create it.
      $array['versions'] = $array['_versions'];
    }
    else {
      // Get from settings:
      if (!is_array($settings['default_project_versions'])) {
        throw new \Exception('Setting "default_project_versions" is missing.');
      }
      $versions = $settings['default_project_versions'];
    }

    return new self($name, $key, $description, $versions, $issuesEntities);
  }

  /**
   * Constructor.
   *
   * @param string $name
   * @param string $key
   * @param string $description
   * @param array $versions
   * @param array $issues
   */
  protected function __construct(string $name, string $key, string $description = '', array $versions = [], array $issuesEntities = []) {
    $this->name = $name;
    $this->key = $key;
    $this->description = $description;
    $this->versions = $versions;
    $this->issuesEntities = $issuesEntities;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    {
    "name": "A Sample Project",
    "key": "ASM",
    "description": "JSON file description",
    "versions": [],
    "components": ['UI / UX', 'Software', 'Operations / Hosting', 'Project Management', 'Marketing'],
    "issues": []
    }
     */
    $array['issues'] = [];
    if (!empty($array['issuesEntities'])) {
      foreach ($array['issuesEntities'] as $entity) {
        $array['issues'][] = $entity->toArray();
      }
    }
    unset($array['issuesEntities']);

    return $array;
  }

  // TODO: Moved from MigrationManager:
  // // TODO - Not supported by Jira Import yet:
  // /**
  //  * Migration processor for project expenses.
  //  *
  //  * @param int $projectId
  //  */
  // protected function buildExpenses($projectId, MigrationManager $migrationManager) {
  //   $fetchedProjectExpenses = $this->acApiFetcher->fetchProjectExpenses($projectId);
  //   return $fetchedProjectExpenses;
  // }
  // TODO - Not supported by Jira Import yet:
  // /**
  //  * Migration processor for project time records.
  //  *
  //  * @param int $projectId
  //  */
  // protected function processProjectTimeRecords($projectId, MigrationManager $migrationManager) {
  //   $fetchedProjectTimeRecords = $this->acApiFetcher->fetchProjectTimeRecords($projectId);
  //   return $fetchedProjectTimeRecords;
  // }.
  // TODO - Not supported by Jira Import yet:
  // /**
  //  * Migration processor for project task lists.
  //  *
  //  * @param int $projectId
  //  */
  // protected function processProjectTaskLists($projectId, MigrationManager $migrationManager) {
  //   // TODO - Should we skip this? Or import as components?
  //   $fetchedProjectTaskLists = $this->acApiFetcher->fetchProjectTaskLists($projectId);
  //   return $fetchedProjectTaskLists;
  // }.
  // TODO - Not supported by Jira Import yet:
  // /**
  //  * Migration processor for project notes.
  //  *
  //  * @param int $projectId
  //  */
  // protected function processProjectNotes($projectId, MigrationManager $migrationManager) {
  //   $fetchedProjectNotes = $this->acApiFetcher->fetchProjectNotes($projectId);
  //   return $fetchedProjectNotes;
  // }.
  // TODO - Not supported by Jira Import yet:
  // /**
  //  * Migration processor for project files.
  //  *
  //  * @param int $projectId
  //  */
  // protected function processProjectFiles($projectId, MigrationManager $migrationManager) {
  //   $fetchedProjectFiles = $this->acApiFetcher->fetchProjectFiles($projectId);
  //   return $fetchedProjectFiles;
  // }.

  /**
   * Fetches and builds the project tasks.
   *
   * @param int $projectId
   * @param \ActiveCollabToJiraMigrator\Process\MigrationManager $migrationManager
   *
   * @return array
   */
  protected static function buildIssueEntities($projectId, MigrationManager $migrationManager) {
    $taskRecordsArray = [];
    $acApiArray = $migrationManager->getAcApiFetcher()->fetchProjectTasks($projectId);
    if (!empty($acApiArray)) {
      foreach ($acApiArray as $taskRecordArray) {
        $issueEntity = IssueEntity::createInstanceFromAcApiArray($taskRecordArray, $migrationManager);
        if (!empty($issueEntity)) {
          $taskRecordsArray[] = $issueEntity;
        }
        else {
          debug('IssueEntity from $taskRecordArray "' . var_export($taskRecordArray, 1) . '" was not created (returned "' . var_export($issueEntity, 1) . '") and skipped thereby.');
        }
      }
    }
    return $taskRecordsArray;
  }

}

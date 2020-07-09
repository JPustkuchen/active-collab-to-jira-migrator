<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;
use ActiveCollabToJiraMigrator\Util\Markup;
use ActiveCollabToJiraMigrator\Util\Date;
use ActiveCollabToJiraMigrator\Util\Issue;

/**
 * Data object for issues.
 */
class IssueEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The issue key.
   *
   * Example: "ASM-123"
   *
   * @required
   * @var string
   */
  protected $key;

  /**
   * The issue priority.
   *
   * Example: "Major".
   *
   * @required
   * @var string
   */
  protected $priority;

  /**
   * The issue description.
   *
   * Example: "Some nice description here\nMaybe _italics_ or *bold*?".
   *
   * @var string
   */
  protected $description;

  /**
   * The issue status.
   *
   * Example: "Closed", "Open".
   *
   * @required
   * @var string
   */
  protected $status;

  /**
   * The issue reporter.
   *
   * Example: "username1"
   *
   * @optional
   * @var string
   */
  /**
   * TODO - Mapping!
   */
  protected $reporter;

  /**
   * The issue labels.
   *
   * Example: [ "impossible", "to", "test" ]
   *
   * @optional
   * @var array
   */
  protected $labels = [];

  /**
   * The issues watchers.
   *
   * Example: ['username1', 'username2']
   *
   * @optional
   * @var array
   */
  protected $watchers = [];

  /**
   * The issues type.
   *
   * Example: "Bug", "Task", "Sub-task"
   *
   * @required
   * @var string
   */
  /**
   * TODO - Allowed values list.
   */
  protected $issueType = 'Task';


  /**
   * The issues resolution status.
   *
   * Example: "Resolved"
   *
   * @optional
   * @var string
   */
  protected $resolution;

  /**
   * The issue created date.
   *
   * Example: 2012-08-31T17:59:02.161+0100
   *
   * @required
   * @var date
   */
  protected $created;

  /**
   * The issue last modified date.
   *
   * Example: 2012-08-31T17:59:02.161+0100
   *
   * @optional
   * @var date
   */
  protected $updated;

  /**
   * The issue due date.
   *
   * @optional
   * @var date
   */
  protected $duedate;

  /**
   * The affected versions. *UNUSED*.
   *
   * Example: ["1.0"]
   *
   * @optional
   * @var array
   */
  protected $affectedVersions = ['1.0-acimport'];

  /**
   * Short issue summary.
   *
   * Example: "My chore for today".
   *
   * @optional
   * @var string
   */
  protected $summary;

  /**
   * The issue assignee.
   *
   * @required
   * @var string
   */
  protected $assignee;

  /**
   * The fixed versions. *Unused*.
   *
   * Example: ['1.0', '1.1']
   *
   * @optional
   * @var array
   */
  protected $fixedVersions = [];

  /**
   * The assigned components.
   *
   * Example: ["Component", "AnotherComponent"],
   *
   * @optional
   * @var array
   */
  protected $components = [];

  /**
   * The external ID.
   *
   * Example: '1'
   *
   * @optional
   * @var string
   */
  protected $externalId;

  /**
   * The original estimate.
   *
   * Example: 'P1W3D'
   *
   * @optional
   * @var string
   */
  protected $originalEstimate;

  /**
   * The time spent on this issue.
   *
   * Example: 'PT4H'
   *
   * @optional
   * @var string
   */
  protected $timeSpent;

  /**
   * The estimate for this issue.
   *
   * Example: 'P2D'
   *
   * @optional
   * @var string
   */
  protected $estimate;

  /**
   * The worklog entities.
   *
   * @optional
   * @mapTo worklogs
   * @var array
   */
  protected $worklogsEntities = [];

  /**
   * The custom field entities entities.
   *
   * @optional
   * @mapTo customFieldValues
   * @var array
   */
  protected $customFieldValuesEntities = [];

  /**
   * The attachment entities.
   *
   * @optional
   * @mapTo attachments
   * @var array
   */
  protected $attachmentsEntities = [];

  /**
   * The comment entities.
   *
   * @optional
   * @mapTo comments
   * @var array
   */
  protected $commentsEntities = [];

  /**
   * The Sub-Task entities.
   *
   * @optional
   * @mapTo IssueSubtaskEntity with externalId set to this parent PLUS global link
   * @var array
   */
  protected $subtaskEntities = [];

  /**
   * The subtask entities link connections.
   * Jira handles subtasks like regular tasks but connected by links.
   *
   * @optional
   * @mapTo IssueSubtaskEntity with externalId set to this parent PLUS global link
   * @var array
   */
  protected $subtaskEntitiesLinkEntities = [];

  /**
   * The parent project external ID.
   *
   * @var int
   */
  protected $externalProjectId;

  /**
   * {@inheritDoc}
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueEntity
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {

    /*
    {
    "single": {
    "id": 1,
    "class": "Task",
    "url_path": "\/projects\/1\/tasks\/1",
    "name": "Test Task #1",
    "assignee_id": 0, // TODO - Not supported by Jira Import yet.
    "delegated_by_id": 0,
    "completed_on": null,
    "completed_by_id": null,
    "is_completed": false,
    "comments_count": 0,
    "attachments": [],
    "labels": [],
    "is_trashed": false,
    "trashed_on": null,
    "trashed_by_id": 0,
    "project_id": 1,
    "is_hidden_from_clients": false, // TODO - Not supported by Jira Import yet.
    "body": "",
    "body_formatted": "",
    "created_on": 1430164444,
    "created_by_id": 1,
    "updated_on": 1430164446,
    "updated_by_id": 1,
    "task_number": 1,
    "task_list_id": 0, // TODO - Not supported by Jira Import yet.
    "position": 1, // TODO - Not supported by Jira Import yet.
    "is_important": false,
    "due_on": null, // TODO - Not supported by Jira Import yet.
    "estimate": 0,
    "job_type_id": 0,
    "total_subtasks": 0,
    "completed_subtasks": 0,
    "open_subtasks": 0
    },
    "subscribers": [
    1
    ],
    "comments": [],
    "reminders": [], // TODO - Not supported by Jira Import yet.
    "subtasks": [],
    "task_list": null, // TODO - Not supported by Jira Import yet.
    "tracked_time": 2.5,
    "tracked_expenses": 0 // TODO - Not supported by Jira Import yet.
    }
     */

    // TODO - Improve this later?
    if (empty($array['single'])) {
      throw new \Exception('Detail array with "single" key at first level expected. Instead keys were: "' . implode(', ', array_keys($array)) . '"');
    }

    $settings = $migrationManager->getSettings();

    $priority = $array['single']['is_important'] ? 'High' : 'Medium';
    $description = Markup::toJiraWikiSyntax($array['single']['body_formatted'], $migrationManager->getUserMapper());
    $status = !empty($array['single']['is_completed']) ? 'Closed' : 'Open';
    $reporter = $migrationManager->mapAcUserIdToJiraUsername($array['single']['created_by_id']);
    $labels = Issue::mapIssueLabels($array['single']['labels']);
    $created = Date::convertTimestampToSimpleDateFormat($array['single']['created_on']);
    $watchers = $migrationManager->mapAcUserIdsToJiraUsername($array['subscribers']);
    $issueType = !empty($settings['issue_default_type']) ? $settings['issue_default_type'] : 'Task';
    $resolution = !empty($array['single']['is_completed']) ? 'Resolved' : NULL;
    $created = Date::convertTimestampToSimpleDateFormat($array['single']['created_on']);
    $updated = Date::convertTimestampToSimpleDateFormat($array['single']['updated_on']);
    $duedate = Date::convertTimestampToSimpleDateFormat($array['single']['due_on']);
    $affectedVersions = !empty($settings['default_project_versions']) ? $settings['default_project_versions'] : NULL;
    // Summary may not be longer than 255 characters:
    $summary = substr($array['single']['name'], 0, 250);

    $assignee = $migrationManager->mapAcUserIdToJiraUsername($array['single']['assignee_id']);
    // No fixed versions.
    $fixedVersions = !empty($array['single']['is_completed']) ? $affectedVersions : NULL;
    // No components.
    $components = [];
    $externalId = $array['single']['id'];
    $externalProjectId = $array['single']['project_id'];
    $originalEstimate = Date::convertHoursToDuration($array['single']['estimate']);
    $timeSpent = Date::convertHoursToDuration($array['tracked_time']);
    $estimate = $originalEstimate;

    $worklogsEntities = [];
    if (!empty($array['tracked_time'])) {
      $worklogsEntities = self::buildWorklogsEntities($externalProjectId, $externalId, $migrationManager);
    }

    // Not required yet. TODO - allow for modifications.
    $customFieldValuesEntities = [
      // Jira Import expects these field values to be String!
      CustomFieldValueEntity::createInstance('ActiveCollab Project ID (Migrated)', 'com.atlassian.jira.plugin.system.customfieldtypes:textfield', (string) $externalProjectId),
      // Jira Import expects these field values to be String!
      CustomFieldValueEntity::createInstance('ActiveCollab Task Number (Migrated)', 'com.atlassian.jira.plugin.system.customfieldtypes:textfield', (string) $array['single']['task_number']),
    ];

    $attachmentsEntities = [];
    if (!empty($array['single']['attachments'])) {
      if (!empty($array['single']['attachments'])) {
        foreach ($array['single']['attachments'] as $attachmentArray) {
          $attachmentsEntities[] = AttachmentEntity::createInstanceFromAcApiArray($attachmentArray, $migrationManager);
        }
      }
    }

    $commentsEntities = [];
    if (!empty($array['comments'])) {
      $commentsEntities = self::buildCommentsEntities($externalProjectId, $externalId, $migrationManager);
    }

    $subtaskEntities = [];
    if (!empty($array['subtasks'])) {
      $subtaskEntities = self::buildSubtasksEntities($externalProjectId, $externalId, $migrationManager);
    }

    // Add AC Task-List as Label because task-links are not supported in Jira:
    if (!empty($array['task_list'])) {
      // Replace special characters by minus:
      $labels[] = preg_replace("/[^A-Za-z0-9]+/", '-', $array['task_list']['name']);
    }

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
   *
   */
  protected function __construct(
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
    $subtaskEntities) {

    $this->priority = $priority;
    $this->description = $description;
    $this->status = $status;
    $this->reporter = $reporter;
    $this->labels = $labels;
    $this->watchers = $watchers;
    $this->issueType = $issueType;
    $this->resolution = $resolution;
    $this->created = $created;
    $this->updated = $updated;
    $this->duedate = $duedate;
    $this->affectedVersions = $affectedVersions;
    $this->summary = $summary;
    $this->assignee = $assignee;
    $this->fixedVersions = $fixedVersions;
    $this->components = $components;
    $this->externalId = $externalId;
    $this->externalProjectId = $externalProjectId;
    $this->originalEstimate = $originalEstimate;
    $this->timeSpent = $timeSpent;
    $this->estimate = $estimate;
    $this->worklogsEntities = $worklogsEntities;
    $this->customFieldValuesEntities = $customFieldValuesEntities;
    $this->attachmentsEntities = $attachmentsEntities;
    $this->commentsEntities = $commentsEntities;
    $this->subtaskEntities = $subtaskEntities;

    // We create these dynamically here:
    $this->subtaskEntitiesLinkEntities = $this->buildSubtaskEntitiesLinkEntities();
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    {
    "key": "ASM-123",
    "priority" : "Major",
    "description" : "Some nice description here\nMaybe _italics_ or *bold*?",
    "status" : "Closed",
    "reporter" : "abcde-12345-fedcba",
    "labels" : [ "impossible", "to", "test" ],
    "watchers" : [ "abcde-12345-fedcba" ],
    "issueType" : "Bug",
    "resolution" : "Resolved",
    "created" : "2012-08-31T17:59:02.161+0100",
    "updated" : "P-1D",
    "affectedVersions" : [ "1.0" ],
    "summary" : "My chore for today",
    "assignee" : "abcde-12345-fedcba",
    "fixedVersions" : [ "1.0", "2.0" ],
    "components" : ["Component", "AnotherComponent"],
    "externalId" : "1",
    "originalEstimate": "P1W3D",
    "timeSpent": "PT4H",
    "estimate": "P2D",
    "worklogs": [
    {
    "author": "abcde-12345-fedcba",
    "comment": "Worklog",
    "startDate": "P-1D", //can be a Period or DateTime
    "timeSpent": "PT1M"
    },
    {
    "author": "abcde-12345-fedcba",
    "startDate": "2014-01-14T17:00:00.000+0100",
    "timeSpent": "PT3H"
    }
    ],
    "customFieldValues": [
    {
    "fieldName": "Story Points",
    "fieldType": "com.atlassian.jira.plugin.system.customfieldtypes:float",
    "value": "15"
    },
    {
    "fieldName": "Business Value",
    "fieldType": "com.atlassian.jira.plugin.system.customfieldtypes:float",
    "value": "34"
    }
    ],
    "attachments" : [
    {
    "name" : "battarang.jpg",
    "attacher" : "bob@example.com",
    "created" : "2012-08-31T17:59:02.161+0100",
    "uri" : "http://optimus-prime/~batman/images/battarang.jpg",
    "description" : "This is optimus prime"
    }
    ],
    "comments": [
    {
    "body": "This is a comment from admin 5 days ago",
    "author": "abcde-12345-fedcba",
    "created": "2012-08-31T17:59:02.161+0100"
    },
    {
    "body": "This is a comment from admin 1 day ago",
    "author": "abcde-12345-fedcba",
    "created": "2012-08-31T17:59:02.161+0100"
    }
    ]
    },
    {
    "status" : "Open",
    "reporter" : "abcde-12345-fedcba",
    "issueType": "Sub-task",
    "created" : "P-3D",
    "updated" : "P-1D",
    "summary" : "Sub-task",
    "externalId": "2"
    },
    {
    "status" : "Closed",
    "reporter" : "abcde-12345-fedcba",
    "issueType": "Sub-task",
    "created" : "P-3D",
    "updated" : "P-1D",
    "resolution" : "Duplicate",
    "summary" : "Duplicate Sub-task",
    "externalId": "3"
    }
     */

    // Process to array & unset entities.
    $array['worklogs'] = [];
    if (!empty($array['worklogsEntities'])) {
      foreach ($array['worklogsEntities'] as $entity) {
        $array['worklogs'][] = $entity->toArray();
      }
    }
    unset($array['worklogsEntities']);

    $array['customFieldValues'] = [];
    if (!empty($array['customFieldValuesEntities'])) {
      foreach ($array['customFieldValuesEntities'] as $entity) {
        $array['customFieldValues'][] = $entity->toArray();
      }
    }
    unset($array['customFieldValuesEntities']);

    $array['attachments'] = [];
    if (!empty($array['attachmentsEntities'])) {
      foreach ($array['attachmentsEntities'] as $entity) {
        $array['attachments'][] = $entity->toArray();
      }
    }
    unset($array['attachmentsEntities']);

    $array['comments'] = [];
    if (!empty($array['commentsEntities'])) {
      foreach ($array['commentsEntities'] as $entity) {
        $array['comments'][] = $entity->toArray();
      }
    }
    unset($array['commentsEntities']);

    // SubtaskEntities are handled separately!
    // Jira retrieves subtasks like other tasks of the same project
    // and on the same hierarchy level but connects them by subtaskEntitiesLinkEntities.
    // Hint: Call getSubtaskEntities() and getSubtaskEntitiesLinkEntities() on
    // the project level to also import subtaskEntities and subtaskEntitiesLinkEntities.
    unset($array['subtaskEntities']);
    unset($array['subtaskEntitiesLinkEntities']);

    // ExternalProjectId is only used internally:
    unset($array['externalProjectId']);

    return $array;
  }

  /**
   * Fetches and builds the CommentEntities.
   *
   * @param int $projectId
   * @param int $taskId
   * @param \ActiveCollabToJiraMigrator\Process\MigrationManager $migrationManager
   *
   * @return array
   */
  protected static function buildCommentsEntities(int $projectId, int $taskId, MigrationManager $migrationManager) {
    $commentsArray = [];
    $acApiArray = $migrationManager->getAcApiFetcher()->fetchProjectTaskComments($projectId, $taskId);
    if (!empty($acApiArray)) {
      foreach ($acApiArray as $commentArray) {
        $commentEntity = CommentEntity::createInstanceFromAcApiArray($commentArray, $migrationManager);
        if (!empty($commentEntity)) {
          $commentsArray[] = $commentEntity;
        }
        else {
          debug('CommentEntity from $commentArray was not created (returned "' . var_export($commentEntity, 1) . '") and skipped thereby.');
        }
      }
    }
    return $commentsArray;

  }

  /**
   * Fetches and builds the ExpensesEntities.
   *
   * @param int $projectId
   * @param int $taskId
   * @param \ActiveCollabToJiraMigrator\Process\MigrationManager $migrationManager
   *
   * @return array
   */
  protected static function buildExpensesEntities(int $projectId, int $taskId, MigrationManager $migrationManager) {
    // TODO - Not yet implemented, Jira import doesn't support expenses!
    throw new \Exception('Not supported yet.');
    // $expensesArray = [];
    // $acApiArray = $migrationManager->getAcApiFetcher()->fetchProjectTaskExpenses($projectId, $taskId);
    // if(!empty($acApiArray)){
    //   foreach($acApiArray as $expenseArray){
    //     $expensesArray[] = ExpenseEntity::createInstanceFromAcApiArray($expenseArray);
    //   }
    // }
    // return $expensesArray;
  }

  /**
   * Fetches and builds the WorklogEntities.
   *
   * @param int $projectId
   * @param int $taskId
   * @param \ActiveCollabToJiraMigrator\Process\MigrationManager $migrationManager
   *
   * @return array
   */
  protected static function buildWorklogsEntities(int $projectId, int $taskId, MigrationManager $migrationManager) {
    $worklogsArray = [];
    $acApiArray = $migrationManager->getAcApiFetcher()->fetchProjectTaskTimeRecords($projectId, $taskId);
    if (!empty($acApiArray)) {
      foreach ($acApiArray as $worklogArray) {
        $worklogEntity = WorklogEntity::createInstanceFromAcApiArray($worklogArray, $migrationManager);
        if (!empty($worklogEntity)) {
          $worklogsArray[] = $worklogEntity;
        }
        else {
          debug('WorklogEntity from $worklogArray "' . var_export($worklogsArray, 1) . '" was not created (returned "' . var_export($worklogEntity, 1) . '") and skipped thereby.');
        }
      }
    }
    return $worklogsArray;
  }

  /**
   * Fetches and builds the SubtaskEntities.
   *
   * @param int $projectId
   * @param int $taskId
   * @param \ActiveCollabToJiraMigrator\Process\MigrationManager $migrationManager
   *
   * @return array
   */
  protected static function buildSubtasksEntities(int $projectId, int $taskId, MigrationManager $migrationManager) {
    $subtasksArray = [];
    $acApiArray = $migrationManager->getAcApiFetcher()->fetchProjectTaskSubtasks($projectId, $taskId);
    if (!empty($acApiArray)) {
      foreach ($acApiArray as $subtaskArray) {
        $subtaskEntity = IssueSubtaskEntity::createInstanceFromAcApiArray($subtaskArray, $migrationManager);
        if (!empty($subtaskEntity)) {
          $subtasksArray[] = $subtaskEntity;
        }
        else {
          debug('IssueSubtaskEntity from $subtaskArray was not created (returned "' . var_export($subtaskEntity, 1) . '") and skipped thereby.');
        }
      }
    }
    return $subtasksArray;
  }

  /**
   * Builds the $subtaskEntitiesLinkEntities from the $this->subtaskEntities.
   *
   * @return arrayLinkEntity
   */
  protected function buildSubtaskEntitiesLinkEntities() {
    $subtaskEntitiesLinkEntities = [];
    if (!empty($this->subtaskEntities)) {
      foreach ($this->subtaskEntities as $subtaskEntity) {
        $parentTaskExternalId = $this->getExternalId();
        $subtaskExternalId = $subtaskEntity->getExternalId();
        $subtaskEntitiesLinkEntities[] = LinkEntity::createSubtaskLinkInstance($subtaskExternalId, $parentTaskExternalId);
      }
    }
    return $subtaskEntitiesLinkEntities;
  }

  /**
   * Adds the given element to $worklogsEntities.
   *
   * @param WorklogEntity $worklogEntity
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueEntity
   */
  public function addWorklogEntity(WorklogEntity $worklogEntity) {
    $this->worklogsEntities[] = $worklogEntity;

    return $this;
  }

  /**
   * Adds the given element to $customFieldValuesEntities.
   *
   * @param CustomFieldValueEntity $customFieldValueEntity
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueEntity
   */
  public function addCustomFieldValueEntity(CustomFieldValueEntity $customFieldValueEntity) {
    $this->customFieldValuesEntities[] = $customFieldValueEntity;

    return $this;
  }

  /**
   * Adds the given element to $attachmentsEntities.
   *
   * @param AttachmentEntity $attachmentEntity
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueEntity
   */
  public function addAttachmentEntity(AttachmentEntity $attachmentEntity) {
    $this->attachmentsEntities[] = $attachmentEntity;

    return $this;
  }

  /**
   * Adds the given element to $comments.
   *
   * @param CommentEntity $commentEntity
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueEntity
   */
  public function addCommentEntity(CommentEntity $commentEntity) {
    $this->comments[] = $commentEntity;

    return $this;
  }

  /**
   * Adds the given element to $subtaskEntities.
   *
   * @param \ActiveCollabToJiraMigrator\Util\IssueSubtaskEntity $subtaskEntity
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueEntity
   */
  public function addSubtaskEntity(IssueSubtaskEntity $subtaskEntity) {
    $this->subtaskEntities[] = $subtaskEntity;

    return $this;
  }

  /**
   * Adds the given element to $watchers.
   *
   * @param string $watcher
   *
   * @return \ActiveCollabToJiraMigrator\Util\IssueEntity
   */
  public function addWatcher(string $watcher) {
    $this->watchers[] = $watcher;

    return $this;
  }

  /**
   * Get example: "ASM-123".
   *
   * @return string
   */
  public function getKey() {
    return $this->key;
  }

  /**
   * Set example: "ASM-123".
   *
   * @param string $key
   *   Example: "ASM-123".
   *
   * @return self
   */
  public function setKey(string $key) {
    $this->key = $key;

    return $this;
  }

  /**
   * Get example: "Major".
   *
   * @return string
   */
  public function getPriority() {
    return $this->priority;
  }

  /**
   * Set example: "Major".
   *
   * @param string $priority
   *   Example: "Major".
   *
   * @return self
   */
  public function setPriority(string $priority) {
    $this->priority = $priority;

    return $this;
  }

  /**
   * Get the issue description.
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set the issue description.
   *
   * @return self
   */
  public function setDescription($description) {
    $this->description = $description;

    return $this;
  }

  /**
   * Get example: "Major".
   *
   * @return string
   */
  public function getStatus() {
    return $this->status;
  }

  /**
   * Set example: "Major".
   *
   * @param string $status
   *   Example: "Major".
   *
   * @return self
   */
  public function setStatus($status) {
    $this->status = $status;

    return $this;
  }

  /**
   * Get the reporter username.
   */
  public function getReporter() {
    return $this->reporter;
  }

  /**
   * Set the reporter username.
   *
   * @return self
   */
  public function setReporter($reporter) {
    $this->reporter = $reporter;

    return $this;
  }

  /**
   * Get the parent project external ID.
   *
   * @return int
   */
  public function getExternalProjectId() {
    return $this->externalProjectId;
  }

  /**
   * Set the parent project external ID.
   *
   * @param int $externalProjectId
   *   The parent project external ID.
   *
   * @return self
   */
  public function setExternalProjectId($externalProjectId) {
    $this->externalProjectId = $externalProjectId;

    return $this;
  }

  /**
   * Get jira handles subtasks like regular tasks but connected by links.
   *
   * @return array
   */
  public function getSubtaskEntitiesLinkEntities() {
    return $this->subtaskEntitiesLinkEntities;
  }

  /**
   * Set jira handles subtasks like regular tasks but connected by links.
   *
   * @param array $subtaskEntitiesLinkEntities
   *   Jira handles subtasks like regular tasks but connected by links.
   *
   * @return self
   */
  public function setSubtaskEntitiesLinkEntities(array $subtaskEntitiesLinkEntities) {
    $this->subtaskEntitiesLinkEntities = $subtaskEntitiesLinkEntities;

    return $this;
  }

  /**
   * Get the Sub-Task entities.
   *
   * @return array
   */
  public function getSubtaskEntities() {
    return $this->subtaskEntities;
  }

  /**
   * Set the Sub-Task entities.
   *
   * @param array $subtaskEntities
   *   The Sub-Task entities.
   *
   * @return self
   */
  public function setSubtaskEntities(array $subtaskEntities) {
    $this->subtaskEntities = $subtaskEntities;

    return $this;
  }

  /**
   * Get example: '1'.
   *
   * @return string
   */
  public function getExternalId() {
    return $this->externalId;
  }

  /**
   * Set example: '1'.
   *
   * @param string $externalId
   *   Example: '1'.
   *
   * @return self
   */
  public function setExternalId(string $externalId) {
    $this->externalId = $externalId;

    return $this;
  }

}

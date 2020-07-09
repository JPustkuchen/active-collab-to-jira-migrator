<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;
use ActiveCollabToJiraMigrator\Util\Date;

/**
 * Data object for worklogs.
 */
class WorklogEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The worklog author.
   *
   * Example: 'username1'
   *
   * @var string
   */
  protected $author;

  /**
   * The worklog comment.
   * *optional*.
   *
   * Example: 'Worklog'
   *
   * @var string
   */
  protected $comment;

  /**
   * The worklog start date in DateTime format.
   *
   * Example: '2014-01-14T17:00:00.000+0100'
   *
   * @var string
   */
  protected $startDate;

  /**
   * The time spent on this issue.
   *
   * Example: 'PT3H'
   *
   * @var string
   */
  /**
   * TODO - Convert!
   */
  protected $timeSpent;

  /**
   * {@inheritDoc}
   *
   * @return WorklogEntity
   *
   * @throws Exception
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    /*
    {
    "id": 2,
    "class": "TimeRecord",
    "url_path": "\/projects\/1\/time-records\/2",
    "is_trashed": false,
    "trashed_on": null,
    "trashed_by_id": 0,
    "billable_status": 1,
    "value": 2.5,
    "record_date": 1400025600,
    "summary": null,
    "user_id": 1,
    "parent_type": "Task",
    "parent_id": 1,
    "created_on": 1430164446,
    "created_by_id": 1,
    "updated_on": 1430164446,
    "updated_by_id": 1,
    "job_type_id": 1
    },
     */
    // TODO - Improve this later?:
    if (!empty($array['single'])) {
      throw new \Exception('NON-Detail array with NO "single" key at first level expected. Instead keys were: "' . implode(', ', array_keys($array)) . '"');
    }

    if (!empty($array['is_trashed']) || $array['billable_status'] != 1) {
      // Do not create if trashed or not billable.
      return FALSE;
    }

    $author = $migrationManager->mapAcUserIdToJiraUsername($array['user_id']);
    $comment = $array['summary'];
    $startDate = $created = Date::convertTimestampToSimpleDateFormat($array['record_date']);
    $timeSpent = Date::convertHoursToDuration($array['value']);

    return new self($author, $comment, $startDate, $timeSpent);
  }

  /**
   * Returns a new WorklogEntity.
   *
   * @param string $author
   * @param string $comment
   * @param string $startDate
   * @param string $timeSpent
   *
   * @return WorklogEntity
   */
  public function createInstance(string $author, string $comment, string $startDate, string $timeSpent) {
    return new self($author, $comment, $startDate, $timeSpent);
  }

  /**
   * Constructor.
   *
   * @param string $author
   * @param string $comment
   * @param string $startDate
   * @param string $timeSpent
   */
  protected function __construct(string $author, string $comment, string $startDate, string $timeSpent) {
    $this->author = $author;
    $this->comment = $comment;
    $this->startDate = $startDate;
    $this->timeSpent = $timeSpent;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    {
    "author": "abcde-12345-fedcba",
    "comment": "Worklog",
    "startDate": "2012-08-31T17:59:02.161+0100",
    "timeSpent": "PT1M"
    },
     */
    return $array;
  }

}

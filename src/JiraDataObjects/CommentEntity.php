<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;
use ActiveCollabToJiraMigrator\Util\Markup;
use ActiveCollabToJiraMigrator\Util\Date;

/**
 * Data object for worklogs.
 */
class CommentEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The comment text.
   *
   * Example: 'This is a comment from admin 5 days ago'
   *
   * @required
   * @var string
   */
  protected $body;

  /**
   * The comment author.
   *
   * Example: 'username1'
   *
   * @required
   * @var string
   */
  protected $author;

  /**
   * The created date of the comment.
   *
   * Example: '2014-01-14T17:00:00.000+0100'
   *
   * @required
   * @var string
   */
  protected $created;

  /**
   * {@inheritDoc}
   *
   * @return CommentEntity
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    /*
    {
    "id": 14791,
    "class": "Comment",
    "url_path": "/comments/14791",
    "attachments": [], // TODO - Not supported by Jira Import yet.
    "is_trashed": false,
    "trashed_on": null,
    "trashed_by_id": 0,
    "reactions": [], // TODO - Not supported by Jira Import yet.
    "parent_type": "Task",
    "parent_id": 9328,
    "body": "<p>15-30 Minuten</p>",
    "body_formatted": "<p>15-30 Minuten</p>",
    "body_plain_text": "15-30 Minuten",
    "created_on": 1574170957,
    "created_by_id": 2,
    "created_by_name": "Thomas Frobieter",
    "created_by_email": "tf@webks.de",
    "updated_on": 1574170957,
    "updated_by_id": null
    },
     */

    // TODO - Improve this later?
    if (!empty($array['single'])) {
      throw new \Exception('NON-Detail array with NO "single" key at first level expected. Instead keys were: "' . implode(', ', array_keys($array)) . '"');
    }

    if (!empty($array['is_trashed'])) {
      // Do not create if trashed.
      return FALSE;
    }

    $body = Markup::toJiraWikiSyntax($array['body_formatted'], $migrationManager->getUserMapper());
    $author = $migrationManager->mapAcUserIdToJiraUsername($array['created_by_id']);
    $created = Date::convertTimestampToSimpleDateFormat($array['created_on']);

    return new self($body, $author, $created);
  }

  /**
   * Constructor.
   *
   * @param string $body
   * @param string $author
   * @param string $created
   */
  protected function __construct(string $body, string $author, string $created) {
    $this->body = $body;
    $this->author = $author;
    $this->created = $created;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    {
    "body": "This is a comment from admin 5 days ago",
    "author": "abcde-12345-fedcba",
    "created": "2012-08-31T17:59:02.161+0100"
    },
     */
    return $array;
  }

  /**
   * Get example: 'This is a comment from admin 5 days ago'.
   *
   * @return string
   */
  public function getBody() {
    return $this->body;
  }

  /**
   * Set example: 'This is a comment from admin 5 days ago'.
   *
   * @param string $body
   *   Example: 'This is a comment from admin 5 days ago'.
   *
   * @return self
   */
  public function setBody(string $body) {
    $this->body = $body;

    return $this;
  }

  /**
   * Get example: 'username1'.
   *
   * @return string
   */
  public function getAuthor() {
    return $this->author;
  }

  /**
   * Set example: 'username1'.
   *
   * @param string $author
   *   Example: 'username1'.
   *
   * @return self
   */
  public function setAuthor(string $author) {
    $this->author = $author;

    return $this;
  }

  /**
   * Get example: '2014-01-14T17:00:00.000+0100'.
   *
   * @return string
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * Set example: '2014-01-14T17:00:00.000+0100'.
   *
   * @param string $created
   *   Example: '2014-01-14T17:00:00.000+0100'.
   *
   * @return self
   */
  public function setCreated(string $created) {
    $this->created = $created;

    return $this;
  }

}

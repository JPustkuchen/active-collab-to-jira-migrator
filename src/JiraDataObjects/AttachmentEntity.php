<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;
use ActiveCollabToJiraMigrator\Util\Date;

/**
 * Data object for attachments.
 */
class AttachmentEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The name of the attachment.
   *
   * @required
   * @var string
   */
  protected $name;

  /**
   * The user who uploaded this attachment. Example: 'username1'.
   *
   * @required
   * @var string
   */
  protected $attacher;

  /**
   * The date when this attachment was created.
   *
   * Example: "2012-08-31T17:59:02.161+0100".
   *
   * @required
   * @var string
   */
  protected $created;

  /**
   * The public URI of the attachment to fetch.
   *
   * Example: "http://optimus-prime/~batman/images/battarang.jpg".
   *
   * @required
   * @var string
   */
  protected $uri;

  /**
   * The attachment description. Example: 'This is optimus prime'.
   *
   * @optional
   * @var string
   */
  protected $description;

  /**
   * {@inheritDoc}
   *
   * @return AttachmentEntity
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    /*
    {
    "id": 1,
    "class": "Attachment",
    "url_path": "\/attachments\/1",
    "name": "ac.png",
    "parent_type": "Task",
    "parent_id": 1,
    "mime_type": "image\/png",
    "size": 1927,
    "md5": "1e3cd308a17a60b22c61493dcb3af0b0",
    "download_url": "http:\/\/feather.dev\/attachments\/1\/download",
    "thumbnail_url": "http:\/\/feather.dev\/proxy.php?proxy=forward_thumbnail&module=system&v=current&b=DEV&context=upload&name=2015-04%2FECHioEl3EL0fLzz7V92nIhpQqN3VE6o7Bxj7Iazj&original_file_name=ac.png&width=--WIDTH--&height=--HEIGHT--&ver=1927&scale=--SCALE--",
    "file_meta": {
    "kind": "image",
    "dimensions": {
    "width": 50,
    "height": 50
    }
    },
    "created_on": 1430164308,
    "created_by_id": 1,
    "disposition": "attachment",
    "project_id": 1,
    "is_hidden_from_clients": false // TODO - Not supported by Jira Import yet.
    }
     */
    // TODO - Improve this later?
    if (!empty($array['single'])) {
      throw new \Exception('NON-Detail array with NO "single" key at first level expected. Instead keys were: "' . implode(', ', array_keys($array)) . '"');
    }

    $settings = $migrationManager->getSettings();

    $name = $array['name'];
    $attacher = $migrationManager->mapAcUserIdToJiraUsername($array['created_by_id']);
    $created = Date::convertTimestampToSimpleDateFormat($array['created_on']);

    if (empty($settings['attachment_proxy_url'])) {
      throw new \Exception('Setting "attachment_proxy_url" is missing.');
    }
    // TODO - finalize! Authentication won't work this way, look at token type and bootstrap!
    $uri = $settings['attachment_proxy_url']
    . '&token=' . $migrationManager->getAcApiFetcher()->getAcClient()->getToken()
    . '&attachmentId=' . $array['id'];
    $description = $array['name'] . ' migrated from ActiveCollab at ' . date('c');

    return new self($name, $attacher, $created, $uri, $description);
  }

  /**
   * Constructor.
   *
   * @param string $name
   * @param string $attacher
   * @param string $created
   * @param string $uri
   * @param string $description
   */
  protected function __construct(string $name, string $attacher, string $created, string $uri, string $description = '') {
    $this->name = $name;
    $this->attacher = $attacher;
    $this->created = $created;
    $this->uri = $uri;
    $this->description = $description;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    {
    "name" : "battarang.jpg",
    "attacher" : "bob@example.com",
    "created" : "2012-08-31T17:59:02.161+0100",
    "uri" : "http://optimus-prime/~batman/images/battarang.jpg",
    "description" : "This is optimus prime"
    }
     */
    return $array;
  }

  /**
   * Get the name of the attachment.
   *
   * @return string
   */
  public function getName() {
    return $this->name;
  }

  /**
   * Set the name of the attachment.
   *
   * @param string $name
   *   The name of the attachment.
   *
   * @return self
   */
  public function setName(string $name) {
    $this->name = $name;

    return $this;
  }

  /**
   * Get the user who uploaded this attachment. Example: 'username1'.
   *
   * @return string
   */
  public function getAttacher() {
    return $this->attacher;
  }

  /**
   * Set the user who uploaded this attachment. Example: 'username1'.
   *
   * @param string $attacher
   *   The user who uploaded this attachment. Example: 'username1'.
   *
   * @return self
   */
  public function setAttacher(string $attacher) {
    $this->attacher = $attacher;

    return $this;
  }

  /**
   * Get example: "2012-08-31T17:59:02.161+0100".
   *
   * @return string
   */
  public function getCreated() {
    return $this->created;
  }

  /**
   * Set example: "2012-08-31T17:59:02.161+0100".
   *
   * @param string $created
   *   Example: "2012-08-31T17:59:02.161+0100".
   *
   * @return self
   */
  public function setCreated(string $created) {
    $this->created = $created;

    return $this;
  }

  /**
   * Get example: "http://optimus-prime/~batman/images/battarang.jpg".
   *
   * @return string
   */
  public function getUri() {
    return $this->uri;
  }

  /**
   * Set example: "http://optimus-prime/~batman/images/battarang.jpg".
   *
   * @param string $uri
   *   Example: "http://optimus-prime/~batman/images/battarang.jpg".
   *
   * @return self
   */
  public function setUri(string $uri) {
    $this->uri = $uri;

    return $this;
  }

  /**
   * Get the attachment description. Example: 'This is optimus prime'.
   *
   * @return string
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * Set the attachment description. Example: 'This is optimus prime'.
   *
   * @param string $description
   *   The attachment description. Example: 'This is optimus prime'.
   *
   * @return self
   */
  public function setDescription(string $description) {
    $this->description = $description;

    return $this;
  }

}

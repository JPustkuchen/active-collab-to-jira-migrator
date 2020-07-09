<?php

namespace ActiveCollabToJiraMigrator\Process;

use ActiveCollabToJiraMigrator\Fetch\AcApiFetcher;
use ActiveCollab\SDK\Client;
use ActiveCollabToJiraMigrator\Export\JiraExporterInterface;
use ActiveCollabToJiraMigrator\JiraDataObjects\JiraMasterImportEntity;
use ActiveCollabToJiraMigrator\JiraDataObjects\UserEntity;
use ActiveCollabToJiraMigrator\JiraDataObjects\ProjectEntity;

require_once '../config/modifications.php';

/**
 * The migration manager.
 */
class MigrationManager {

  /**
   * AcApiFetcher instance.
   *
   * @var \ActiveCollabToJiraMigrator\Fetch\AcApiFetcher
   */
  protected $acApiFetcher;

  /**
   * The JiraMasterImportEntity which we act on.
   *
   * @var \ActiveCollabToJiraMigrator\JiraDataObjects\JiraMasterImportEntity
   */
  protected $jiraMasterImportEntity;

  /**
   * The user mapping helper object.
   *
   * @var \ActiveCollabToJiraMigrator\Process\UserMapper
   */
  protected $userMapper;

  /**
   * The settings array from config.php and in parts modified by user input.
   *
   * @var array
   */
  protected $settings = [];

  /**
   * Returns a new MigrationManager instance.
   *
   * @param \ActiveCollab\SDK\Client $acClient
   *   The client object.
   *
   * @return \MigrationManager The MigrationManager instance.
   */
  public static function createInstance(Client $acClient, array $settings) {
    return new self($acClient, $settings);
  }

  /**
   * Constructor.
   *
   * @param \ActiveCollab\SDK\Client $acClient
   */
  protected function __construct(Client $acClient, array $settings) {
    $this->settings = $settings;
    $this->acApiFetcher = AcApiFetcher::createInstance($acClient);
    $this->jiraMasterImportEntity = JiraMasterImportEntity::createInstance();
    $this->userMapper = UserMapper::createInstance($this, $settings);
  }

  /**
   * Helper function to preprocess an entity array of a certain $type.
   *
   * @param string $type
   * @param array $dataRecords
   * @param array $context
   */
  protected static function modifyFetchedRecords($type, array $dataRecords, array $context = []) {
    // TODO - Reactivate or implement in a better way:
    return $dataRecords;

    // Call preprocess function from modifications.php.
    $functionName = 'modify' . $type . 'Fetched';
    if (function_exists($functionName)) {
      foreach ($dataRecords as $key => $record) {
        // Preprocess each record individually.
        $context['type'] = $type;
        $dataRecords[$key] = call_user_func($functionName, $record, $context);
        // Remove the value if the result was explicitly set to boolean false.
        if ($dataRecords[$key] === FALSE) {
          unset($dataRecords[$key]);
        }
      }

    }
    else {
      trigger_error('Expected function "' . $functionName . '" not found in config/modifications.php');
    }
    return $dataRecords;
  }

  /**
   * Maps the given $acUserId to the Jira username by the UserMapper logic.
   * Please ensure that all users used here exist.
   *
   * @param int $acUserId
   *
   * @return string
   *   The mapped Jira username.
   */
  public function mapAcUserIdToJiraUsername(int $acUserId = null) {
    if (!is_numeric($acUserId)) {
      debug('Skipped non-numeric userId in mapAcUserIdToJiraUsername.
      Typically this happens if the original user has been deleted (null) or
      never existed (User id =0): "' . var_export($acUserId, 1) . '"');
      return $this->getUserMapper()->getUsernameFallback();
    }
    return $this->getUserMapper()->mapAcUserIdToJiraUsername($acUserId);
  }

  /**
   * Maps the given $acUserIds array to the Jira usernames by the UserMapper logic.
   * Please ensure that all users used here exist.
   *
   * @param array $acUserIds
   *
   * @return string
   *   The mapped Jira username.
   */
  public function mapAcUserIdsToJiraUsername(array $acUserIds) {
    $usernames = [];
    if (!empty($acUserIds)) {
      foreach ($acUserIds as $acUserId) {
        // ActiveCollab returns an array in some cases, for example for task watchers, instead of id, if the user doesn't exist.
        // We'll never import such users. => Skip!
        if (!is_numeric($acUserId)) {
          debug('Skipped non-numeric userIds in mapAcUserIdsToJiraUsername. Typically this happens if the original user has been deleted or never existed (User id =0): "' . var_export($acUserId, 1) . '"');
          // Skip this entry:
          continue;
        }
        $usernames[] = $this->getUserMapper()->mapAcUserIdToJiraUsername($acUserId);
      }
    }
    return $usernames;
  }

  // Not yet implemented because Jira ServiceDesk doesn't support import.
  // /**
  //  * Migration processor for companies.
  //  */
  // public function processCompanies() {
  //   $fetched = $this->acApiFetcher->fetchCompanies();
  //   $modified = self::modifyFetchedRecords(__FUNCTION__, $fetched);
  // }.

  /**
   * Migration processor for projects.
   *
   * @param int $limit
   * @param int $offset
   */
  public function processUsers(int $limit = NULL, int $offset = NULL) {
    $fetched = $this->acApiFetcher->fetchUsers();

    // Limit results.
    if (!empty($limit) || !empty($offset)) {
      if ($offset === NULL) {
        $offset = 0;
      }
      $fetched = array_slice($fetched, $offset, $limit);
    }

    if (!empty($modified)) {
      foreach ($modified as $userRecord) {
        $userEntity = new UserEntity();
        $this->getJiraMasterImportEntity()->addUser($userEntity);
      }
    }
  }

  /**
   * Migration processor for users.
   *
   * @param int $limit
   * @param int $offset
   */
  public function processProjects(int $limit = NULL, int $offset = NULL) {
    $fetched = $this->acApiFetcher->fetchProjects();
    // Limit results.
    if (!empty($limit) || !empty($offset)) {
      if ($offset === NULL) {
        $offset = 0;
      }
      $fetched = array_slice($fetched, $offset, $limit, true);
    }
    foreach ($fetched as $project) {
      $projectId = $project['id'];
      $projectEntity = $this->processProject($projectId);
      $this->getJiraMasterImportEntity()->addProject($projectEntity);
    }
  }

  /**
   * Returns a list of all projects in the order they would be processed.
   *
   * @return array
   */
  public function getProjectsList(){
    $results = [];
    $fetched = $this->acApiFetcher->fetchProjects();
    foreach ($fetched as $project) {
      $projectId = $project['id'];
      $results[] = [
        'id' => $project['id'],
        'name' => $project['name'],
        'is_completed' => $project['is_completed'] ? 'TRUE' : 'FALSE',
      ];
    }
    return $results;
  }

  /**
   * Migration processor for a given project.
   *
   * @param int $projectId
   *
   * @return \ActiveCollabToJiraMigrator\JiraDataObjects\ProjectEntity
   *   The created project entity.
   */
  protected function processProject(int $projectId) {
    $fetchedProject = $this->acApiFetcher->fetchProject($projectId);
    $projectEntity = ProjectEntity::createInstanceFromAcApiArray($fetchedProject, $this);
    return $projectEntity;
  }

  /**
   * Allows to download an AC attachment through our proxy.
   *
   * @param int $attachmentId
   */
  public function proxyAttachmentAccess(int $attachmentId) {
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
    "is_hidden_from_clients": false
    },
     */
    try {
      $attachment = $this->acApiFetcher->fetchAttachment($attachmentId);
      // Allows to download an AC attachment through our proxy.
      $binary = $this->acApiFetcher->fetchAttachmentDownloadBinary($attachmentId);
      $filename = $attachment['name'];
      header('Content-Description: File Transfer');
      header('Content-Type: ' . $attachment['mime_type']);
      header('Content-Disposition: attachment; filename="' . $filename . '"');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . $attachment['size']);
      header('Content-MD5: ' . $attachment['md5']);
      echo $binary;
      exit(0);
    } catch (\Exception $e) {
      debug('Fetching attachment with ID "' . $attachmentId . '" failed. Error:' . $e->getMessage());
      http_response_code(404);
      echo 'ERROR! Attachment file not found (404) in source system, see log!';
      exit(1);
    }
  }

  /**
   * Runs the export.
   *
   * @param string $jiraExporterClass
   *   The exporter class (must implement JiraExporterInterface)
   * @param array $options
   *   Optional options array, depends on JiraExporterInterface class.
   *
   * @return mixed
   *   Export result based on JiraExporterInterface.
   */
  public function export($jiraExporterClass, array $options = []) {
    $jiraExporterClassReflection = new \ReflectionClass($jiraExporterClass);
    if ($jiraExporterClassReflection->implementsInterface(JiraExporterInterface::class)) {
      return $jiraExporterClass::createInstance($this->getJiraMasterImportEntity())->export($options);
    }
    else {
      throw new \Exception('$jiraExporterClass must be instance of JiraExporterInterface but was "' . gettype($jiraExporterClass) . '"');
    }
  }

  /**
   * Get the JiraMasterImportEntity which we act on.
   *
   * @return \ActiveCollabToJiraMigrator\JiraDataObjects\JiraMasterImportEntity
   */
  public function getJiraMasterImportEntity() {
    return $this->jiraMasterImportEntity;
  }

  /**
   * Get the settings array from config.php and in parts modified by user input.
   *
   * @return array
   */
  public function getSettings() {
    return $this->settings;
  }

  /**
   * Set the settings array from config.php and in parts modified by user input.
   *
   * @param array $settings
   *   The settings array from config.php and in parts modified by user input.
   *
   * @return self
   */
  public function setSettings(array $settings) {
    $this->settings = $settings;

    return $this;
  }

  /**
   * Get acApiFetcher instance.
   *
   * @return \ActiveCollabToJiraMigrator\Fetch\AcApiFetcher
   */
  public function getAcApiFetcher() {
    return $this->acApiFetcher;
  }

  /**
   * Set acApiFetcher instance.
   *
   * @param \ActiveCollabToJiraMigrator\Fetch\AcApiFetcher $acApiFetcher
   *   AcApiFetcher instance.
   *
   * @return self
   */
  public function setAcApiFetcher(AcApiFetcher $acApiFetcher) {
    $this->acApiFetcher = $acApiFetcher;

    return $this;
  }

  /**
   * Get the user mapping helper object.
   *
   * @return \ActiveCollabToJiraMigrator\Process\UserMapper
   */
  public function getUserMapper() {
    return $this->userMapper;
  }

  /**
   * Set the user mapping helper object.
   *
   * @param \ActiveCollabToJiraMigrator\Process\UserMapper $userMapper
   *   The user mapping helper object.
   *
   * @return self
   */
  public function setUserMapper(UserMapper $userMapper) {
    $this->userMapper = $userMapper;

    return $this;
  }
}

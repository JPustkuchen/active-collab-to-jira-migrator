<?php

namespace ActiveCollabToJiraMigrator\Fetch;

use ActiveCollab\SDK\Client;
use ActiveCollab\SDK\ResponseInterface;

/**
 *
 */
class AcApiFetcher {

  /**
   * Cache results and return them from cache after the first fetch.
   *
   * @var bool
   */
  protected $useCache = TRUE;

  /**
   * The internal datacache.
   */
  protected $dataCache = [];

  /**
   * ActiveCollab SDK Client.
   *
   * @var ActiveCollab\SDK\Client
   */
  protected $acClient;

  /**
   * Returns a new AcApi instance.
   *
   * @param \ActiveCollab\SDK\Client $client
   *
   * @return AcApi
   */
  public static function createInstance(Client $acClient, bool $useCache = TRUE) {
    return new self($acClient, $useCache);
  }

  /**
   * Constructor.
   *
   * @param \ActiveCollab\SDK\Client $acClient
   *   The AC SDK Client object.
   * @param bool $useCache
   *   Use cache after first same call?
   */
  protected function __construct(Client $acClient, $useCache = TRUE) {
    $this->acClient = $acClient;
    $this->setUseCache($useCache);
  }

  /**
   * Fetches all companies.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchCompanies() {
    $result = $this->getCachedResult(__FUNCTION__, function () {
      $result = $this->getAllPagesRecords('companies');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all task labels.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchTaskLabels() {
    $result = $this->getCachedResult(__FUNCTION__, function () {
      $result = $this->getAllPagesRecords('labels/task-labels');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all users.
   *
   * Json data example:
   * {
   *     "id": 1,
   *     "class": "Client",
   *     "url_path": "/users/1",
   *     "is_archived": false,
   *     "is_trashed": false,
   *     "trashed_on": null,
   *     "trashed_by_id": 0,
   *     "created_on": 1504607043,
   *     "created_by_id": 0,
   *     "created_by_name": "",
   *     "created_by_email": "",
   *     "updated_on": 1590656459,
   *     "language_id": 4,
   *     "first_name": "Peter",
   *     "last_name": "Mustermann",
   *     "display_name": "pmustermann",
   *     "short_display_name": "pmustermann",
   *     "email": "pm@example.com",
   *     "is_email_at_example": false,
   *     "additional_email_addresses": [],
   *     "daily_capacity": null,
   *     "is_pending_activation": false,
   *     "avatar_url": "https://ac.example.com/proxy.php?proxy=avatar&module=system&v=6.2.135&b=DEV&user_id=120&size=--SIZE--&timestamp=1590656459",
   *     "custom_permissions": [],
   *     "company_id": 7,
   *     "title": null,
   *     "phone": null,
   *     "im_type": null,
   *     "im_handle": null,
   *     "workspace_count": 1,
   *     "first_login_on": null
   *   },
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchUsers() {
    $result = $this->getCachedResult(__FUNCTION__, function () {
      $result = $this->getAllPagesRecords('users/all');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all projects.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjects() {
    $result = $this->getCachedResult(__FUNCTION__, function () {
      $records1 = $this->getAllPagesRecords('projects');
      $records2 = $this->getAllPagesRecords('projects/archive');
      $records = array_merge($records1, $records2);

      $result = [];
      // Order by project id ascending:
      foreach ($records as $record) {
        $result[$record['id']] = $record;
      }
      ksort($result);
      return $result;
    });

    return $result;
  }

  /**
   * Fetche a single project details.
   *
   * @param int $projectId
   *   The project Id.
   *
   * @return array
   *   The fetched project array.
   */
  public function fetchProject(int $projectId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId, function () use ($projectId) {
      $response = $this->acClient->get('projects/' . $projectId);
      $result = $response->getJson();

      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project expenses.
   *
   * @param int $projectId
   *   The project Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectExpenses(int $projectId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId, function () use ($projectId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/expenses');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project time records.
   *
   * @param int $projectId
   *   The project Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTimeRecords(int $projectId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId, function () use ($projectId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/time-records');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project task lists.
   *
   * @param int $projectId
   *   The project Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTaskLists(int $projectId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId, function () use ($projectId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/task-lists');
      return $result;
    });

    return $result;
  }

  public function fetchProjectTaskList(int $projectId, int $taskListId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId . $taskListId, function () use ($projectId, $taskListId) {
      $response = $this->acClient->get('projects/' . $projectId . '/task-lists/' . $taskListId);
      $result = $response->getJson();

      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project notes.
   *
   * @param int $projectId
   *   The project Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectNotes(int $projectId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId, function () use ($projectId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/notes');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project files.
   *
   * @param int $projectId
   *   The project Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectFiles(int $projectId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId, function () use ($projectId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/files');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project tasks.
   *
   * @param int $projectId
   *   The project Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTasks(int $projectId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId, function () use ($projectId) {
      $results = [];
      $openTasks = $this->getAllPagesRecords('projects/' . $projectId . '/tasks');
      // Open tasks are returned as full array with data under key "tasks":
      $openTasks = !empty($openTasks['tasks']) ? $openTasks['tasks'] : [];
      if (!empty($openTasks)) {
        foreach ($openTasks as $openTask) {
          $results[$openTask['id']] = $this->fetchProjectTask($projectId, $openTask['id']);
        }
      }

      // Completed tasks are just returned as ID's:
      $completedTaskIds = !empty($responseTasks['completed_task_ids']) ? $responseTasks['completed_task_ids'] : [];
      if (!empty($completedTaskIds)) {
        foreach ($completedTaskIds as $completedTaskId) {
          $results[$completedTaskId] = $this->fetchProjectTask($projectId, $completedTaskId);
        }
      }

      // Archived tasks are returned as full array directly.
      $archivedTasks = $this->getAllPagesRecords('projects/' . $projectId . '/tasks/archive');
      if (!empty($archivedTasks)) {
        foreach ($archivedTasks as $archivedTask) {
          $results[$archivedTask['id']] = $this->fetchProjectTask($projectId, $archivedTask['id']);
        }
      }

      // Sort by ID ascending:
      ksort($results);

      return $results;
    });

    return $result;
  }

  /**
   *
   */
  public function fetchProjectTask(int $projectId, int $taskId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId . $taskId, function () use ($projectId, $taskId) {
      $responseTask = $this->acClient->get('projects/' . $projectId . '/tasks/' . $taskId);
      $result = $responseTask->getJson();

      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project task comments.
   *
   * @param int $projectId
   *   The project Id.
   * @param int $taskId
   *   The task Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTaskComments(int $projectId, int $taskId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId . $taskId, function () use ($projectId, $taskId) {
      $result = $this->getAllPagesRecords('comments/task/' . $taskId);
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project task expenses.
   *
   * @param int $projectId
   *   The project Id.
   * @param int $taskId
   *   The task Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTaskExpenses(int $projectId, int $taskId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId . $taskId, function () use ($projectId, $taskId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/tasks/' . $taskId . '/expenses');
      return $result;
    });

    return $result;
  }

  /**
   * Fetches all project task time records.
   *
   * @param int $projectId
   *   The project Id.
   * @param int $taskId
   *   The task Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTaskTimeRecords(int $projectId, int $taskId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId . $taskId, function () use ($projectId, $taskId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/tasks/' . $taskId . '/time-records');
      return !empty($result['time_records']) ? $result['time_records'] : [];
    });

    return $result;
  }

  /**
   * Fetches all project task substasks.
   *
   * @param int $projectId
   *   The project Id.
   * @param int $taskId
   *   The task Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTaskSubtasks(int $projectId, int $taskId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId . $taskId, function () use ($projectId, $taskId) {
      $result = $this->getAllPagesRecords('projects/' . $projectId . '/tasks/' . $taskId);
      return $result['subtasks'];
    });

    return $result;
  }

  /**
   * Fetches all project task substasks.
   *
   * @param int $projectId
   *   The project Id.
   * @param int $taskId
   *   The task Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchProjectTaskSubtask(int $projectId, int $taskId, int $subtaskId) {
    $result = $this->getCachedResult(__FUNCTION__ . $projectId . $taskId . $subtaskId, function () use ($projectId, $taskId, $subtaskId) {
      $response = $this->acClient->get('projects/' . $projectId . '/tasks/' . $taskId . '/subtasks/' . $subtaskId);
      $result = $response->getJson();

      return $result['single'];
    });

    return $result;
  }

  /**
   * Fetches an attachment.
   *
   * @param int $projectId
   *   The project Id.
   * @param int $taskId
   *   The task Id.
   *
   * @return array
   *   The fetched records array.
   */
  public function fetchAttachment(int $attachmentId) {
    $result = $this->getCachedResult(__FUNCTION__ . $attachmentId, function () use ($attachmentId) {
      $response = $this->acClient->get('attachments/' . $attachmentId);
      $result = $response->getJson();

      return $result['single'];
    });

    return $result;
  }

  /**
   * Fetches an attachment as binary download.
   *
   * @param int $attachmentId
   *
   * @return blob
   *   The binary file content.
   */
  public function fetchAttachmentDownloadBinary(int $attachmentId) {
    $result = $this->getCachedResult(__FUNCTION__ . $attachmentId, function () use ($attachmentId) {
      $response = $this->acClient->get('attachments/' . $attachmentId . '/download');

      if ($response->getHttpCode() != 200) {
        throw new \Exception("Error fetching attachment binary: HTTP Statuscode != 200. Response:" . $response->getBody());
      }

      // NOT JSON! ;)
      $result = $response->getBody();

      return $result;
    });

    return $result;
  }

  /**
   * Wrapper for $this->acClient->get() which returns all pages, because AC limits results in some cases.
   *
   * Returns results aggregated from all pages.
   *
   * @see https://stackoverflow.com/questions/40020003/get-pagination-results-in-active-collab-api
   *
   * @param string $path
   *
   * @return array
   */
  protected function getAllPagesRecords(string $path) {
    $pageResponse = $this->acClient->get($path);
    $result = $pageResponse->getJson();
    if (!empty($result) && $this->isPaged($pageResponse)) {
      $page = 1;
      while ($page === 1 || !empty($pageResult)) {
        // Call next page until an empty array is returned:
        $page++;
        $pageResult = $this->acClient->get($path . '?page=' . $page)->getJson();
        $result = array_merge($result, $pageResult);
      }
    }
    else {
      // No paging, return the results directly:
      return $result;
    }
    return $result;
  }

  /**
   * Check if a response is paged. Returns true if yes, else false.
   *
   * @param \ActiveCollab\SDK\ResponseInterface $acResponse
   *   The AC Response Object.
   *
   * @return bool
   */
  protected function isPaged(ResponseInterface $acResponse) {
    $headers = $acResponse->getHeaders();

    // @see https://stackoverflow.com/questions/40020003/get-pagination-results-in-active-collab-api
    $paginationCurrentPage = isset($headers['x-angie-paginationcurrentpage'][0]) ? $headers['x-angie-paginationcurrentpage'][0] : NULL;
    $paginationItemsPerPage = isset($headers['x-angie-paginationitemsperpage'][0]) ? $headers['x-angie-paginationitemsperpage'][0] : NULL;
    $paginationTotalItems = isset($headers['x-angie-paginationtotalitems'][0]) ? $headers['x-angie-paginationtotalitems'][0] : NULL;
    if ($paginationCurrentPage === NULL || $paginationItemsPerPage === NULL || $paginationTotalItems === NULL) {
      return FALSE;
    }
    $total_pages = ceil($paginationTotalItems / $paginationItemsPerPage);
    return $total_pages > 1;
  }

  /**
   * Returns the cached result, if one exists with the given $cacheName. Otherwise
   * the $resultElse. $cacheName must be a unique ID.
   *
   * @param string $cacheName
   *   The unique name of the cache. Typically the function name plus identifiers.
   * @param mixed $resultElse
   *   The result to return and cache if no cache entry
   *   exists yet.
   *
   * @return mixed The result data.
   */
  protected function getCachedResult(string $cacheName, callable $resultElseCallback) {
    if (!$this->getUseCache()) {
      // Cache not used. Return the $resultElseCallback result directly.
      return $resultElseCallback();
    }

    // Caching:
    if (isset($this->dataCache[$cacheName])) {
      return $this->dataCache[$cacheName];
    }
    else {
      // Call the result function.
      $resultElse = $resultElseCallback();
      $this->dataCache[$cacheName] = $resultElse;
      return $resultElse;
    }

  }

  /**
   * Clears the whole or a single cache.
   *
   * @param string|null $cacheName
   *   Clear the whole cache (null) or only for a single fetch?
   */
  public function clearCache(string $cacheName = NULL) {
    if (empty($cacheName)) {
      $this->dataCache = [];
    }
    else {
      unset($this->dataCache[$cacheName]);
    }

    return $this;
  }

  /**
   * Get cache results and return them from cache after the first fetch.
   *
   * @return bool
   */
  public function getUseCache() {
    return $this->useCache;
  }

  /**
   * Set cache results and return them from cache after the first fetch.
   *
   * @param bool $useCache
   *   Cache results and return them from cache after the first fetch.
   *
   * @return self
   */
  public function setUseCache(bool $useCache) {
    $this->useCache = $useCache;

    return $this;
  }

  /**
   * Get activeCollab SDK Client.
   *
   * @return ActiveCollab\SDK\Client
   */
  public function getAcClient() {
    return $this->acClient;
  }

  /**
   * Set activeCollab SDK Client.
   *
   * @param ActiveCollab\SDK\Client $acClient
   *   ActiveCollab SDK Client.
   *
   * @return self
   */
  protected function setAcClient(Client $acClient) {
    $this->acClient = $acClient;

    return $this;
  }

}

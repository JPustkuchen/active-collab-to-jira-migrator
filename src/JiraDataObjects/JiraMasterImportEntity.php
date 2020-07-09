<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;

/**
 * Data object for jira root import.
 *
 * Contains the first level elements of the import including their subtrees.
 */
class JiraMasterImportEntity extends JiraImportEntityAbstract implements JiraMasterImportEntityInterface {

  /**
   * The users entities array.
   *
   * @var array
   */
  protected $usersEntities = [];

  /**
   * The links entities array.
   *
   * @var array
   */
  protected $linksEntities = [];

  /**
   * The projects entities array.
   *
   * @var array
   */
  protected $projectsEntities = [];

  /**
   * {@inheritDoc}
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    // TODO - Improve this later?
    throw new \Exception('Not implemented here.');
  }

  /**
   * Returns a new JiraMasterImportEntity instance.
   *
   * @param array $users
   *   UserEntity.
   * @param array $links
   *   LinkEntity.
   * @param array $projects
   *   ProjectEntity.
   *
   * @return JiraMasterImportEntity
   *   The import entity.
   */
  public static function createInstance(array $users = [], array $links = [], array $projects = []) {
    return new self($users, $links, $projects);
  }

  /**
   * Undocumented function.
   *
   * @param array $users
   *   UserEntity.
   * @param array $links
   *   LinkEntity.
   * @param array $projects
   *   ProjectEntity.
   */
  protected function __construct(array $users = [], array $links = [], array $projects = []) {
    if (!empty($users)) {
      foreach ($users as $user) {
        $this->addUser($user);
      }
    }

    if (!empty($links)) {
      foreach ($links as $link) {
        $this->addLink($link);
      }
    }

    if (!empty($projects)) {
      foreach ($projects as $project) {
        $this->addProject($project);
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    $array['users'] = [];
    if (!empty($array['usersEntities'])) {
      $array['users'];
      foreach ($array['usersEntities'] as $entity) {
        $array['users'][] = $entity->toArray();
      }
    }
    unset($array['usersEntities']);

    $array['links'] = [];
    if (!empty($array['linksEntities'])) {
      foreach ($array['linksEntities'] as $entity) {
        $array['links'][] = $entity->toArray();
      }
    }
    unset($array['linksEntities']);

    $array['projects'] = [];
    if (!empty($array['projectsEntities'])) {
      foreach ($array['projectsEntities'] as $entity) {
        $array['projects'][] = $entity->toArray();
      }
    }
    unset($array['projectsEntities']);

    // Process entities to array.
    return $array;
  }

  /**
   * Add user entity.
   *
   * @param UserEntity $userEntity
   *
   * @return JiraMasterImportEntity
   */
  public function addUser(UserEntity $userEntity) {
    $this->usersEntities[] = $userEntity;

    return $this;
  }

  /**
   * Add link entity.
   *
   * @param LinkEntity $linkEntity
   *
   * @return JiraMasterImportEntity
   */
  public function addLink(LinkEntity $linkEntity) {
    $this->linksEntities[] = $linkEntity;

    return $this;
  }

  /**
   * Add project entity.
   *
   * @param ProjectEntity $projectEntity
   */
  public function addProject(ProjectEntity $projectEntity) {
    $this->projectsEntities[] = $projectEntity;

    return $this;
  }

}

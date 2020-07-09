<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;

/**
 * Data object for users.
 */
class UserEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The user login name.
   *
   * @var string
   */
  protected $name;

  /**
   * User assigned group names as array.
   *
   * @var array
   */
  protected $groups = [];

  /**
   * Indicator if the user is active.
   *
   * @var bool
   */
  protected $active;

  /**
   * The users email address.
   *
   * @var string
   */
  protected $email;

  /**
   * The user full name.
   *
   * @var string
   */
  protected $fullname;

  /**
   * {@inheritDoc}
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    /*
    {
    "id": 120,
    "class": "Client",
    "url_path": "/users/120",
    "is_archived": false,
    "is_trashed": false,
    "trashed_on": null,
    "trashed_by_id": 0,
    "created_on": 1504607043,
    "created_by_id": 0,
    "created_by_name": "",
    "created_by_email": "",
    "updated_on": 1590656459,
    "language_id": 4,
    "first_name": "Thuebner",
    "last_name": null,
    "display_name": "thuebner",
    "short_display_name": "thuebner",
    "email": "thuebner@digades.de",
    "is_email_at_example": false,
    "additional_email_addresses": [],
    "daily_capacity": null,
    "is_pending_activation": false,
    "avatar_url": "https://ac.pm.webks.de/proxy.php?proxy=avatar&module=system&v=6.2.135&b=DEV&user_id=120&size=--SIZE--&timestamp=1590656459",
    "custom_permissions": [],
    "company_id": 7,
    "title": null,
    "phone": null,
    "im_type": null,
    "im_handle": null,
    "workspace_count": 1,
    "first_login_on": null
    },
     */
    // TODO - Improve this later?
    if (!empty($array['single'])) {
      throw new \Exception('NON-Detail array with NO "single" key at first level expected. Instead keys were: "' . implode(', ', array_keys($array)) . '"');
    }

    if (!empty($array['is_trashed']) || !empty($array['is_archived'])) {
      // Do not create if trashed.
      return FALSE;
    }

    $settings = $migrationManager->getSettings();
    $name = $migrationManager->mapAcUserIdToJiraUsername($array['id']);

    // We do not migrate groups, but allow modifications to set them.
    if (!empty($array['_groups'])) {
      // Use custom assigned groups:
      $groups = $array['_groups'];
    }
    else {
      // Get from settings:
      if (!is_array($settings['default_user_groups'])) {
        throw new \Exception('Setting "default_user_groups" is missing.');
      }
      $groups = $settings['default_user_groups'];
    }
    $active = TRUE;
    $email = $array['email'];
    $fullname = $array['first_name'] . ' ' . $array['last_name'];

    return new self($name, $groups, $active, $email, $fullname);
  }

  /**
   *
   */
  protected function __construct(string $name, array $groups, bool $active, string $email, string $fullname) {
    $this->name = $name;
    $this->groups = $groups;
    $this->active = $active;
    $this->email = $email;
    $this->fullname = $fullname;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    {
    "name" : "peter-mustermann",
    "groups" : [ "jira-users", "my-custom-group" ],
    "active" : true,
    "email" : "user1@example.com",
    "fullname" : "User 1"
    }
     */
    // Nothing to postprocess here.
    return $array;
  }

}

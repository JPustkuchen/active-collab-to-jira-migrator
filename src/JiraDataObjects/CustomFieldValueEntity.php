<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

use ActiveCollabToJiraMigrator\Process\MigrationManager;

/**
 * Data object for custom field values.
 */
class CustomFieldValueEntity extends JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * The human readable field name. Example: 'Story Points'.
   *
   * @required
   * @var string
   */
  protected $fieldName;

  /**
   * The field type.
   *
   * Example: "com.atlassian.jira.plugin.system.customfieldtypes:float".
   *
   * @required
   * @var string
   */
  protected $fieldType;

  /**
   * The field value. Example: '15'.
   *
   * @required
   * @var string
   */
  protected $value;

  /**
   * {@inheritDoc}
   *
   * @return CustomFieldValueEntity
   *
   * @throws Exception
   */
  public static function createInstanceFromAcApiArray(array $array, MigrationManager $migrationManager) {
    throw new \Exception('createInstanceFromAcApiArray not supported. AC does not know this type. createInstance for indirect use.');
  }

  /**
   * Returns a new instance of CustomFieldValueEntity.
   *
   * @param string $fieldName
   * @param string $fieldType
   * @param mixed $value
   *
   * @return CustomFieldValueEntity
   */
  public static function createInstance(string $fieldName, string $fieldType, $value) {
    return new self($fieldName, $fieldType, $value);
  }

  /**
   * Constructor.
   *
   * @param string $fieldName
   *   The human readable field name. Example: 'Story Points'.
   * @param string $fieldType
   *   The field type.
   * @param string $value
   *   The field value. Example: '15'.
   */
  protected function __construct(string $fieldName, string $fieldType, $value) {
    $this->fieldName = $fieldName;
    $this->fieldType = $fieldType;
    $this->value = $value;
  }

  /**
   * {@inheritDoc}
   */
  protected function postprocessToArray(array $array) {
    /*
    "customFieldValues": [
    {
    "fieldName": "Sprint",
    "fieldType": "com.pyxis.greenhopper.jira:gh-sprint",
    "value": [
    {
    "rapidViewId": 30,
    "state": CLOSED",
    "startDate": "2018-01-01",
    "endDate": "2018-01-01",
    "completeDate": "2018-01-01",
    "name": "New Sprint"
    }
    ]
    }
     */
    // No changes required.
    return $array;
  }

  /**
   * Get the human readable field name. Example: 'Story Points'.
   *
   * @return string
   */
  public function getFieldName() {
    return $this->fieldName;
  }

  /**
   * Set the human readable field name. Example: 'Story Points'.
   *
   * @param string $fieldName
   *   The human readable field name. Example: 'Story Points'.
   *
   * @return self
   */
  public function setFieldName(string $fieldName) {
    $this->fieldName = $fieldName;

    return $this;
  }

  /**
   * Get example: "com.atlassian.jira.plugin.system.customfieldtypes:float".
   *
   * @return string
   */
  public function getFieldType() {
    return $this->fieldType;
  }

  /**
   * Set example: "com.atlassian.jira.plugin.system.customfieldtypes:float".
   *
   * @param string $fieldType
   *   Example: "com.atlassian.jira.plugin.system.customfieldtypes:float".
   *
   * @return self
   */
  public function setFieldType(string $fieldType) {
    $this->fieldType = $fieldType;

    return $this;
  }

  /**
   * Get the field value. Example: '15'.
   *
   * @return string
   */
  public function getValue() {
    return $this->value;
  }

  /**
   * Set the field value. Example: '15'.
   *
   * @param string $value
   *   The field value. Example: '15'.
   *
   * @return self
   */
  public function setValue(string $value) {
    $this->value = $value;

    return $this;
  }

}

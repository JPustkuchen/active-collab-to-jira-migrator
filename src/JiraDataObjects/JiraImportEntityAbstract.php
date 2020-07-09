<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

/**
 * Abstract logic for Jira import entities.
 */
abstract class JiraImportEntityAbstract implements JiraImportEntityInterface {

  /**
   * Postprocess the default generated array representation from get_object_vars($this).
   *
   * @param array $array
   *   My default array representation.
   *
   * @return array
   *   The postprocessed array.
   */
  abstract protected function postprocessToArray(array $array);

  /**
   * {@inheritdoc}
   */
  final public function toArray() {
    $array = get_object_vars($this);
    $array = $this->postprocessToArray($array);
    // We erase all entries with null values completely to prevent wrong handling in Jira:
    foreach ($array as $key => $value) {
      if ($value === NULL) {
        // No value has been set. Remove the value.
        unset($array[$key]);
      }
    }
    return $array;
  }

  /**
   * {@inheritdoc}
   */
  public function toJson(int $options = 0) {
    return json_encode($this->toArray(), $options);
  }

}

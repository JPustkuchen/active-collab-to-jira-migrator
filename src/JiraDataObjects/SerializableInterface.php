<?php

namespace ActiveCollabToJiraMigrator\JiraDataObjects;

/**
 * Represents a serializable object.
 */
interface SerializableInterface {

  /**
   * Returns the import data structure as array.
   *
   * @return array
   *   Array representation.
   */
  public function toArray();

  /**
   * Returns the JSON representation.
   *
   * @param int $options Bitwise!
   *   See https://www.php.net/manual/de/function.json-encode.php.
   *
   * @return string The JSON representation.
   */
  public function toJson(int $options = 0);

}

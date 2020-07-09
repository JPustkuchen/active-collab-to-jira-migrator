<?php

namespace ActiveCollabToJiraMigrator\Export;

use JsonSchema\Validator;

/**
 * Handles export to Jira JSON.
 */
class JiraJsonExporter extends JiraExporterAbstract implements JiraExporterInterface {

  /**
   * Validates the json export agains the schema.
   *
   * Returns boolean true, if valid,
   *
   * @param string $json
   *   The JSON string.
   * @param string $schemaPath
   *   The relative schema path.
   *
   * @return bool|array
   */
  protected static function validateJson(string $json, string $schemaPath) {
    // String value found, but an object is required, see:
    // https://github.com/justinrainbow/json-schema/issues/203:
    $json = json_decode($json);

    // Validate.
    $validator = new Validator();
    $validator->validate($json, (object) ['$ref' => 'file://' . realpath($schemaPath)]);

    if ($validator->isValid()) {
      return TRUE;
    }
    else {
      $errors = [];
      foreach ($validator->getErrors() as $error) {
        $errors[] = sprintf("[%s] %s\n", $error['property'], $error['message']);
      }
      return $errors;
    }
  }

  /**
   * {@inheritDoc}
   */
  public function export(array $options = []) {
    return $this->toJson($options);
  }

  /**
   * Returns the JSON string representation.
   */
  protected function toJson(array $options = []) {
    $jsonOptions = 0;
    if (!empty($options['jsonOptions'])) {
      $jsonOptions = $jsonOptions | $options['jsonOptions'];
    }

    // Set general important options:
    $jsonOptions = $jsonOptions | JSON_THROW_ON_ERROR;

    // Pretty print:
    if (!empty($options['prettyPrint'])) {
      $jsonOptions = $jsonOptions | JSON_PRETTY_PRINT;
    }

    $output = $this->getJiraMasterImportEntity()->toJson($jsonOptions);

    if (!empty($options['validateJson'])) {
      $jsonValidatorErrors = self::validateJson($output, $options['validateJsonSchemaPath']);
      debug('JSON Schema Validation errors:');
      if (!empty($jsonValidatorErrors)) {
        // Print errors to debug:
        debug($jsonValidatorErrors);
      } else {
        debug('NO JSON Schema Validation errors! :)');
      }
    }

    return $output;
  }

  /**
   * Helper function to postprocess an entity array of a certain $type.
   *
   * @param string $type
   *   The entity type string, e.g. "company".
   * @param array $dataRecords
   *   The array of entities of this type.
   * @param array $context
   *   Additional context information.
   */
  protected static function modifyExportEntities(string $type, array $dataRecords, array $context = []) {
    // Call preprocess function from modifications.php.
    $functionName = 'modify' . $type . 'Export';
    if (function_exists($functionName)) {
      foreach ($dataRecords as $key => $data) {
        // Preprocess each record individually.
        $context['type'] = $type;
        $dataRecords[$key] = call_user_func($functionName, $data, $context);
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

}

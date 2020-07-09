<?php

/**
 * @file
 * Helper file to allow individual modifications to mappings and behaviour.
 */

/*
How to use modifications
- return boolean false to remove the record.
- return $data unchanged to make no change (default)
 */

/**
 * ------ *user* -------.
 */
function preprocess_user(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_user(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *attachment* -------.
 */
function preprocess_attachment(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_attachment(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *customFieldValue* -------.
 */
function preprocess_customFieldValue(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_customFieldValue(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *issue* -------.
 */
function preprocess_issue(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_issue(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *issueSubtask* -------.
 */
function preprocess_issueSubtask(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_issueSubtask(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *link* -------.
 */
function preprocess_link(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_link(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *project* -------.
 */
function preprocess_project(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_project(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *version* -------.
 */
function preprocess_version(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_version(array $data, array $context = []) {
  return $data;
}

/**
 * ------ *worklog* -------.
 */
function preprocess_worklog(array $data, array $context = []) {
  return $data;
}

/**
 *
 */
function postprocess_worklog(array $data, array $context = []) {
  return $data;
}

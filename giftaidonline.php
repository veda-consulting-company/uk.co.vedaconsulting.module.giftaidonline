<?php

require_once 'giftaidonline.civix.php';

/**
 * Implementation of hook_civicrm_config
 */
define('GIFTAID_FAILURE_REPORT_ID', 'uk.co.vedaconsulting.module.giftaidonline/giftaidonlinefailure');
function giftaidonline_civicrm_config(&$config) {
  _giftaidonline_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function giftaidonline_civicrm_xmlMenu(&$files) {
  _giftaidonline_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 */
function giftaidonline_civicrm_install() {
  return _giftaidonline_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function giftaidonline_civicrm_uninstall() {
  return _giftaidonline_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 */
function giftaidonline_civicrm_enable() {
  return _giftaidonline_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 */
function giftaidonline_civicrm_disable() {
  return _giftaidonline_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function giftaidonline_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _giftaidonline_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 */
function giftaidonline_civicrm_managed(&$entities) {
  return _giftaidonline_civix_civicrm_managed($entities);
}

/**
 *Implementation of hook_civicrm_navigationMenu
 * @param array $params
 */
function giftaidonline_civicrm_navigationMenu( &$params ) {
  return _giftaidonline_civicrm_navigationMenu( $params );
}

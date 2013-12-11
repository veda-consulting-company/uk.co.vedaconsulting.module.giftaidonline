<?php

// AUTO-GENERATED FILE -- Civix may overwrite any changes made to this file

function _giftaidonline_db_setup( $bIsUpgrading ) {
    $sDbScriptsDir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'scripts' .  DIRECTORY_SEPARATOR . 'sql' .  DIRECTORY_SEPARATOR; 
    if ( !$bIsUpgrading ) {
      CRM_Utils_File::sourceSQLFile(CIVICRM_DSN, sprintf( "%scivi_gift_aid_submission.sql", $sDbScriptsDir ) );
      CRM_Utils_File::sourceSQLFile(CIVICRM_DSN, sprintf( "%scivi_gift_aid_submission_setting.sql", $sDbScriptsDir ) );
      CRM_Utils_File::sourceSQLFile(CIVICRM_DSN, sprintf( "%scivi_gift_aid_submission_setting_default_data.sql", $sDbScriptsDir ) );      
      CRM_Utils_File::sourceSQLFile(CIVICRM_DSN, sprintf( "%civi_gift_aid_polling_request.sql", $sDbScriptsDir ) );      
    } 
}
/**
 * (Delegated) Implementation of hook_civicrm_config
 */
function _giftaidonline_civix_civicrm_config(&$config = NULL) {
  static $configured = FALSE;
  if ($configured) return;
  $configured = TRUE;

  $template =& CRM_Core_Smarty::singleton();

  $extRoot = dirname( __FILE__ ) . DIRECTORY_SEPARATOR;
  $extDir = $extRoot . 'templates';

  if ( is_array( $template->template_dir ) ) {
      array_unshift( $template->template_dir, $extDir );
  } else {
      $template->template_dir = array( $extDir, $template->template_dir );
  }

  $include_path = $extRoot . PATH_SEPARATOR . get_include_path( );
  set_include_path( $include_path );
}

/**
 * (Delegated) Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 */
function _giftaidonline_civix_civicrm_xmlMenu(&$files) {
  foreach (glob(__DIR__ . '/xml/Menu/*.xml') as $file) {
    $files[] = $file;
  }
}

/**
 * Implementation of hook_civicrm_install
 */
function _giftaidonline_civix_civicrm_install() {
  _giftaidonline_civix_civicrm_config();
  if ($upgrader = _giftaidonline_civix_upgrader()) {
    _giftaidonline_db_setup( true );
    return $upgrader->onInstall();
  }
  _giftaidonline_db_setup( false );
}

/**
 * Implementation of hook_civicrm_uninstall
 */
function _giftaidonline_civix_civicrm_uninstall() {
  _giftaidonline_civix_civicrm_config();
  if ($upgrader = _giftaidonline_civix_upgrader()) {
    return $upgrader->onUninstall();
  }
}

/**
 * (Delegated) Implementation of hook_civicrm_enable
 */
function _giftaidonline_civix_civicrm_enable() {
  _giftaidonline_civix_civicrm_config();
  if ($upgrader = _giftaidonline_civix_upgrader()) {
    if (is_callable(array($upgrader, 'onEnable'))) {
      return $upgrader->onEnable();
    }
  }
}

/**
 * (Delegated) Implementation of hook_civicrm_disable
 */
function _giftaidonline_civix_civicrm_disable() {
  _giftaidonline_civix_civicrm_config();
  if ($upgrader = _giftaidonline_civix_upgrader()) {
    if (is_callable(array($upgrader, 'onDisable'))) {
      return $upgrader->onDisable();
    }
  }
}

/**
 * (Delegated) Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 */
function _giftaidonline_civix_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  if ($upgrader = _giftaidonline_civix_upgrader()) {
    return $upgrader->onUpgrade($op, $queue);
  }
}

function _giftaidonline_civix_upgrader() {
  if (!file_exists(__DIR__.'/CRM/Giftaidonline/Upgrader.php')) {
    return NULL;
  } else {
    return CRM_Giftaidonline_Upgrader_Base::instance();
  }
}

/**
 * Search directory tree for files which match a glob pattern
 *
 * @param $dir string, base dir
 * @param $pattern string, glob pattern, eg "*.txt"
 * @return array(string)
 */
function _giftaidonline_civix_find_files($dir, $pattern) {
  $todos = array($dir);
  $result = array();
  while (!empty($todos)) {
    $subdir = array_shift($todos);
    foreach (glob("$subdir/$pattern") as $match) {
      if (!is_dir($match)) {
        $result[] = $match;
      }
    }
    if ($dh = opendir($subdir)) {
      while (FALSE !== ($entry = readdir($dh))) {
        $path = $subdir . DIRECTORY_SEPARATOR . $entry;
        if ($entry == '.' || $entry == '..') {
        } elseif (is_dir($path)) {
          $todos[] = $path;
        }
      }
      closedir($dh);
    }
  }
  return $result;
}
/**
 * (Delegated) Implementation of hook_civicrm_managed
 *
 * Find any *.mgd.php files, merge their content, and return.
 */
function _giftaidonline_civix_civicrm_managed(&$entities) {
  $mgdFiles = _giftaidonline_civix_find_files(__DIR__, '*.mgd.php');
  foreach ($mgdFiles as $file) {
    $es = include $file;
    foreach ($es as $e) {
      if (empty($e['module'])) {
        $e['module'] = 'uk.co.vedaconsulting.module.giftaidonline';
      }
      $entities[] = $e;
    }
  }
}

/**
 * Add a Online Gift Aid Submission to the end of the Contributions Menu.
 * @param array $params
 */
function _giftaidonline_civicrm_navigationMenu( &$params ) {
  $aMenus             = array_values( $params );
  $aContributionsMenu = array();
  $aAdministerMenu    = array();
  foreach ( $aMenus as $aMenu ) {
    if ( $aMenu['attributes'][ 'name' ] == 'Administer' ) {
      $aAdministerMenu = $aMenu;
    } else {
      if ( $aMenu['attributes'][ 'name' ] == 'Contributions' ) {
        $aContributionsMenu = $aMenu;
      }
    }
  }
  $iContributionMenuId     = $aContributionsMenu['attributes'][ 'navID' ];
  $aContributionChildMenus = $aContributionsMenu['child'];
  $iLastChildKey           = max( array_keys( $aContributionChildMenus ) );
  $aGiftAidOnlineMenu      = array ( 'attributes' => array ( 'label'      => 'Online Gift Aid Submission'
                                                           , 'name'       => 'CRM_Giftaidonline_Page_OnlineSubmission'
                                                           , 'url'        => 'civicrm/onlinesubmission'
                                                           , 'permission' => 'access CiviContribute,administer CiviCRM'
                                                           , 'operator'   => 'AND'
                                                           , 'separator'  => 1
                                                           , 'parentID'   => $iContributionMenuId
                                                           , 'navID'      => $iLastChildKey + 1
                                                           , 'active'     => 1
                                                           )
                                   , 'child' => null
                                   );

  // Add a Separator line before our Menu.
  $aContributionChildMenus[$iLastChildKey]['attributes']['separator'] = 1;

  $aContributionChildMenus[] = $aGiftAidOnlineMenu;
  $params[$iContributionMenuId]['child'] = $aContributionChildMenus;
  
  $iAdministerMenuId         = $aAdministerMenu['attributes'][ 'navID' ];
  $aAdministerChildMenus     = $aAdministerMenu['child'];
  $iLastChildKey             = max( array_keys( $aAdministerChildMenus ) );
  $aGiftAidOnlineSettingMenu = array ( 'attributes' => array ( 'label'      => 'Online Gift Aid Submission'
                                                             , 'name'       => 'CRM_Giftaidonline_Page_giftAidSubmissionSettings'
                                                             , 'url'        => 'civicrm/gift-aid-submission-settings'
                                                             , 'permission' => 'access CiviContribute,administer CiviCRM'
                                                             , 'operator'   => 'AND'
                                                             , 'separator'  => 1
                                                             , 'parentID'   => $iAdministerMenuId
                                                             , 'navID'      => $iLastChildKey + 1
                                                             , 'active'     => 1
                                                             )
                                     , 'child' => null
                                     );

  // Add a Separator line before our Menu.
  $aAdministerChildMenus[$iLastChildKey]['attributes']['separator'] = 1;

  $aAdministerChildMenus[] = $aGiftAidOnlineSettingMenu;
  $params[$iAdministerMenuId]['child'] = $aAdministerChildMenus;
  
}

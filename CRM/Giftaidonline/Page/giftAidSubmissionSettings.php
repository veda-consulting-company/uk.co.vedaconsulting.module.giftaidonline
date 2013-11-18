<?php
require_once 'CRM/Core/Page.php';

class CRM_Giftaidonline_Page_giftAidSubmissionSettings extends CRM_Core_Page {

  private function get_setting( $pName ) {
    $sSql =<<<SQL
            SELECT id
            ,      name
            ,      value
            ,      description
            FROM   civicrm_gift_aid_submission_setting
            WHERE  name = %1
            LIMIT  1
SQL;
    $aSettings = array();
    $aParams   = array( 1 => array( $pName, 'String' ) );
    $oDao      = CRM_Core_DAO::executeQuery( $sSql, $aParams );

    if ( is_a( $oDao, 'DB_Error' ) ) {
      CRM_Core_Error::fatal();
    }
    if ( $oDao->fetch() ) {
      $aSettings['id']          = $oDao->id;
      $aSettings['name']        = $oDao->name;
      $aSettings['value']       = $oDao->value;
      $aSettings['description'] = $oDao->description;
    }

    return $aSettings;
  }

  function get_contribution_details_source() {
    $aSetting = $this->get_setting( 'CONTRIBUTION_DETAILS_SOURCE' );
    if ( empty( $aSetting ) ) {
      return 'DECLARATION';
    } else {
      return $aSetting['value'];
    }
  }

  function get_all_gift_aid_submission_setting (){
      $settings_query    =   "SELECT * FROM civicrm_gift_aid_submission_setting";
      $settings_Dao     = CRM_Core_DAO::executeQuery( $settings_query );

      while ( $settings_Dao->fetch() ) {

          $gift_aid_settings[] = array ( 'id'               => $settings_Dao->id
                                       , 'name'             => $settings_Dao->name
                                       , 'value'            => $settings_Dao->value
                                       , 'description'      => $settings_Dao->description
                                       );
      }

      $this->assign('gift_aid_settings', $gift_aid_settings);
  }

  function update_gift_aid_submission_setting ( $id, $name, $value ) {
      $settings_query    =   " UPDATE civicrm_gift_aid_submission_setting
                                SET value        = '".$value."'

                               WHERE id =  ".$id."
                                   AND name = '".$name."'
                             ";

      $settings_update_Dao     = CRM_Core_DAO::executeQuery( $settings_query );


  }

 function run() {
    CRM_Utils_System::setTitle(ts('Gift Aid Submission Settings'));
    self::get_all_gift_aid_submission_setting();
    //self::update_gift_aid_submission_setting( $id, $name, $value, $description );

    parent::run();
  }


}
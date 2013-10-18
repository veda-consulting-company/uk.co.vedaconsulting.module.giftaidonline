<?php
require_once 'CRM/Core/Page.php';

class CRM_Giftaidonline_Page_giftAidSubmissionSettings extends CRM_Core_Page {

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
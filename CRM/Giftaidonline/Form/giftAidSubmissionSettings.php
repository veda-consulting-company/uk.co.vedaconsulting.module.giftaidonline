<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';
require_once 'CRM/Core/OptionGroup.php';
require_once 'CRM/Core/SelectValues.php';

class CRM_Giftaidonline_Form_giftAidSubmissionSettings extends CRM_Core_Form
{
    function buildQuickForm() {
        global $settings_id ;
        global $settings_name;
        if(isset($_GET['sid'])) {
            $settings_id    = $_GET['sid']; 
        }
        if(isset($_GET['sname'])) {
            $settings_name  = $_GET['sname'];
        }
        $this->assign( 'settings_id'    ,   $settings_id    );
        $this->assign( 'settings_name'  ,   $settings_name  );
        $buttons = array(
            array(
              'type' => 'upload',
              'name' => ts('Save'),
              'isDefault' => TRUE,
            ),
            array(
              'type' => 'cancel',
              'name' => ts('Cancel'),
            ),
          );
        $this->addButtons($buttons);
    }
  
    function postProcess() {
        $session = CRM_Core_Session::singleton();
        $buttonName = $this->controller->getButtonName();
        $value  =   $_POST['value'];
        $id     =   $_POST['id'];
        $name   =   $_POST['name'];
        if ($buttonName ==  $this->getButtonName('upload')) {

           CRM_Giftaidonline_Page_giftAidSubmissionSettings:: update_gift_aid_submission_setting( $id,  $name, $value );

        }
         $session->replaceUserContext(CRM_Utils_System::url('civicrm/gift-aid-submission-settings'));
    }
}   
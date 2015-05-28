<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.5                                               |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2013
 * $Id$
 *
 */
class CRM_Giftaidonline_Utils_Submission {
  /*
   * Function to return the array of submission id & date
   */
  static function getSubmissionIdTitle( $orderBy = 'id' ){
    $query = "SELECT * FROM civicrm_gift_aid_submission ORDER BY " . $orderBy;
    $dao   =& CRM_Core_DAO::executeQuery( $query);

    $result = array();
    while ( $dao->fetch( ) ) {
        $result[$dao->id] = $dao->id." - ".$dao->created_date;
    }
    return $result;
  }
}

<?php 

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
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
 * @package CiviCRM_Hook
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id: $
 *
 */

abstract class Giftaidonline_Utils_Hook {

	static $_nullObject = null;

    /**
     * We only need one instance of this object. So we use the singleton
     * pattern and cache the instance in this variable
     *
     * @var object
     * @static
     */
    static private $_singleton = null;

    
    /**
     * Constructor and getter for the singleton instance
     * @return instance of $config->userHookClass
     */
    static function singleton( ) {
        if (self::$_singleton == null) {
            $config = CRM_Core_Config::singleton( );
            $class = $config->userHookClass;
            require_once( str_replace( '_', DIRECTORY_SEPARATOR, $config->userHookClass ) . '.php' );
            self::$_singleton = new $class();
        }
        return self::$_singleton;
    }
    
    abstract function invoke( $numParams,
                              &$arg1, &$arg2, &$arg3, &$arg4, &$arg5,
                              $fnSuffix );

    /**
     * This hook allows filtering contributions for gift-aid
     * @param bool    $isEligible eligibilty already detected if getDeclaration() method.
     * @param integer $contactID  contact being checked 
     * @param date    $date  date gift-aid declaration was made on 
     * @param $contributionID  contribution id if any being referred  
     *
     * @access public
     */
    static function giftAidOnlineSubmitted( $batchID ) {
		return self::singleton( )->invoke( 1, $batchID, self::$_nullObject, 'civicrm_giftAidOnlineSubmitted' );       
    }

    /**
     * This hook allows doing any extra processing for contributions that were removed due to not meeting the gift aid online rules
     * @param $contributionsRemoved  contribution ids that have been removed from batch
     *
     * @access public
     */
    static function invalidGiftAidOnlineContribution( $batchID, $contributionIDRemoved ) {
		return self::singleton( )->invoke( 2, $batchID, $contributionIDRemoved, self::$_nullObject, self::$_nullObject, self::$_nullObject, 'civicrm_invalidGiftAidOnlineContribution' );
    }
    
}


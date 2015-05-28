<?php

class CRM_Giftaidonline_Form_Report_giftaidonlinefailure extends CRM_Report_Form {

    protected $_addressField  = FALSE;

    protected $_emailField    = FALSE;

    protected $_summary       = NULL;

    protected $_customGroupExtends = array('Membership');
    protected $_customGroupGroupBy = FALSE; 
    function __construct() {
      
        if(isset($_GET['batch_id'])){
            $this->_columns = array(
                'civicrm_contact' => array(
                    'dao' => 'CRM_Contact_DAO_Contact',
                        'fields' => array(
                            'id' => array(
                                'name' => 'id',
                                'title' => 'Contact Id',
                                'required'    => TRUE,
                            ),
                            'sort_name' => array(
                                'title'       => ts('Contact Name'),
                                'required'    => TRUE,
                                'default'     => TRUE,
                                'no_repeat'   => TRUE,
                            ),
                            'first_name' => array(
                                'title'       => ts('First Name'),
                                'no_repeat'   => TRUE,
                            ),
                            'id' => array(
                                'no_display'  => TRUE,
                                'required'    => TRUE,
                            ),
                            'last_name' => array(
                                'title'       => ts('Last Name'),
                                'no_repeat'   => TRUE,
                            ),
                        ),
                ),
                'civicrm_gift_aid_rejected_contributions' => array(
                    'fields' => array(
                        'rejection_reason' => array(
                            'name'            => 'rejection_reason',
                            'title'           => 'Rejection Reason',
                            'required' => TRUE,
                                'default'     => TRUE,
                                'no_repeat'   => TRUE,
                        ),
                        'id' => array(
                            'name'            => 'batch_id',
                            'title'           => 'Batch Id',
                            'required'        => TRUE,
                            'default'         => TRUE,
                            'no_repeat'       => TRUE,
                        ),
                       
                    ),
                    'filters'   => array(
                        'submission_id'    => array(
                            'title'           => 'Submission',
                            'operatorType'    => CRM_Report_Form::OP_MULTISELECT,
                            'options'         => CRM_Giftaidonline_Utils_Submission::getSubmissionIdTitle( 'id desc' ),
                            'default'         => array($_GET['submissionId']),
                            //'default'       => array(1),
                          ),
                    ),
                ),
                'civicrm_batch' => array(
                    'dao'       => 'CRM_Batch_DAO_Batch',
                    'filters'   => array(
                        'id'    => array(
                            'title'           => 'Batch Name',
                            'operatorType'    => CRM_Report_Form::OP_MULTISELECT,
                            'options'         => CRM_Civigiftaid_Utils_Contribution::getBatchIdTitle( 'id desc' ),
                            'default'         => array($_GET['batch_id']),
                            //'default'       => array(1),
                          ),
                    ),
                ),
                'civicrm_contribution'  => array(
                    'dao'      => 'CRM_Contribute_DAO_Contribution',
                    'fields'   => array(
                        'id'   => array(
                            'name'       => 'id',
                            'title'      => 'Contribution ID',
                            'no_display' => FALSE,
                            'default'    => TRUE,
                        ),
                        'total_amount'   => array(
                            'title'      => ts('Total Amount'),
                            'default'    => TRUE,
                            'no_display' => FALSE,
                            'statistics' => array(
                            'sum'        => ts('Total Amount'),
                            ),
                          ),
                    ),
                  'grouping' => 'contri-fields',
                    'filters' => array(
                      'total_sum' => array(
                            'title'     => ts('Total Amount'),
                            'type'      => CRM_Report_Form::OP_INT,
                            'dbAlias'   => 'civicrm_contribution_total_amount_sum',
                            //'having'  => TRUE,
                        ),
                      
                    ),
                ),     
            );
        }else{
          $this->_columns = array(
                'civicrm_contact' => array(
                    'dao' => 'CRM_Contact_DAO_Contact',
                        'fields' => array(
                            'id' => array(
                                'name' => 'id',
                                'title' => 'Contact Id',
                                'required'    => TRUE,
                            ),
                            'sort_name' => array(
                                'title'       => ts('Contact Name'),
                                'required'    => TRUE,
                                'default'     => TRUE,
                                'no_repeat'   => TRUE,
                            ),
                            'first_name' => array(
                                'title'       => ts('First Name'),
                                'no_repeat'   => TRUE,
                            ),
                            'id' => array(
                                'no_display'  => TRUE,
                                'required'    => TRUE,
                            ),
                            'last_name' => array(
                                'title'       => ts('Last Name'),
                                'no_repeat'   => TRUE,
                            ),
                        ),
                ),
                'civicrm_gift_aid_rejected_contributions' => array(
                    'fields' => array(
                        'rejection_reason' => array(
                            'name'     => 'rejection_reason',
                            'title'    => 'Rejection Reason',
                            'required' => TRUE,
                            'default'  => TRUE,
                            'no_repeat'=> TRUE,
                        ),
                        'batch_id' => array(
                            'name'     => 'batch_id',
                            'title'    => 'Batch Id',
                            'required' => TRUE,
                            'default'  => TRUE,
                            'no_repeat'=> TRUE,
                        ),
                        'contribution_id' => array(
                            'name'     => 'contribution_id',
                            'title'    => 'Contribution Id',
                            'required' => TRUE,
                            'default'  => TRUE,
                            'no_repeat'=> FALSE,
                        ),
                    ),
                    'filters'   => array(
                        'submission_id'    => array(
                            'title'           => 'Submission',
                            'operatorType'    => CRM_Report_Form::OP_MULTISELECT,
                            'options'         => CRM_Giftaidonline_Utils_Submission::getSubmissionIdTitle( 'id desc' ),
                            'default'         => array($_GET['submissionId']),
                            //'default'       => array(1),
                          ),
                    ),
                ),
                'civicrm_batch' => array(
                    'dao'       => 'CRM_Batch_DAO_Batch',
                    'filters'   => array(
                        'id'    => array(
                            'title'           => 'Batch Name',
                            'operatorType'    => CRM_Report_Form::OP_MULTISELECT,
                            'options'         => CRM_Civigiftaid_Utils_Contribution::getBatchIdTitle( 'id desc' ),
                            //'default'       => array(1),
                          ),
                    ),
                ),
                'civicrm_contribution'  => array(
                    'dao'      => 'CRM_Contribute_DAO_Contribution',
                    'fields'   => array(
                        'contribution_id'   => array(
                            'name'       => 'id',
                            'title'      => 'Contribution ID',
                            'no_display' => FALSE,
                        ),
                        'total_amount'   => array(
                            'title'      => ts('Total Amount'),
                            'default'    => TRUE,
                            'no_display' => FALSE,
                            'statistics' => array(
                            'sum'        => ts('Total Amount'),
                            ),
                          ),
                    ),
                ),      
            );
        }
        //$this->_groupFilter = TRUE;
        //$this->_tagFilter = TRUE;
        parent::__construct();
    }

    function preProcess() {
        $this->assign('reportTitle', ts('Gift Aid Online Failure'));
    parent::preProcess();
    }

    function select() {
        $select = $this->_columnHeaders = array();
        foreach ($this->_columns as $tableName => $table) {
            if (array_key_exists('fields', $table)) {
                foreach ($table['fields'] as $fieldName => $field) {
                    if (CRM_Utils_Array::value('required', $field) ||
                      CRM_Utils_Array::value($fieldName, $this->_params['fields'])
                    ) {
                        // only include statistics columns if set
                        if ( CRM_Utils_Array::value('statistics', $field) ) {
                            foreach ( $field['statistics'] as $stat => $label ) {
                                switch (strtolower($stat)) {
                                case 'sum':
                                    $select[] = "SUM({$field['dbAlias']}) as {$tableName}_{$fieldName}_{$stat}";
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['title'] = $label;
                                    $this->_columnHeaders["{$tableName}_{$fieldName}_{$stat}"]['type']  = $field['type'];
                                    $this->_statFields[] = "{$tableName}_{$fieldName}_{$stat}";
                                    break;
                                }
                            }
                        }else{
                            $select[] = "{$field['dbAlias']} as {$tableName}_{$fieldName}";
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['title'] = $field['title'];
                            $this->_columnHeaders["{$tableName}_{$fieldName}"]['type']  = CRM_Utils_Array::value('type', $field);
                        }
                    }
                }
            }
        }
        $this->_select = "SELECT " . implode(', ', $select) . " ";
    }

    function from() {
      $this->_from = NULL;
      $this->_from = "
           FROM  civicrm_contact {$this->_aliases['civicrm_contact']}{$this->_aclFrom} 
                  LEFT JOIN civicrm_contribution {$this->_aliases['civicrm_contribution']} 
                            ON {$this->_aliases['civicrm_contribution']}.contact_id =
                                {$this->_aliases['civicrm_contact']}.id 
                  JOIN civicrm_gift_aid_rejected_contributions {$this->_aliases['civicrm_gift_aid_rejected_contributions']}
                            ON {$this->_aliases['civicrm_gift_aid_rejected_contributions']}.contribution_id = 
                                {$this->_aliases['civicrm_contribution']}.id 
                  JOIN civicrm_batch {$this->_aliases['civicrm_batch']}
                            ON {$this->_aliases['civicrm_batch']}.id =
                                {$this->_aliases['civicrm_gift_aid_rejected_contributions']}.batch_id ";    
    }

    function where() {
        $clauses = array();
        foreach ($this->_columns as $tableName => $table) {
            if (array_key_exists('filters', $table)) {
                foreach ($table['filters'] as $fieldName => $field) {
                    $clause = NULL;
                    if (CRM_Utils_Array::value('operatorType', $field) & CRM_Utils_Type::T_DATE) {
                        $relative = CRM_Utils_Array::value("{$fieldName}_relative", $this->_params);
                        $from     = CRM_Utils_Array::value("{$fieldName}_from", $this->_params);
                        $to       = CRM_Utils_Array::value("{$fieldName}_to", $this->_params);
                        $clause   = $this->dateClause($field['name'], $relative, $from, $to, $field['type']);
                    }else {
                        $op = CRM_Utils_Array::value("{$fieldName}_op", $this->_params);
                        if ($op) {
                            $clause = $this->whereClause($field, $op,
                                CRM_Utils_Array::value("{$fieldName}_value", $this->_params),
                                CRM_Utils_Array::value("{$fieldName}_min", $this->_params),
                                CRM_Utils_Array::value("{$fieldName}_max", $this->_params)
                            );
                        }
                    }
                    if(!empty($clause)){
                        $clauses[] =  $clause;
                    }
                }
            }
        }
        if (empty($clauses)) {
            $this->_where = "WHERE ( 1 ) ";
        }else {
            $this->_where = "WHERE " . implode(' AND ', $clauses);
        }
        $bId = isset($_GET['batch_id']) ? $_GET['batch_id'] : NULL ;
        if($bId){
            $this->_where = "WHERE {$this->_aliases['civicrm_batch']}.id IN (".$bId .")";
        }
        $submissionId = isset($_GET['submissionId']) ? $_GET['submissionId'] : NULL ;
        if($submissionId){
            $this->_where = "WHERE {$this->_aliases['civicrm_gift_aid_rejected_contributions']}.submission_id IN (".$submissionId.")";
        }
    }

    function groupBy() {
        $this->_groupBy = " GROUP BY {$this->_aliases['civicrm_contact']}.id ";
    }
    
    function statistics( &$rows ) {
        $statistics = parent::statistics( $rows );
        $select     = "SELECT SUM( contribution_civireport.total_amount ) as amount";
        $sql        = "{$select} {$this->_from} {$this->_where}";
        $dao        = CRM_Core_DAO::executeQuery( $sql );
        if ( $dao->fetch( ) ) {
            $statistics['counts']['amount'] = array('value' => $dao->amount,
                                                    'title' => 'Total Amount',
                                                    'type'  => CRM_Utils_Type::T_MONEY );
        }
        //print_r ($config);exit;
        return $statistics;
    }

    function postProcess() {
        $this->beginPostProcess();
        // get the acl clauses built before we assemble the query
        $this->buildACLClause($this->_aliases['civicrm_contact']);
        $sql = $this->buildQuery(TRUE);
        $rows = array();
        $this->buildRows($sql, $rows);
        $this->formatDisplay($rows);
        $this->doTemplateAssignment($rows);
        $this->endPostProcess($rows);
    }

    function alterDisplay(&$rows) {
      // custom code to alter rows
        $entryFound = FALSE;
        $checkList = array();
        foreach ($rows as $rowNum => $row) {
            /* PS 18032015
             * Commented out this code
             * Was causing duplicates messages for all contacts showing rejections
             * The report is supposed to support finding all rejections per contact but doesnt seem to at the moment
             * This requirement is to allow charities to focus on the highest reward i.e. if a person has given 1k GBP and we cant claim we should focus on them over someone who's give 1GBP
            if (!empty($this->_noRepeats) && $this->_outputMode != 'csv') {
                // not repeat contact display names if it matches with the one
                // in previous row
                $repeatFound = FALSE;
                foreach ($row as $colName => $colVal) {
                    if(isset($_GET['batch_id'])){
                        $bId = $_GET['batch_id'];
                        $checkList[$colName][] = $colVal;
                        $sQuery = "SELECT GROUP_CONCAT( DISTINCT {$this->_aliases['civicrm_gift_aid_rejected_contributions']}.rejection_reason SEPARATOR '<br>')as gift_rejection_reason FROM civicrm_gift_aid_rejected_contributions {$this->_aliases['civicrm_gift_aid_rejected_contributions']}
                                   WHERE batch_id = ".$bId;
                        $dao = crm_core_dao::executeQuery($sQuery);
                        $dao->fetch();
                        $rReason = $dao->gift_rejection_reason;
                        $rows[$rowNum]['civicrm_gift_aid_rejected_contributions_rejection_reason'] = $rReason; 
                        }elseif (!isset($_GET['batch_id'])) {
                            $batchId = $row['civicrm_gift_aid_rejected_contributions_batch_id'];
                            $checkList[$colName][] = $colVal;
                            $sQuery = "SELECT GROUP_CONCAT( DISTINCT {$this->_aliases['civicrm_gift_aid_rejected_contributions']}.rejection_reason SEPARATOR '<br>')as gift_rejection_reasion FROM civicrm_gift_aid_rejected_contributions {$this->_aliases['civicrm_gift_aid_rejected_contributions']}
                                       WHERE batch_id = ".$batchId;
                            $dao = crm_core_dao::executeQuery($sQuery);
                            $dao->fetch();
                            $rReason = $dao->gift_rejection_reasion;
                            $rows[$rowNum]['civicrm_gift_aid_rejected_contributions_rejection_reason'] = $rReason; 
                        }
                    if (in_array($colName, $this->_noRepeats)){
                        $checkList[$colName][] = $colVal;
                    } 
                } 
            }
            if ($this->_outputMode == 'csv') {
                // not repeat contact display names if it matches with the one
                // in previous row
                $repeatFound = FALSE;
                foreach ($row as $colName => $colVal) {
                    $giftId = $row['civicrm_gift_aid_rejected_contributions_id'];
                    if($giftId){
                        $checkList[$colName][] = $colVal;
                        $sQuery = "SELECT GROUP_CONCAT( DISTINCT {$this->_aliases['civicrm_gift_aid_rejected_contributions']}.rejection_reason SEPARATOR '<br>')as gift_rejection_reasion FROM civicrm_gift_aid_rejected_contributions {$this->_aliases['civicrm_gift_aid_rejected_contributions']}
                                  WHERE batch_id = ".$giftId;
                        $dao = crm_core_dao::executeQuery($sQuery);
                        $dao->fetch();
                        $rReason = $dao->gift_rejection_reasion;
                        $rows[$rowNum]['civicrm_gift_aid_rejected_contributions_rejection_reason'] = $rReason; 
                    }
                    if (in_array($colName, $this->_noRepeats)){
                        $checkList[$colName][] = $colVal;
                    } 
                } 
            }
             * PS 18032015 End of Comment
             */
            if (array_key_exists('civicrm_membership_membership_type_id', $row)) {
                if ($value = $row['civicrm_membership_membership_type_id']) {
                    $rows[$rowNum]['civicrm_membership_membership_type_id'] = CRM_Member_PseudoConstant::membershipType($value, FALSE);
                }
                $entryFound = TRUE;
            }
            if (array_key_exists('civicrm_address_state_province_id', $row)) {
                if ($value = $row['civicrm_address_state_province_id']) {
                    $rows[$rowNum]['civicrm_address_state_province_id'] = CRM_Core_PseudoConstant::stateProvince($value, FALSE);
                }
                $entryFound = TRUE;
            }

            if (array_key_exists('civicrm_address_country_id', $row)) {
                if ($value = $row['civicrm_address_country_id']) {
                    $rows[$rowNum]['civicrm_address_country_id'] = CRM_Core_PseudoConstant::country($value, FALSE);
                }
                $entryFound = TRUE;
            }

            if (array_key_exists('civicrm_contact_sort_name', $row) &&
                $rows[$rowNum]['civicrm_contact_sort_name'] &&
                array_key_exists('civicrm_contact_id', $row)
            ) {
                $url = CRM_Utils_System::url("civicrm/contact/view",
                    'reset=1&cid=' . $row['civicrm_contact_id'],
                    $this->_absoluteUrl
                );
                $rows[$rowNum]['civicrm_contact_sort_name_link']  = $url;
                $rows[$rowNum]['civicrm_contact_sort_name_hover'] = ts("View Contact Summary for this Contact.");
                $entryFound = TRUE;
            }
            if (!$entryFound) {
                break;
            }
        }
    }
}

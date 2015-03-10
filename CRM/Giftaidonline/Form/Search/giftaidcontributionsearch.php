<?php

/**
 * A custom contact search
 */
class CRM_Giftaidonline_Form_Search_giftaidcontributionsearch extends CRM_Contact_Form_Search_Custom_Base implements CRM_Contact_Form_Search_Interface {
    function __construct(&$formValues) {
        parent::__construct($formValues);
    }
    /**
     * Prepare a set of search fields
     *
     * @param CRM_Core_Form $form modifiable
     * @return void
     */

      function buildForm(&$form) {
    CRM_Utils_System::setTitle(ts('Gift Aid Claim Search'));
    
    $activityRoles = array(
      1 => ts('With Valid Declaration'),
      2 => ts('All Claims'),
    );
    $form->addRadio('contribution_claim', ts("Contributions that aren't Claimed"), $activityRoles);
    $form->addDate( 'start_date', ts('Start Date : '), false, array( 'formatType' => 'custom' ) );
    $form->addDate( 'end_date', ts('End Date : '), false, array( 'formatType' => 'custom' ) );
    

    // Optionally define default search values
    $defaults = array( 'contribution_claim' => 2 );
    $form->setDefaults($defaults);

    /**
     * if you are using the standard template, this array tells the template what elements
     * are part of the search criteria
     */
    $elements = array( 'contribution_claim'
                    ,  'start_date'
                    ,  'end_date'
    );
    
    $form->assign('elements',$elements);
  }

  /**
   * Get a list of summary data points
   *
   * @return mixed; NULL or array with keys:
   *  - summary: string
   *  - total: numeric
   */
  function summary() {
    return NULL;
    // return array(
    //   'summary' => 'This is a summary',
    //   'total' => 50.0,
    // );
  }

  /**
   * Get a list of displayable columns
   *
   * @return array, keys are printable column headers and values are SQL column names
   */
  function &columns() {
    // return by reference
    $columns = array(
      ts('Contact Id')        => 'contact_id',
      ts('Contact Name')      => 'display_name',
      ts('Contribution Id')   => 'contribution_id',
      ts('Contribution Date') => 'receive_date',
    );
    return $columns;
  }

  /**
   * Construct a full SQL query which returns one page worth of results
   *
   * @return string, sql
   */
  function all($offset = 0, $rowcount = 0, $sort = NULL, $includeContactIDs = FALSE, $justIDs = FALSE) {
    // delegate to $this->sql(), $this->select(), $this->from(), $this->where(), etc.
    $sql = $this->sql($this->select(), $offset, $rowcount, $sort, $includeContactIDs, NULL);
    //print_r($sql);
    //die();
    return $sql;
  }

  /**
   * Construct a SQL SELECT clause
   *
   * @return string, sql fragment with SELECT arguments
   */
  function select() {
    $select =<<<SELECT
      contact_a.id           as contact_id,
      contact_a.display_name    as display_name,
      contribution.id        as contribution_id,
      contribution.receive_date as receive_date
SELECT;
    
    return $select;
  }

  /**
   * Construct a SQL FROM clause
   *
   * @return string, sql fragment with FROM and JOIN clauses
   */
  function from() {
    return "
      FROM civicrm_contribution contribution 
      LEFT JOIN civicrm_value_gift_aid_submission submission ON ( contribution.id = submission.entity_id )
      LEFT JOIN civicrm_contact contact_a ON ( contact_a.id = contribution.contact_id )
      LEFT JOIN civicrm_value_gift_aid_declaration declaration ON (declaration.entity_id = contact_a.id ) 
    ";
  }

  /**
   * Construct a SQL WHERE clause
   *
   * @return string, sql fragment with conditional expressions
   */
  function where($includeContactIDs = FALSE) {
    $params = array();
    $BatchOptionGroupId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_OptionGroup', 'batch_type', 'id', 'name');
    $apiParams = array(
      'version'         => 3,
      'sequential'      => 1,
      'option_group_id' => $BatchOptionGroupId,
      'name'            => 'Gift Aid',
    );
    $batchOptionValues = civicrm_api('OptionValue', 'getsingle', $apiParams);

    $batchTypeWhereClause = $batchTypeTableJoin = '';
    if(!civicrm_error($batchOptionValues)){
      $batchTypeId = $batchOptionValues['value'];
      $batchTypeTableJoin = "INNER JOIN civicrm_batch batch ON ( entity_batch.batch_id = batch.id )";
      $batchTypeWhereClause = " AND batch.type_id = {$batchTypeId}";
    }
    
    $where  =<<<WHERE
    contribution.id NOT IN 
      ( SELECT entity_batch.entity_id 
        FROM civicrm_entity_batch entity_batch {$batchTypeTableJoin}
        WHERE entity_table = 'civicrm_contribution' {$batchTypeWhereClause} 
       )
WHERE;

    $count  = 1;
    $clause = array();
    $claim   = CRM_Utils_Array::value('contribution_claim', $this->_formValues);
    #to check the valid declaration. at the moment checking only eliglible flag in declaration table.
    if ($claim != NULL && $claim == 1 ) {
      $params[$count] = array(1, 'Integer');
      $clause[] = "declaration.eligible_for_gift_aid = %{$count}";
      $clause[] = "declaration.start_date <= contribution.receive_date";
      $clause[] = "(declaration.end_date IS NULL OR declaration.end_date >= contribution.receive_date)";
      $count++;
    }
    
    $startDate = CRM_Utils_Array::value('start_date', $this->_formValues);
    if( $startDate ){
      $clause[] = "contribution.receive_date >= '".date('Y-m-d H:i:s', strtotime($startDate))."'";
    }
    $endDate = CRM_Utils_Array::value('end_date', $this->_formValues);
    if( $endDate ){
      $clause[] = "contribution.receive_date <= '".date('Y-m-d H:i:s', strtotime($endDate))."'";
    }

    if (!empty($clause)) {
      $where .= ' AND ' . implode(' AND ', $clause);
    }

    return $this->whereClause($where, $params);
  }


  /**
   * Determine the Smarty template for the search screen
   *
   * @return string, template path (findable through Smarty template path)
   */
  function templateFile() {
    return 'CRM/Contact/Form/Search/Custom.tpl';
  }

  /**
   * Modify the content of each row
   *
   * @param array $row modifiable SQL result row
   * @return void
   */
  function alterRow(&$row) {
    $row['contribution_id'] = "<a href='".CRM_Utils_System::url('civicrm/contact/view/contribution', 'id='.$row['contribution_id'].'&cid='.$row['contact_id'].'&reset=1&action=view&context=contribution&selectedChild=contribute')."'>{$row['contribution_id']}</a>";
  }
}
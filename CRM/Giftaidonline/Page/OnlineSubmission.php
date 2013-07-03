<?php

require_once 'CRM/Core/Page.php';
require_once ( dirname(__FILE__) . '/../../../govtalk/HmrcGiftAid.php'  );

class CRM_Giftaidonline_Page_OnlineSubmission extends CRM_Core_Page {
  private function _get_hmrcUserId() {
    return '323412300001';
  }
  private function _get_hmrcPassword() {
    return 'testing1';
  }
  
  function _get_batch_record_sql ( $pBatchId = null ) {
    $cWhere = empty( $pBatchId ) ? null : ' AND batch.id = ' . $pBatchId;
    $cQuery = " SELECT batch.id                                        AS batch_id              " .
              " ,     batch.title                                      AS batch_name            " .
              " ,     batch.created_date                               AS created_date          " .
              " ,     SUM( value_gift_aid_submission.amount )          AS total_amount          " .
              " ,     SUM( value_gift_aid_submission.gift_aid_amount ) AS total_gift_aid_amount " .
              " FROM  civicrm_entity_batch entity_batch                                     " .
              " INNER JOIN civicrm_contribution contribution                           ON entity_batch.entity_table = 'civicrm_contribution' AND entity_batch.entity_id = contribution.id " .
              " INNER JOIN civicrm_value_gift_aid_submission value_gift_aid_submission ON value_gift_aid_submission.entity_id = contribution.id " .
              " INNER JOIN civicrm_batch batch                                         ON batch.id = entity_batch.batch_id " . $cWhere . 
              " GROUP BY batch.id    " .
              " ,        batch.title " .
              " ,        batch.created_date " .
              " ORDER BY batch.created_date DESC ";
                  
    return $cQuery;
  }

  function get_submission_status ( $pEndpoint, $pCorrelation )  {
    $cType = 'status';
    if ( isset( $pEndpoint ) && isset( $pCorrelation )) {
      $oHmrcGiftAid = new HmrcGiftAid( $this->_get_hmrcUserId()
                                     , $this->_get_hmrcPassword()
                                     , 'dev'
                                     );
      $pollResponse = $oHmrcGiftAid->declarationResponsePoll( $pCorrelation
                                                            , $pEndpoint 
                                                            ); 
      if ( $pollResponse ) {
        if (isset($pollResponse['endpoint'])) {
          $cMessage = sprintf( "Response pending.  Please wait %d seconds and then try again."
                             , $pollResponse['interval']
                             );
        } else {
          $cMessage = sprintf( 'Response received, delete command sent.  See below:<br />%s' . print_r( $pollResponse, true ) );
          if ($oHmrcGiftAid->sendDeleteRequest()) {
            $cMessage .= 'Delete request successful. Resource no longer exists on Gateway.';
          } else {
            $cMessage .= 'Delete request failed. Resource may still exist on Gateway.';
            $cType     = 'error';
          }
        }
      } else {
        $cMessage = sprintf( 'Government Gateway returned errors in response to poll request: <br />%s'
                            , print_r( $oHmrcGiftAid->getResponseErrors(), true ) 
                           );
        $cType    = 'error';
      }

    } else {
      $cMessage = 'Unable to poll Government Gateway: missing arguments.';
      $cType    = 'error';
    }
    
      CRM_Core_Session::setStatus( $cMessage , '' , $cType );

  }
  

  function is_submitted( $pBatchId )   {
        
    $bIsSubmitted = null;
    $cQuery = " SELECT submission.batch_id                    AS batch_id " .
              " ,      submission.response_status             AS status   " .
              " FROM   civicrm_gift_aid_submission submission             " .
              " WHERE  submission.batch_id = %1                           " .
              " AND    submission.response_status IS NOT NULL             ";
    $queryParam = array( 1 => array( $pBatchId, 'Integer' ) );
    $oDao     = CRM_Core_DAO::executeQuery( $cQuery, $queryParam );
    while ( $oDao->fetch() ) {
        return true;
    }
  
    return $bIsSubmitted;
  }
  
  function submit_batch ( $pBatchId )   {
    if ( $this->is_submitted( $pBatchId ) ) {
      CRM_Core_Session::setStatus('This Batch has already been submitted.' );
      return array();
    }
   
    $oHmrcGiftAid    = new HmrcGiftAid( $this->_get_hmrcUserId()
                                      , $this->_get_hmrcPassword()
                                      , 'dev'
                                      );
//    $pollRequest     = $oHmrcGiftAid->declarationRequest( $vatNumber         = 999900001
//                                                        , $returnPeriod      = '2009-01'
//                                                        , $senderCapacity    = 'Individual'
//                                                        , $vatOutput         = 6035.33
//                                                        , $vatECAcq          = 0.00
//                                                        , $vatReclaimedInput = 'disabled'
//                                                        , $netOutput         = 84.75
//                                                        , $netInput          = 'disabled'
//                                                        , $netECSupply       = 40235.35
//                                                        , $netECAcq          = 993.54
//                                                        , $totalVat          = 0.0
//                                                        , $netVat            = 0.0
//                                                        , $finalReturn       = false
//                                                        );
//    $pollRequest     = $oHmrcGiftAid->testGatewayReflector();
//    $pollRequest     = $oHmrcGiftAid->testExampleGiftAidSubmission();
    $pollRequest     = $oHmrcGiftAid->giftAidSubmit( $pBatchId );
    $cStatusMessage  = null;
    if ( $pollRequest ) {
      $cStatusMessage = sprintf( 'Return successfully submitted.<br /><a href="submit-poll.php?endpoint=%s&correlation=%s">Poll for HMRC response.</a>'
                               , urlencode( $pollRequest['endpoint']      )
                               , urlencode( $pollRequest['correlationid'] )
                               );
    } else {
      $aError   = array();
      foreach ( $oHmrcGiftAid->getResponseErrors() as $e ) {
        if ( !empty( $e ) ) {
          $aError[] = sprintf("%d - %s", $e[0]['number'], $e[0]['text'] );
        }
      }
      $cStatusMessage = sprintf( "Gift Aid submission was rejected by the Government Gateway:<br />%s"
                               , implode('<br />', $aError)
                               );
    }
    $cRequestXml     = $oHmrcGiftAid->getFullXMLRequest();
//    watchdog(WATCHDOG_INFO, sprintf("XMLRequest:<br /><pre>%s</pre>",  print_r($cRequestXml, true ) ) );
    $cResponseXml    = $oHmrcGiftAid->getFullXMLResponse();
    $cResponseStatus = $cStatusMessage;
    $dSubmissionDate = date('YmdHmi');
    $cQuery = " INSERT INTO civicrm_gift_aid_submission " .
              " ( batch_id        " .
              " , request_xml     " .
              " , response_xml    " .
              " , response_status " .
              " , created_date    " .
              " ) VALUES (        " . 
              "   %1              " .
              " , %2              " .
              " , %3              " .
              " , %4              " .
              " , %5              " .
              " )                 ";
    $queryParam = array( 1 => array( $pBatchId       , 'Integer'    ) 
                       , 2 => array( $cRequestXml    , 'String'     ) 
                       , 3 => array( $cResponseXml   , 'String'     ) 
                       , 4 => array( $cResponseStatus, 'String'     ) 
                       , 5 => array( $dSubmissionDate, 'Timestamp'  ) 
                       );
    
    $oDao        = CRM_Core_DAO::executeQuery( $cQuery, $queryParam );
    $aSubmission = array();
    $cQuery      = $this->_get_batch_record_sql( $pBatchId );
    $oBatchDao   = CRM_Core_DAO::executeQuery( $cQuery );
    if ( $oBatchDao->fetch() ) {
        $aSubmission = array ( 'batch_id'              => $pBatchId
                             , 'batch_name'            => $oBatchDao->batch_name
                             , 'created_date'          => $oBatchDao->created_date
                             , 'submision_date'        => date( "Y-m-d H:i:s", strtotime( $dSubmissionDate ) )
                             , 'total_amount'          => $oBatchDao->total_amount
                             , 'total_gift_aid_amount' => $oBatchDao->total_gift_aid_amount
                             , 'hmrc_response'         => $cResponseStatus
                             );
    }
    
    return $aSubmission;
  }
    
  function get_all_giftaid_batch() {
    $cQuery   = $this->_get_batch_record_sql();
    $oDao     = CRM_Core_DAO::executeQuery( $cQuery );
    $aBatches = array();
    while ( $oDao->fetch() ) {
      if ( !$this->is_submitted ( $oDao->batch_id ) ) {
        $cLink = sprintf( "<a href='%s'>Submit now</a>", 'onlinesubmission?id=' . $oDao->batch_id );
      } else {
        $cLink = sprintf( "%s", $oDao->created_date );          
      }
      $aBatches[] = array ( 'batch_id'              => $oDao->batch_id
                          , 'batch_name'            => $oDao->batch_name
                          , 'created_date'          => $oDao->created_date
                          , 'total_amount'          => $oDao->total_amount
                          , 'total_gift_aid_amount' => $oDao->total_gift_aid_amount
                          , 'submit_link'           => $cLink
                          );
    }
  
    return $aBatches;
  }
  
  function run() {
    // Example: Set the page-title dynamically; alternatively, declare a static title in xml/Menu/*.xml
    CRM_Utils_System::setTitle(ts('OnlineSubmission'));
    $cTask = 'VIEW_BATCH';
    $iBatchId   = isset( $_GET['id'] ) ? $_GET['id'] : null;
    if ( empty( $iBatchId ) ) {
        $this->assign( 'batches', $this->get_all_giftaid_batch() );
    } else {
        $this->assign( 'submission', $this->submit_batch( $iBatchId ) );
        $cTask = 'VIEW_SUBMISSION';
    }
    $this->assign( 'task', $cTask );
        
    parent::run();
  }
}

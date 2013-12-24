<?php

require_once 'CRM/Core/Page.php';
require_once ( dirname(__FILE__) . '/../../../govtalk/HmrcGiftAid.php'  );

class CRM_Giftaidonline_Page_OnlineSubmission extends CRM_Core_Page {

  private function _get_submission( $p_batch_id ) {
    $sSql =<<<EOF
        SELECT id
        ,      batch_id
        ,      created_date
        ,      request_xml
        ,      response_xml
        ,      response_qualifier
        ,      response_errors
        ,      response_end_point
        ,      response_end_point_interval
        ,      response_correlation_id
        ,      transaction_id
        ,      gateway_timestamp
        FROM   civicrm_gift_aid_submission
        WHERE  batch_id = %1
EOF;
    $aQueryParam = array(
                          1 => array( $p_batch_id  , 'Integer' )
                        );

    $oDao = CRM_Core_DAO::executeQuery( $sSql, $aQueryParam );
    if ( $oDao->fetch() ) {
      $aSubmission['id']                          = $oDao->id;
      $aSubmission['batch_id']                    = $oDao->batch_id;
      $aSubmission['created_date']                = $oDao->created_date;
      $aSubmission['request_xml']                 = $oDao->request_xml;
      $aSubmission['response_xml']                = $oDao->response_xml;
      $aSubmission['response_qualifier']          = $oDao->response_qualifier;
      $aSubmission['response_errors']             = $oDao->response_errors;
      $aSubmission['response_end_point']          = $oDao->response_end_point;
      $aSubmission['response_end_point_interval'] = $oDao->response_end_point_interval;
      $aSubmission['response_correlation_id']     = $oDao->response_correlation_id;
      $aSubmission['transaction_id']              = $oDao->transaction_id;
      $aSubmission['gateway_timestamp']           = $oDao->gateway_timestamp;
    } else {
      $aSubmission = array();
    }

    return $aSubmission;
  }

  private function _record_submission(
                                       $p_batch_id
                                     , $p_request_xml
                                     , $p_response_xml
                                     , $p_response_qualifier
                                     , $p_response_errors
                                     , $p_response_end_point
                                     , $p_response_end_point_interval
                                     , $p_response_correlation_id
                                     , $p_transaction_id
                                     , $p_gateway_timestamp
                                     ) {
      $sSql =<<<EOF
              INSERT INTO civicrm_gift_aid_submission(
                batch_id
              , request_xml
              , response_xml
              , response_qualifier
              , response_errors
              , response_end_point
              , response_end_point_interval
              , response_correlation_id
              , transaction_id
              , gateway_timestamp
              ) VALUES (
                %1
              , %2
              , %3
              , %4
              , %5
              , %6
              , %7
              , %8
              , %9
              , %10
              );
EOF;
    $aQueryParam = array( 1   => array( $p_batch_id, 'Integer' )
                        , 2   => array( empty( $p_request_xml                 ) ? '' : $p_request_xml                , 'String'  )
                        , 3   => array( empty( $p_response_xml                ) ? '' : $p_response_xml               , 'String'  )
                        , 4   => array( empty( $p_response_qualifier          ) ? '' : $p_response_qualifier         , 'String'  )
                        , 5   => array( empty( $p_response_errors             ) ? '' : $p_response_errors            , 'String'  )
                        , 6   => array( empty( $p_response_end_point          ) ? '' : $p_response_end_point         , 'String'  )
                        , 7   => array( empty( $p_response_end_point_interval ) ? '' : $p_response_end_point_interval, 'String'  )
                        , 8   => array( empty( $p_response_correlation_id     ) ? '' : $p_response_correlation_id    , 'String'  )
                        , 9   => array( empty( $p_transaction_id              ) ? '' : $p_transaction_id             , 'String'  )
                        , 10  => array( empty( $p_gateway_timestamp           ) ? '' : $p_gateway_timestamp          , 'String'  )
                        );

    $oDao = CRM_Core_DAO::executeQuery( $sSql, $aQueryParam );
    if ( is_a( $oDao, 'DB_Error' ) ) {
      CRM_Core_Error::fatal( 'Trying to create a new Submission record failed.' );
    }
  }

  private function _record_polling(
                                    $p_submission_id
                                  , $p_request_xml
                                  , $p_response_xml
                                  , $p_response_qualifier
                                  , $p_response_errors
                                  , $p_response_end_point
                                  , $p_response_end_point_interval
                                  , $p_response_correlation_id
                                  , $p_transaction_id
                                  , $p_gateway_timestamp
                                  ) {
      $sSql =<<<EOF
              INSERT INTO civicrm_gift_aid_polling_request(
                submission_id
              , request_xml
              , response_xml
              , response_qualifier
              , response_errors
              , response_end_point
              , response_end_point_interval
              , response_correlation_id
              , transaction_id
              , gateway_timestamp
              ) VALUES (
                %1
              , %2
              , %3
              , %4
              , %5
              , %6
              , %7
              , %8
              , %9
              , %10
              );
EOF;
    $aQueryParam = array( 1   => array( $p_submission_id                                                            , 'Integer' )
                        , 2   => array( empty( $p_request_xml                ) ? '' : $p_request_xml                , 'String'  )
                        , 3   => array( empty( $p_response_xml               ) ? '' : $p_response_xml               , 'String'  )
                        , 4   => array( empty( $p_response_qualifier         ) ? '' : $p_response_qualifier         , 'String'  )
                        , 5   => array( empty( $p_response_errors            ) ? '' : $p_response_errors            , 'String'  )
                        , 6   => array( empty( $p_response_end_point         ) ? '' : $p_response_end_point         , 'String'  )
                        , 7   => array( empty( $p_response_end_point_interval) ? '' : $p_response_end_point_interval, 'String'  )
                        , 8   => array( empty( $p_response_correlation_id    ) ? '' : $p_response_correlation_id    , 'String'  )
                        , 9   => array( empty( $p_transaction_id             ) ? '' : $p_transaction_id             , 'String'  )
                        , 10  => array( empty( $p_gateway_timestamp          ) ? '' : $p_gateway_timestamp          , 'String'  )
                        );

    $oDao = CRM_Core_DAO::executeQuery( $sSql, $aQueryParam );
    if ( is_a( $oDao, 'DB_Error' ) ) {
      CRM_Core_Error::fatal( 'Trying to create a new Submission record failed.' );
    }
  }

  function _get_batch_record_sql ( $batch_id = null ) {
    $sWhere = empty( $batch_id ) ? null : ' AND batch.id = ' . $batch_id;
    $sQuery =<<<EOF
      SELECT batch.id                                        AS batch_id
      ,     batch.title                                      AS batch_name
      ,     batch.created_date                               AS created_date
      ,     SUM( value_gift_aid_submission.amount )          AS total_amount
      ,     SUM( value_gift_aid_submission.gift_aid_amount ) AS total_gift_aid_amount
      FROM  civicrm_entity_batch entity_batch
      INNER JOIN civicrm_contribution contribution                           ON entity_batch.entity_table = 'civicrm_contribution' AND entity_batch.entity_id = contribution.id
      INNER JOIN civicrm_value_gift_aid_submission value_gift_aid_submission ON value_gift_aid_submission.entity_id = contribution.id
      INNER JOIN civicrm_batch batch                                         ON batch.id = entity_batch.batch_id
      $sWhere
      GROUP BY batch.id
      ,        batch.title
      ,        batch.created_date
      ORDER BY batch.created_date DESC;
EOF;
    return $sQuery;
  }

  function get_submission_status ( $pEndpoint, $pCorrelation )  {
    $sType = 'status';
    if ( isset( $pEndpoint ) && isset( $pCorrelation ) ) {
      $oHmrcGiftAid = new HmrcGiftAid();
      $pollResponse = $oHmrcGiftAid->declarationResponsePoll( $pCorrelation
                                                            , $pEndpoint
                                                            );
      if ( $pollResponse ) {
        if ( isset( $pollResponse['endpoint'] ) ) {
          $sMessage = sprintf( "Response pending.  Please wait %d seconds and then try again."
                             , $pollResponse['interval']
                             );
        } else {
          $sMessage = sprintf( 'Response received, delete command sent.  See below:<br />%s' . print_r( $pollResponse, true ) );
          if ($oHmrcGiftAid->sendDeleteRequest()) {
            $sMessage .= 'Delete request successful. Resource no longer exists on Gateway.';
          } else {
            $sMessage .= 'Delete request failed. Resource may still exist on Gateway.';
            $sType     = 'error';
          }
        }
      } else {
        $sMessage = sprintf( 'Government Gateway returned errors in response to poll request: <br />%s'
                            , print_r( $oHmrcGiftAid->getResponseErrors(), true )
                           );
        $sType    = 'error';
      }

    } else {
      $sMessage = 'Unable to poll Government Gateway: missing arguments.';
      $sType    = 'error';
    }

    return $sMessage;
  }

  function is_submitted( $p_batch_id ) {
    $bIsSubmitted = null;
    $aSubmission  = $this->_get_submission( $p_batch_id );
    if ( empty( $aSubmission ) ) {
      $bIsSubmitted = false;
    } else {
      $bIsSubmitted = empty( $aSubmission['response_xml'] ) ? false : true;
    }

    return $bIsSubmitted;
  }

  function _build_submission( $p_batch_id, $p_hmrc_gift_aid ) {
    $aSubmission = array();
    $sQuery      = $this->_get_batch_record_sql( $p_batch_id );
    $oBatchDao   = CRM_Core_DAO::executeQuery( $sQuery );
    if ( $oBatchDao->fetch() ) {
      $dSubmissionDate = date('YmdHmi', $p_hmrc_gift_aid->getGatewayTimestamp() );
      $sSuccessMessage = $p_hmrc_gift_aid->getResponseSuccessfullMessage();
      $sResponseStatus = null;
      if ( !empty( $sSuccessMessage ) ) {
          $sResponseStatus = sprintf( "<div>%s</div>"
                                  , $sSuccessMessage
                                  );
      } else {
        $aEndPoint         = $p_hmrc_gift_aid->getResponseEndpoint();
        $sEndPointInterval = isset($aEndPoint['interval']) ? $aEndPoint['interval'] : null ;
        $sUrl              = CRM_Utils_System::url( 'civicrm/onlinesubmission'
                                           , "id=$p_batch_id"
                                           );
        $sRefreshLink      = sprintf( "<a href='%s'>Refresh</a>"
                             , $sUrl
                             );
        $sResponseError  = $this->_response_error_to_string( $p_hmrc_gift_aid->getFullXMLResponse(), '<br /><br />' );
        $sResponseStatus = sprintf( "<div>Status:<strong>%s</strong></div><div>%s</div>"
                                  , $p_hmrc_gift_aid->getResponseQualifier()
                                  , $sResponseError
                                  );
        if ( !empty( $aEndPoint ) ) {
          $sResponseStatus .= sprintf( "<div>Please wait for %s seconds then click on the Refresh link to get an update of the submission.</div><div>[%s]</div>"
                                  , $sEndPointInterval
                                  , $sRefreshLink
                                  );
        }
      }
      $aSubmission = array ( 'batch_id'              => $p_batch_id
                           , 'batch_name'            => $oBatchDao->batch_name
                           , 'created_date'          => $oBatchDao->created_date
                           , 'submision_date'        => date( "Y-m-d H:i:s", strtotime( $dSubmissionDate ) )
                           , 'total_amount'          => $oBatchDao->total_amount
                           , 'total_gift_aid_amount' => $oBatchDao->total_gift_aid_amount
                           , 'hmrc_response'         => $sResponseStatus
                           );
    }

    return $aSubmission;
  }

  private function _parse_response_error( $p_response_errors ) {
    $aErrors = array();
    if ( !empty( $p_response_errors ) ) {
      foreach( $p_response_errors as $v ) {
        $aErrors[] = sprintf( "%s:%s"
                            , $v[number]
                            , $v[text]
                            );
      }
    }
    return implode( ',', $aErrors );
  }

  private function _response_error_to_string( $p_response_errors, $p_separator = "\n" ) {
    $oXmlReader =  new XMLReader();
    $oXmlReader->XML( $p_response_errors );
    $aError = array();
    while ( $oXmlReader->read() ) {
      if ( $oXmlReader->name === 'Error' ) {
        $aError[] = $oXmlReader->readString() ;
        $oXmlReader->next();
      }
    }

    return implode( $p_separator, $aError );
  }

  function process_batch ( $p_batch_id )   {
    $oHmrcGiftAid = new HmrcGiftAid();
    if ( !$this->is_submitted( $p_batch_id ) ) {
        // imacdonal Patch 
        // $oHmrcGiftAid = $oHmrcGiftAid->giftAidSubmit( $p_batch_id );
        $submitResponse = $oHmrcGiftAid->giftAidSubmit( $p_batch_id );
        
        if ( $oHmrcGiftAid->responseHasErrors() === false ) {
          /**
           * TODO: to handle error in submission.
           */
        }
        $dSubmissionDate   = date('YmdHmi', $oHmrcGiftAid->getGatewayTimestamp() );
        $aEndPoint         = $oHmrcGiftAid->getResponseEndpoint();
        $sEndPoint         = isset($aEndPoint['endpoint']) ? $aEndPoint['endpoint'] : null ;
        $sEndPointInterval = isset($aEndPoint['interval']) ? $aEndPoint['interval'] : null ;

        $this->_record_submission( $p_batch_id
                                  , $oHmrcGiftAid->getFullXMLRequest()
                                  , $oHmrcGiftAid->getFullXMLResponse()
                                  , $oHmrcGiftAid->getResponseQualifier()
                                  , $this->_response_error_to_string( $oHmrcGiftAid->getFullXMLResponse() )
                                  , $sEndPoint
                                  , $sEndPointInterval
                                  , $oHmrcGiftAid->getResponseCorrelationId()
                                  , $oHmrcGiftAid->getTransactionId()
                                  , $oHmrcGiftAid->getGatewayTimestamp()
                                  );
    } else {
      $aSubmission = $this->_get_submission( $p_batch_id );
      if ( empty( $aSubmission ) ) {
        CRM_Core_Error::fatal( "Cannot locate Submission record for batch: $p_batch_id" );
      }
      $sEndPoint    = $aSubmission['response_end_point'];
      $sCorrelation = $aSubmission['response_correlation_id'];
      $oHmrcGiftAid = $oHmrcGiftAid->giftAidPoll( $sEndPoint, $sCorrelation );
      $dSubmissionDate   = date('YmdHmi', $oHmrcGiftAid->getGatewayTimestamp() );
      $aEndPoint         = $oHmrcGiftAid->getResponseEndpoint();
      $sEndPoint         = isset($aEndPoint['endpoint']) ? $aEndPoint['endpoint'] : null ;
      $sEndPointInterval = isset($aEndPoint['interval']) ? $aEndPoint['interval'] : null ;
      $this->_record_polling( $aSubmission['id']
                            , $oHmrcGiftAid->getFullXMLRequest()
                            , $oHmrcGiftAid->getFullXMLResponse()
                            , $oHmrcGiftAid->getResponseQualifier()
                            , $this->_response_error_to_string( $oHmrcGiftAid->getFullXMLResponse() )
                            , $sEndPoint
                            , $sEndPointInterval
                            , $oHmrcGiftAid->getResponseCorrelationId()
                            , $oHmrcGiftAid->getTransactionId()
                            , $oHmrcGiftAid->getGatewayTimestamp()
                            );
    }

    $aSubmission = $this->_build_submission( $p_batch_id, $oHmrcGiftAid );
    return $aSubmission;
  }

  function get_all_giftaid_batch() {
    $cQuery   = $this->_get_batch_record_sql();
    $oDao     = CRM_Core_DAO::executeQuery( $cQuery );
    $aBatches = array();
    while ( $oDao->fetch() ) {
      if ( !$this->is_submitted ( $oDao->batch_id ) ) {
        $sUrl  = CRM_Utils_System::url( 'civicrm/onlinesubmission'
                                      , "id=$oDao->batch_id"
                                      );
        $cLink = sprintf( "<a href='%s'>Submit now</a>"
                        , $sUrl
                        );
      } else {
        $aSubmission = $this->_get_submission( $oDao->batch_id );
        $sUrl  = CRM_Utils_System::url( 'civicrm/onlinesubmission'
                                      , "id=$oDao->batch_id&task=POLL"
                                      );
        $cLink = sprintf( "%s<br /><a href='%s'>Get new status</a>"
                        , $oDao->created_date
                        , $sUrl
                        );
      }

      $aBatches[] = array ( 'batch_id'              => $oDao->batch_id
                          , 'batch_name'            => $oDao->batch_name
                          , 'created_date'          => $oDao->created_date
                          , 'total_amount'          => $oDao->total_amount
                          , 'total_gift_aid_amount' => $oDao->total_gift_aid_amount
                          , 'action'                => $cLink
                          );
    }

    return $aBatches;
  }

  function run() {
    CRM_Utils_System::setTitle( ts( 'Online Submission' ) );
    $iBatchId = CRM_Utils_Request::retrieve( 'id'
                                           , 'Positive'
                                           );
    if ( empty( $iBatchId ) ) {
      $this->assign( 'batches', $this->get_all_giftaid_batch() );
      $sTask = 'VIEW_BATCH';
    } else {
      $this->assign( 'submission', $this->process_batch( $iBatchId ) );
      $sTask = 'VIEW_SUBMISSION';
    }
    $this->assign( 'task', $sTask );

    parent::run();
  }
}

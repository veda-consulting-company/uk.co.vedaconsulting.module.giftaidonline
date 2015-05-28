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
        ORDER BY id DESC
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

  private function _get_polling_request( $submission_id ) {
    $pSql =<<<EOF
        SELECT id
        ,      submission_id
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
        FROM   civicrm_gift_aid_polling_request
        WHERE  submission_id = %1
        ORDER BY id DESC
EOF;
    $pQueryParam = array(
                          1 => array( $submission_id  , 'Integer' )
                        );

    $oDao = CRM_Core_DAO::executeQuery( $pSql, $pQueryParam );
    if ( $oDao->fetch() ) {
      $pRequest['id']                          = $oDao->id;
      $pRequest['submission_id']               = $oDao->submission_id;
      $pRequest['created_date']                = $oDao->created_date;
      $pRequest['request_xml']                 = $oDao->request_xml;
      $pRequest['response_xml']                = $oDao->response_xml;
      $pRequest['response_qualifier']          = $oDao->response_qualifier;
      $pRequest['response_errors']             = $oDao->response_errors;
      $pRequest['response_end_point']          = $oDao->response_end_point;
      $pRequest['response_end_point_interval'] = $oDao->response_end_point_interval;
      $pRequest['response_correlation_id']     = $oDao->response_correlation_id;
      $pRequest['transaction_id']              = $oDao->transaction_id;
      $pRequest['gateway_timestamp']           = $oDao->gateway_timestamp;
    } else {
      $pRequest = array();
    }

    return $pRequest;
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
                        , 10  => array( empty( $p_gateway_timestamp           ) ? '' : date( "Y-m-d H:i:s", $p_gateway_timestamp)          , 'String'  )
                        );

    $oDao = CRM_Core_DAO::executeQuery( $sSql, $aQueryParam );
    if ( is_a( $oDao, 'DB_Error' ) ) {
      CRM_Core_Error::fatal( 'Trying to create a new Submission record failed.' );
    }
    $submissionId = CRM_Core_DAO::singleValueQuery('SELECT LAST_INSERT_ID()');
    return $submissionId;
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
                        , 10  => array( empty( $p_gateway_timestamp          ) ? '' : date( "Y-m-d H:i:s", $p_gateway_timestamp)          , 'String'  )
                        );

    $oDao = CRM_Core_DAO::executeQuery( $sSql, $aQueryParam );
    if ( is_a( $oDao, 'DB_Error' ) ) {
      CRM_Core_Error::fatal( 'Trying to create a new Submission record failed.' );
    }

    return NULL;
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

  function allow_resubmission( $p_batch_id ) {
    $allowResubmission = false;
    $aSubmission  = $this->_get_submission( $p_batch_id );
    $pRequest = $this->_get_polling_request( $aSubmission['id'] );
    if ($aSubmission['response_qualifier'] == 'error' && !empty($aSubmission['response_errors'])) {
      $allowResubmission = true;
    }
    if (!empty($pRequest) && $pRequest['response_qualifier'] == 'error') {
      $allowResubmission = true; 
    }
    return $allowResubmission;
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
        // hook to carry out other actions on success submission
        CRM_Giftaidonline_Utils_Hook::giftAidOnlineSubmitted( $p_batch_id );

      } else {
        $aEndPoint         = $p_hmrc_gift_aid->getResponseEndpoint();
        $sEndPointInterval = isset($aEndPoint['interval']) ? $aEndPoint['interval'] : null ;
        $sUrl              = CRM_Utils_System::url( 'civicrm/onlinesubmission'
                                           , "id=$p_batch_id&task=POLL"
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

  function process_batch ( $p_batch_id, $task = NULL )   {
    $oHmrcGiftAid = new HmrcGiftAid();
    $rejections = array();
    $submissionId = '';
    if ( !$this->is_submitted( $p_batch_id ) ) {
        // imacdonal Patch
        // $oHmrcGiftAid = $oHmrcGiftAid->giftAidSubmit( $p_batch_id );
        $submitResponse = $oHmrcGiftAid->giftAidSubmit( $p_batch_id, $rejections );

        if ( $oHmrcGiftAid->responseHasErrors() === false ) {
          /**
           * TODO: to handle error in submission.
           */
        }
        $dSubmissionDate   = date('YmdHmi', $oHmrcGiftAid->getGatewayTimestamp() );
        $aEndPoint         = $oHmrcGiftAid->getResponseEndpoint();
        $sEndPoint         = isset($aEndPoint['endpoint']) ? $aEndPoint['endpoint'] : null ;
        $sEndPointInterval = isset($aEndPoint['interval']) ? $aEndPoint['interval'] : null ;

        $submissionId = $this->_record_submission( $p_batch_id
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

      if ($task == 'RESUBMIT') {
        $submitResponse = $oHmrcGiftAid->giftAidSubmit( $p_batch_id , $rejections );
        if ( $oHmrcGiftAid->responseHasErrors() === false ) {
          /**
           * TODO: to handle error in submission.
           */
        }
        $dSubmissionDate   = date('YmdHmi', $oHmrcGiftAid->getGatewayTimestamp() );
        $aEndPoint         = $oHmrcGiftAid->getResponseEndpoint();
        $sEndPoint         = isset($aEndPoint['endpoint']) ? $aEndPoint['endpoint'] : null ;
        $sEndPointInterval = isset($aEndPoint['interval']) ? $aEndPoint['interval'] : null ;
        $submissionId = $this->_record_submission( $p_batch_id
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
      } else if ($task == 'POLL') {
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
        $submissionId = $this->_record_polling( $aSubmission['id']
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
    }

    // Update submission_id in rejected contributions table
    if (!empty($rejections) && !empty($submissionId)) {
      self::update_submission_id_for_rejections($rejections, $submissionId);
    }

    $aSubmission = $this->_build_submission( $p_batch_id, $oHmrcGiftAid );
    return $aSubmission;
  }

  /*
   * Function to update submission_id in civicrm_gift_aid_rejected_contributions table
   * in order to report rejections based on submission
   */
  function update_submission_id_for_rejections($rejections, $submissionId) {
    // Check if submission_id exists in the table
    $columnExists = CRM_Core_DAO::checkFieldExists('civicrm_gift_aid_rejected_contributions', 'submission_id');
    if(!$columnExists) {
      $query = "
        ALTER TABLE civicrm_gift_aid_rejected_contributions
        ADD submission_id int(10) unsigned AFTER batch_id";
      CRM_Core_DAO::executeQuery($query);
    }

    $rejectionIds = implode(',', $rejections);
    $updateQuery = "UPDATE civicrm_gift_aid_rejected_contributions SET submission_id = %1 WHERE id IN (%2)";
    $updateParams = array(
                      '1' => array($submissionId, 'Integer'),
                      '2' => array($rejectionIds, 'String'),
                    );
    CRM_Core_DAO::executeQuery($updateQuery, $updateParams);
  }

  function get_all_giftaid_batch() {
    $cQuery   = $this->_get_batch_record_sql();
    $oDao     = CRM_Core_DAO::executeQuery( $cQuery );
    $aBatches = array();

    // Get report instance
    $params = array(
      'version' => 3,
      'report_id' => GIFTAID_FAILURE_REPORT_ID,
    );
    $result = civicrm_api('ReportInstance', 'getsingle', $params);
    if (!empty($result['id'])) {
      $reportUrl = 'civicrm/report/instance/'.$result['id'];
    }

    while ( $oDao->fetch() ) {
      $responseErrors = $responseMessage = $sQuerySt = $linkLabel = $sUrl = '';
      $cLink = $oDao->created_date."<br />";
      if ( !$this->is_submitted ( $oDao->batch_id ) ) {
        $sUrl  = CRM_Utils_System::url( 'civicrm/onlinesubmission'
                                      , "id=$oDao->batch_id"
                                      );
        $cLink .= sprintf( "<a href='%s'>Submit now</a>"
                        , $sUrl
                        );
      } else {
        $aSubmission = $this->_get_submission( $oDao->batch_id );
        $pRequest = $this->_get_polling_request( $aSubmission['id'] );
        // Allow resubmission of the batch, if previously reported as 'error'
        if ( $this->allow_resubmission($oDao->batch_id) ) {
          $sQueryStr = "id=$oDao->batch_id&task=RESUBMIT";
          $linkLabel = 'Re-Submit now';
          // Get submission response error
          if ($aSubmission['response_qualifier'] == 'error') {
            $responseErrors = $aSubmission['response_xml'];  
          }

          // Get response error from polling, if available (as polling reponse is the latest)
          if (!empty($pRequest) && $pRequest['response_qualifier'] == 'error') {
            $responseErrors = $pRequest['response_xml'];  
          }
          
        } else {
          $sQueryStr = "id=$oDao->batch_id&task=POLL";
          $linkLabel = 'Get new status';
        }

        if (isset($pRequest['response_qualifier']) && $pRequest['response_qualifier'] == 'response') {
          $sQueryStr = '';
          $linkLabel = '';
          $responseMessage = $pRequest['response_xml'];
        } 
        if (!empty($sQueryStr)) {
          $sUrl  = CRM_Utils_System::url( 'civicrm/onlinesubmission'
                                        , $sQueryStr
                                        );
          $cLink .= sprintf( "<a href='%s'>{$linkLabel}</a><br />"
                          , $sUrl
                          );
        }
      }

      if (!empty($responseErrors)) {
        $responseErrorObj = simplexml_load_string($responseErrors);
        $responseErrorMsg = "Raised By:<br />".$responseErrorObj->GovTalkDetails->GovTalkErrors->Error->RaisedBy;
        $responseErrorMsg .= "<br /><br />Number:<br />".$responseErrorObj->GovTalkDetails->GovTalkErrors->Error->Number;
        $responseErrorMsg .= "<br /><br />Type:<br />".$responseErrorObj->GovTalkDetails->GovTalkErrors->Error->Type;
        $responseErrorMsg .= "<br /><br />Error Text:<br />".$responseErrorObj->GovTalkDetails->GovTalkErrors->Error->Text;
        $rLink = sprintf( "<a style='cursor: pointer;' id='errorLink_%s' class='errorLink'>View Failure Message</a>
                        <div id='errorMessage_%s' style='display: none;'><div title='Failure Message'>%s</div></div>"
                        , $oDao->batch_id
                        , $oDao->batch_id
                        , $responseErrorMsg
                        );
        $cLink .= $rLink;
      }

      if (!empty($responseMessage)) {
        $responseObj = simplexml_load_string($responseMessage);
        $responseMsg = '';
        if (isset($responseObj->Body->SuccessResponse->IRmarkReceipt->Message)) {
          $responseMsg .= "Message:<br />".$responseObj->Body->SuccessResponse->IRmarkReceipt->Message;
        }
        $responseMsg .= "<br /><br />CorrelationID:<br />".$responseObj->Header->MessageDetails->CorrelationID;
        $responseMsg .= "<br /><br />GatewayTimestamp:<br />".$responseObj->Header->MessageDetails->GatewayTimestamp;
        $rLink = sprintf( "<a style='cursor: pointer;' id='responseLink_%s' class='responseLink'>View Response</a>
                        <div id='responseMessage_%s' style='display: none;'><div title='Response Message'>%s</div></div>"
                        , $oDao->batch_id
                        , $oDao->batch_id
                        , $responseMsg
                        );
        $cLink .= $rLink;
      }

      $reportLink = '';
      if (!empty($reportUrl)) {
        //$aSubmission = $this->_get_submission( $oDao->batch_id );
        //$submissionId = $aSubmission['id'];
        $rLink = CRM_Utils_System::url( $reportUrl
                                      , "batch_id=$oDao->batch_id&force=1&reset=1"
                                      );
        $reportLink = sprintf( "<a href='%s'>View</a>"
                        , $rLink
                        );
      }

      $aBatches[] = array ( 'batch_id'              => $oDao->batch_id
                          , 'batch_name'            => $oDao->batch_name
                          , 'created_date'          => $oDao->created_date
                          , 'total_amount'          => $oDao->total_amount
                          , 'total_gift_aid_amount' => $oDao->total_gift_aid_amount
                          , 'action'                => $cLink
                          , 'report_link'           => $reportLink
                          );
    }

    return $aBatches;
  }

  function run() {
    CRM_Utils_System::setTitle( ts( 'Online Submission' ) );
    $iBatchId = CRM_Utils_Request::retrieve( 'id'
                                           , 'Positive'
                                           );
    $task = CRM_Utils_Request::retrieve( 'task'
                                           , 'String'
                                           );
    if ( empty( $iBatchId ) ) {
      $this->assign( 'batches', $this->get_all_giftaid_batch() );
      $sTask = 'VIEW_BATCH';
    } else {
      $this->assign( 'submission', $this->process_batch( $iBatchId, $task ) );
      $sTask = 'VIEW_SUBMISSION';
    }
    $this->assign( 'task', $sTask );

    parent::run();
  }
}

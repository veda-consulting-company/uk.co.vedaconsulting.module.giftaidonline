<?php

#
#  HmrcGiftAid.php
#
#  Created by Long Luong on 13-03-2013.
#  Copyright 2013, Veda Consulting Limited. All rights reserved.
#
#  This program is free software: you can redistribute it and/or modify
#  it under the terms of the GNU General Public License as published by
#  the Free Software Foundation, either version 3 of the License, or
#  (at your option) any later version.
#
#  You may obtain a copy of the License at:
#  http://www.gnu.org/licenses/gpl-3.0.txt
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.

/**
 * HMRC Gift Aid API client.  Extends the functionality provided by the
 * GovTalk class to build and parse HMRC Gift Aid submissions.  The php-govtalk
 * base class needs including externally in order to use this extention.
 *
 * @author Long Luong
 * @copyright 2013, Veda Consulting Limited
 * @licence http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 */
require_once 'CRM/Core/Page.php';
require_once ( dirname(__FILE__) . '/GiftAidGovTalk.php' );
class HmrcGiftAid extends GiftAidGovTalk {

 /* General IRenvelope related variables. */

	/**
	 * Details of the agent sending the return declaration.
	 *
	 * @var string
	 */
	private $_agentDetails = array();
  
  
	/**
	 * Specific Settings required for call HRMC Webservices.
	 *
	 * @var array
	 */
  private $_Settings    = array();
	
 /* System / internal variables. */

	/**
	 * Flag indicating if the IRmark should be generated for outgoing XML.
	 *
	 * @var boolean
	 */
	private $_generateIRmark = true;

 /* Magic methods. */

	/**
	 * Instance constructor. Contains a hard-coded CH XMLGW URL and additional
	 * schema location.  Adds a channel route identifying the use of this
	 * extension.
	 *
	 * @param string $govTalkSenderId GovTalk sender ID.
	 * @param string $govTalkPassword GovTalk password.
	 * @param string $service The service to use ('dev', or 'live').
	 */
	public function __construct( $govTalkSenderId, $govTalkPassword, $service = 'live' ) {
		switch ($service) {
			case 'dev':
				parent::__construct( 'https://secure.dev.gateway.gov.uk/submission'
                           , $govTalkSenderId
                           , $govTalkPassword
                           );
				$this->setTestFlag( true );
			break;
			default:
				parent::__construct( 'https://secure.gateway.gov.uk/submission'
                           , $govTalkSenderId
                           , $govTalkPassword
                           );
			break;
    }
    
    $cSettingsSelect = <<<EOD
      SELECT setting.name                                    AS name
      ,      setting.value                                   AS value       
      FROM   civicrm_gift_aid_submission_setting setting                                    
EOD;
    $oDao = CRM_Core_DAO::executeQuery( $cSettingsSelect, array() );    
    while ( $oDao->fetch() ) {
      $this->_Settings[$oDao->name] = $oDao->value;
    }
		
		$this->setMessageAuthentication( 'clear' );
		$this->addChannelRoute( 'http://www.vedaconsulting.co.uk/uk-hrmc-gift-aid-online-submission/'
                          , 'Veda Consulting HMRC Gift Aid Online Submission extension'
                          , '0.1'
                          );
	}

 /* Public methods. */

	/**
	 * Turns the IRmark generator on or off (by default the IRmark generator is
	 * turned off). When it's switched off no IRmark element will be sent with
	 * requests to HMRC.
	 *
	 * @param boolean $flag True to turn on IRmark generator, false to turn it off.
	 */
	public function setIRmarkGeneration($flag) {
	
		if (is_bool($flag)) {
			$this->_generateIRmark = $flag;
		} else {
			return false;
		}
	
	}
	
	/**
	 * Sets details about the agent submitting the declaration.
	 *
	 * The agent company's address should be specified in the following format:
	 *   line => Array, each element containing a single line information.
	 *   postcode => The agent company's postcode.
	 *   country => The agent company's country. Defaults to England.
	 *
	 * The agent company's primary contact should be specified as follows:
	 *   name => Array, format as follows:
	 *     title => Contact's title (Mr, Mrs, etc.)
	 *     forename => Contact's forename.
	 *     surname => Contact's surname.
	 *   email => Contact's email address (optional).
	 *   telephone => Contact's telephone number (optional).
	 *   fax => Contact's fax number (optional).
	 *
	 * @param string $company The agent company's name.
	 * @param array $address The agent company's address in the format specified above.
	 * @param array $contact The agent company's key contact in the format specified above (optional, may be skipped with a null value).
	 * @param string $reference An identifier for the agent's own reference (optional).
	 */
	public function setAgentDetails($company, array $address, array $contact = null, $reference = null) {
	
		if (preg_match('/[A-Za-z0-9 &\'\(\)\*,\-\.\/]*/', $company)) {
			$this->_agentDetails['company'] = $company;
			$this->_agentDetails['address'] = $address;
			if (!isset($this->_agentDetails['address']['country'])) {
				$this->_agentDetails['address']['country'] = 'England';
			}
			if ($contact !== null) {
				$this->_agentDetails['contact'] = $contact;
			}
			if (($reference !== null) && preg_match('/[A-Za-z0-9 &\'\(\)\*,\-\.\/]*/', $reference)) {
				$this->_agentDetails['reference'] = $reference;
			}
		} else {
			return false;
		}
	
	}

  public function testExampleGiftAidSubmission() {
    $cSenderId       = '323412300001';
    $cAuthValue      = 'testing1';
    $cKeyCharId      = 'AB12345';
    $cChannelUri     = '2252';
    $cChannelProduct = 'VedaGiftAidSubmission';
    $cChannelVersion = '1.0 beta';
    $cXML            = <<<EOF
<GovTalkMessage xmlns="http://www.govtalk.gov.uk/CM/envelope">
	<EnvelopeVersion>2.0</EnvelopeVersion>
	<Header>
		<MessageDetails>
			<Class>HMRC-CHAR-CLM</Class>
			<Qualifier>request</Qualifier>
			<Function>submit</Function>
			<CorrelationID/>
			<Transformation>XML</Transformation>
			<GatewayTest>1</GatewayTest>
      <GatewayTimestamp></GatewayTimestamp>             
		</MessageDetails>
		<SenderDetails>
			<IDAuthentication>
				<SenderID>$cSenderId</SenderID>
				<Authentication>
					<Method>clear</Method>
					<Role>principal</Role>
					<Value>$cAuthValue</Value>
				</Authentication>
			</IDAuthentication>
		</SenderDetails>
	</Header>
	<GovTalkDetails>
		<Keys>
			<Key Type="CHARID">$cKeyCharId</Key>
		</Keys>
		<TargetDetails>
			<Organisation>IR</Organisation>
		</TargetDetails>
		<ChannelRouting>
			<Channel>
				<URI>$cChannelUri</URI>
				<Product>$cChannelProduct</Product>
				<Version>$cChannelVersion</Version>
			</Channel>
		</ChannelRouting>
	</GovTalkDetails>
	<Body>
		<IRenvelope xmlns="http://www.govtalk.gov.uk/taxation/charities/r68/1">
			<IRheader>
				<Keys>
					<Key Type="CHARID">$cKeyCharId</Key>
				</Keys>
				<PeriodEnd>2012-10-31</PeriodEnd>
				<DefaultCurrency>GBP</DefaultCurrency>
				<IRmark Type="generic">ymFYM1StJ3IbfyieQuJ04tPXOBY=</IRmark>
				<Sender>Individual</Sender>
			</IRheader>
			<R68>
				<AuthOfficial>
					<OffName>
						<Fore>John</Fore>
						<Sur>Smith</Sur>
					</OffName>
					<OffID>
						<Postcode>AB12 3CD</Postcode>
					</OffID>
					<Phone>01234 567890</Phone>
				</AuthOfficial>
				<Declaration>yes</Declaration>
				<Claim>
					<OrgName>My Organisation</OrgName>
					<HMRCref>AA12345</HMRCref>
					<Regulator>
						<RegName>CCEW</RegName>
						<RegNo>A1234</RegNo>
					</Regulator>
					<Repayment>
						<GAD>
							<Donor>
								<Fore>James</Fore>
								<Sur>Bacon</Sur>
								<House>55</House>
								<Postcode>AB23 4CD</Postcode>
							</Donor>
							<Date>2012-10-01</Date>
							<Total>5.00</Total>
						</GAD>
						<GAD>
							<Donor>
								<Fore>Mary</Fore>
								<Sur>Jones</Sur>
								<House>12</House>
								<Postcode>AB23 9CD</Postcode>
							</Donor>
							<Date>2012-10-15</Date>
							<Total>10.00</Total>
						</GAD>
						<GAD>
							<Donor>
								<Fore>Bob</Fore>
								<Sur>Smith</Sur>
								<House>1</House>
								<Postcode>BA23 9CD</Postcode>
							</Donor>
							<Date>2012-10-03</Date>
							<Total>2.50</Total>
						</GAD>
						<GAD>
							<Donor>
								<Fore>Jane</Fore>
								<Sur>Smith</Sur>
								<House>1</House>
								<Postcode>BA23 9CD</Postcode>
							</Donor>
							<Date>2012-10-03</Date>
							<Total>12.00</Total>
						</GAD>
						<EarliestGAdate>2012-10-01</EarliestGAdate>
						<OtherInc>
							<Payer>Peter Other</Payer>
							<OIDate>2012-10-31</OIDate>
							<Gross>13.12</Gross>
							<Tax>2.62</Tax>
						</OtherInc>
					</Repayment>
					<GASDS>
						<ConnectedCharities>no</ConnectedCharities>
						<CommBldgs>no</CommBldgs>
					</GASDS>
				</Claim>
			</R68>
		</IRenvelope>
	</Body>
</GovTalkMessage>            
EOF;
    if ($this->sendMessage( $cXML ) && ($this->responseHasErrors() === false)) {
      $returnable = $this->getResponseEndpoint();
      $returnable['correlationid'] = $this->getResponseCorrelationId();
      return $returnable;
    } else {
      return false;
    }    
  }

  public function testGatewayReflector() {
    $iChardId              = 'AB12345';
    $cOrganisation         = 'HMRC';
    $cClientUri            = '2355';
    $cClientProduct        = 'VedaGiftAidSubmission';
    $cClientProductVersion = '1.0 beta';

    // Set the message envelope
    $this->setMessageClass         ( 'HMRC-CHAR-CLM' );
    $this->setMessageQualifier     ( 'request'       );
    $this->setMessageFunction      ( 'submit'        );
    $this->setMessageCorrelationId (  null           );
    $this->setMessageTransformation( 'XML'           );
    $this->addTargetOrganisation   ( $cOrganisation  );
    
    $this->addMessageKey( 'CHARID'
                        , $iChardId
                        );
    $this->addChannelRoute( $cClientUri
                          , $cClientProduct
                          , $cClientProductVersion 
                          );
    // Build message body...
    $package = new XMLWriter();
    $package->openMemory();
    $package->setIndent( true );
	  // Send the message and deal with the response...
    $package->startElement( 'BodyText' );
      $package->writeAttribute( 'xmlns', 'http://vedaconsulting.co.uk' );
      $package->startElement( 'MyText' );
        $package->text( 'The Gateway Reflector service cannot validate the data in the body tag so please use LTS.' );
      $package->endElement(); 
    $package->endElement(); 
    
    $this->setMessageBody( $package );

    if ($this->sendMessage() && ($this->responseHasErrors() === false)) {
      $returnable = $this->getResponseEndpoint();
      $returnable['correlationid'] = $this->getResponseCorrelationId();
      return $returnable;
    } else {
      return false;
    }
  }
  
  private function build_giftaid_donors_xml( $pBatchId, &$package ) {
    $cDonorSelect = <<<EOD
      SELECT batch.id                                        AS batch_id         
      ,     batch.title                                      AS batch_name       
      ,     batch.created_date                               AS created_date     
      ,     contact.id                                       AS contact_id
      ,     contact.first_name                               AS first_name
      ,     contact.last_name                                AS last_name
      ,     address.street_number                            AS house_no 
      ,     address.postal_code                              AS postcode 
      ,     value_gift_aid_submission.amount                 AS amount     
      ,     value_gift_aid_submission.gift_aid_amount        AS gift_aid_amount
      FROM  civicrm_entity_batch entity_batch                                    
      INNER JOIN civicrm_batch batch                                         ON batch.id = entity_batch.batch_id
      INNER JOIN civicrm_contribution contribution                           ON entity_batch.entity_table = 'civicrm_contribution' AND entity_batch.entity_id = contribution.id 
      INNER JOIN civicrm_contact      contact                                ON contact.id                = contribution.contact_id 
      INNER JOIN civicrm_address      address                                ON address.id                = contact.id
      INNER JOIN civicrm_value_gift_aid_submission value_gift_aid_submission ON value_gift_aid_submission.entity_id = contribution.id 
      WHERE batch.id = %1            
EOD;
    $aQueryParam = array( 1 => array( $pBatchId, 'Integer' ) );
    $oDao        = CRM_Core_DAO::executeQuery( $cDonorSelect, $aQueryParam );    
    $aDonors     = array();
    while ( $oDao->fetch() ) {
      $aDonors[] = array( 'forename' => $oDao->first_name
                        , 'surname'  => $oDao->last_name
                        , 'house_no' => $oDao->house_no
                        , 'postcode' => $oDao->postcode
                        );
    }
    
    foreach ( $aDonors as $d ) {
      $package->startElement( 'GAD' );
        $package->startElement( 'Donor' );
          $package->writeElement( 'Fore'    , $d['forename'] );
          $package->writeElement( 'Sur'     , $d['surname']  );
          $package->writeElement( 'House'   , $d['house_no'] );
          $package->writeElement( 'Postcode', $d['postcode'] );
        $package->endElement(); # Donor    
      $package->endElement(); # GAD    
    }
  }
  
  private function build_claim_xml( $pBatchId, &$package ) {
    $cClaimOrgName         = $this->_Settings['CLAIMER_ORG_NAME'];
    $cClaimOrgHmrcref      = $this->_Settings['CLAIMER_ORG_HMRC_REF'];
    $cRegulatorName        = $this->_Settings['CLAIMER_ORG_REGULATOR_NAME'];
    $cRegulatorNo          = $this->_Settings['CLAIMER_ORG_REGULATOR_NO'];
    $cConnectedCharities   = 'no';
    $cCommBldgs            = 'no';

    $package->startElement(     'Claim'                      );
      $package->writeElement(   'OrgName', $cClaimOrgName    );
      $package->writeElement(   'HMRCref', $cClaimOrgHmrcref );
      $package->startElement(   'Regulator'                  );
        $package->writeElement( 'RegName', $cRegulatorName   );
        $package->writeElement( 'RegNo'  , $cRegulatorNo     );
      $package->endElement(); # Regulator
      $package->startElement(   'Repayment'                  );
        $this->build_giftaid_donors_xml( $pBatchId, $package );
      $package->endElement(); # Repayment
      $package->startElement(   'GASDS'                                      );
        $package->writeElement( 'ConnectedCharities'  , $cConnectedCharities );
        $package->writeElement( 'CommBldgs'           , $cCommBldgs          );
      $package->endElement(); # GASDS
    $package->endElement(); # Claim
  }
  
  public function giftAidSubmit( $pBatchId ) {
    $cChardId              = $this->_Settings['CHAR_ID'];
    $cOrganisation         = 'IR';
    $cClientUri            = $this->_Settings['VENDOR_ID'];
    $cClientProduct        = 'VedaGiftAidSubmission';
    $cClientProductVersion = '1.0 beta';
    $dReturnPeriod         = '2013-03-31';
    $sDefaultCurrency      = 'GBP';
    $sIRmark               = 'ymFYM1StJ3IbfyieQuJ04tPXOBY';
    $sSender               = 'Individual';
    $cAuthOffSurname       = 'Smith';
    $cAuthOffForename      = 'John';
    $cAuthOffPhone         = '';
    $cAuthOffPostcode      = 'AB12 3CD';
    $cDeclaration          = 'yes';
    
    // Set the message envelope
    $this->setMessageClass         ( 'HMRC-CHAR-CLM' );
    $this->setMessageQualifier     ( 'request'       );
    $this->setMessageFunction      ( 'submit'        );
    $this->setMessageCorrelationId (  null           );
    $this->setMessageTransformation( 'XML'           );
    $this->addTargetOrganisation   ( $cOrganisation  );
    
    
    $this->addMessageKey( 'CHARID'
                        , $cChardId
                        );
    $this->addChannelRoute( $cClientUri
                          , $cClientProduct
                          , $cClientProductVersion 
                          );
    // Build message body...
    $package = new XMLWriter();
    $package->openMemory();
    $package->setIndent( true );
    $package->startElement('IRenvelope');
      $package->writeAttribute('xmlns', 'http://www.govtalk.gov.uk/taxation/charities/r68/1');
      $package->startElement('IRheader');
        $package->startElement('Keys');
          $package->startElement('Key');
            $package->writeAttribute('Type', 'CHARID');
            $package->text($cChardId);
          $package->endElement(); # Key
        $package->endElement(); # Keys
        $package->writeElement('PeriodEnd', $dReturnPeriod);
        $package->writeElement('DefaultCurrency', $sDefaultCurrency );
        $package->writeElement('IRmark', $dReturnPeriod);
          $package->writeAttribute('Type', 'generic');
          $package->text( $sIRmark );
        $package->endElement(); 
        $package->writeElement('Sender', $sSender);
        $package->endElement(); 
      $package->endElement(); #IRheader
      $package->startElement('R68');
        $package->startElement('AuthOfficial');
          $package->startElement('OffName');
            $package->writeElement( 'Fore', $cAuthOffForename);
            $package->writeElement( 'Sur' , $cAuthOffSurname);
          $package->endElement(); #OffName
          $package->startElement('OffID');
            $package->writeElement( 'Postcode', $cAuthOffPostcode);
          $package->endElement(); #OffID
          $package->writeElement( 'Phone', $cAuthOffPhone);        
        $package->endElement(); #AuthOfficial
        $package->writeElement( 'Declaration', $cDeclaration);        
        $this->build_claim_xml( $pBatchId, $package );
      $package->endElement(); #R68
    $package->endElement(); #IRenvelope
    
	  // Send the message and deal with the response...
    $this->setMessageBody( $package );

    if ($this->sendMessage() && ($this->responseHasErrors() === false)) {
      $returnable = $this->getResponseEndpoint();
      $returnable['correlationid'] = $this->getResponseCorrelationId();
      return $returnable;
    } else {
      return false;
    }
  }
  	
	/**
	 * Polls the Gateway for a submission response / error following a VAT
	 * declaration request. By default the correlation ID from the last response
	 * is used for the polling, but this can be over-ridden by supplying a
	 * correlation ID. The correlation ID can be skipped by passing a null value.
	 *
	 * If the resource is still pending this method will return the same array
	 * as declarationRequest() -- 'endpoint', 'interval' and 'correlationid' --
	 * if not then it'll return lots of useful information relating to the return
	 * and payment of any VAT due in the following array format:
	 *
	 *  message => an array of messages ('Thank you for your submission', etc.).
	 *  accept_time => the time the submission was accepted by the HMRC server.
	 *  period => an array of information relating to the period of the return:
	 *    id => the period ID.
	 *    start => the start date of the period.
	 *    end => the end date of the period.
	 *  payment => an array of information relating to the payment of the return:
	 *    narrative => a string representation of the payment (generated by HMRC)
	 *    netvat => the net value due following this return.
	 *    payment => an array of information relating to the method of payment:
	 *      method => the method to be used to pay any money due, options are:
	 *        - nilpayment: no payment is due.
	 *        - repayment: a repayment from HMRC is due.
	 *        - directdebit: payment will be taken by previous direct debit.
	 *        - payment: payment should be made by alternative means.
	 *      additional => additional information relating to this payment.
	 *
	 * @param string $correlationId The correlation ID of the resource to poll. Can be skipped with a null value.
	 * @param string $pollUrl The URL of the Gateway to poll.
	 * @return mixed An array of details relating to the return and payment, or false on failure.
	 */
	public function declarationResponsePoll($correlationId = null, $pollUrl = null) {
	
		if ($correlationId === null) {
			$correlationId = $this->getResponseCorrelationId();
		}

		if ($this->setMessageCorrelationId($correlationId)) {
			if ($pollUrl !== null) {
				$this->setGovTalkServer($pollUrl);
			}
			$this->setMessageClass('HMRC-VAT-DEC');
			$this->setMessageQualifier('poll');
			$this->setMessageFunction('submit');
			$this->resetMessageKeys();
			$this->setMessageBody('');
			if ($this->sendMessage() && ($this->responseHasErrors() === false)) {
			
				$messageQualifier = (string) $this->_fullResponseObject->Header->MessageDetails->Qualifier;
				if ($messageQualifier == 'response') {
				
					$successResponse = $this->_fullResponseObject->Body->SuccessResponse;
					
					if (isset($successResponse->IRmarkReceipt)) {
						$irMarkReceipt = (string) $successResponse->IRmarkReceipt->Message;
					}
					
					$responseMessage = array();
					foreach ($successResponse->Message AS $message) {
						$responseMessage[] = (string) $message;
					}
					$responseAcceptedTime = strtotime($successResponse->AcceptedTime);
					
					$declarationResponse = $successResponse->ResponseData->VATDeclarationResponse;
					$declarationPeriod = array('id' => (string) $declarationResponse->Header->VATPeriod->PeriodId,
					                           'start' => strtotime($declarationResponse->Header->VATPeriod->PeriodStartDate),
					                           'end' => strtotime($declarationResponse->Header->VATPeriod->PeriodEndDate));

					$paymentDueDate = strtotime($declarationResponse->Body->PaymentDueDate);

               $paymentDetails = array('narrative' => (string) $declarationResponse->Body->PaymentNotification->Narrative,
					                        'netvat' => (string) $declarationResponse->Body->PaymentNotification->NetVAT);
               
					$paymentNotifcation = $successResponse->ResponseData->VATDeclarationResponse->Body->PaymentNotification;
					if (isset($paymentNotifcation->NilPaymentIndicator)) {
						$paymentDetails['payment'] = array('method' => 'nilpayment', 'additional' => null);
					} else if (isset($paymentNotifcation->RepaymentIndicator)) {
						$paymentDetails['payment'] = array('method' => 'repayment', 'additional' => null);
					} else if (isset($paymentNotifcation->DirectDebitPaymentStatus)) {
						$paymentDetails['payment'] = array('method' => 'directdebit', 'additional' => strtotime($paymentNotifcation->DirectDebitPaymentStatus->CollectionDate));
					} else if (isset($paymentNotifcation->PaymentRequest)) {
						$paymentDetails['payment'] = array('method' => 'payment', 'additional' => (string) $paymentNotifcation->PaymentRequest->DirectDebitInstructionStatus);
					}
					
					return array('message' => $responseMessage,
					             'irmark' => $irMarkReceipt,
					             'accept_time' => $responseAcceptedTime,
					             'period' => $declarationPeriod,
					             'payment' => $paymentDetails);
					
				} else if ($messageQualifier == 'acknowledgement') {
					$returnable = $this->getResponseEndpoint();
					$returnable['correlationid'] = $this->getResponseCorrelationId();
					return $returnable;
				} else {
					return false;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
	
	}
	
 /* Protected methods. */

	/**
	 * Adds a valid IRmark to the given package.
	 *
	 * This function over-rides the packageDigest() function provided in the main
	 * php-govtalk class.
	 *
	 * @param string $package The package to add the IRmark to.
	 * @return string The new package after addition of the IRmark.
	 */
	protected function packageDigest( $package ) {
	
		if ($this->_generateIRmark === true) {
			$packageSimpleXML = simplexml_load_string( $package );
			$packageNamespaces = $packageSimpleXML->getNamespaces();
			
			preg_match('/<Body>(.*?)<\/Body>/', str_replace("\n", '�', $package), $matches);
			$packageBody = str_replace('�', "\n", $matches[1]);
			
			$irMark = base64_encode($this->_generateIRMark($packageBody, $packageNamespaces));
			$package = str_replace('IRmark+Token', $irMark, $package);
		}
		
		return $package;
	
	}

 /* Private methods. */
 
	/**
	 * Generates an IRmark hash from the given XML string for use in the IRmark
	 * node inside the message body.  The string passed must contain one IRmark
	 * element containing the string IRmark (ie. <IRmark>IRmark</IRmark>) or the
	 * function will fail.
	 *
	 * @param $xmlString string The XML to generate the IRmark hash from.
	 * @return string The IRmark hash.
	 */
	private function _generateIRMark($xmlString, $namespaces = null) {
	
		if (is_string($xmlString)) {
			$xmlString = preg_replace('/<(vat:)?IRmark Type="generic">[A-Za-z0-9\/\+=]*<\/(vat:)?IRmark>/', '', $xmlString, -1, $matchCount);
			if ($matchCount == 1) {
				$xmlDom = new DOMDocument;
				
				if ($namespaces !== null && is_array($namespaces)) {
					$namespaceString = array();
					foreach ($namespaces AS $key => $value) {
						if ($key !== '') {
							$namespaceString[] = 'xmlns:'.$key.'="'.$value.'"';
						} else {
							$namespaceString[] = 'xmlns="'.$value.'"';
						}
					}
					$bodyCompiled = '<Body '.implode(' ', $namespaceString).'>'.$xmlString.'</Body>';
				} else {
					$bodyCompiled = '<Body>'.$xmlString.'</Body>';
				}
				$xmlDom->loadXML($bodyCompiled);

				return sha1($xmlDom->documentElement->C14N(), true);

			} else {
				return false;
			}
		} else {
			return false;
		}	
	}
}
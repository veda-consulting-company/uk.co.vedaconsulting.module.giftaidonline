<?php

/*
 * GiftAidGovTalk - extends the GovTalk class with specifics for submiting
 * Gift Aid data to HMRC.
 *
 * Created by Long Luong on 13-03-2013.
 * Copyright 2013, Veda Consulting Limited. All rights reserved.
*/
require_once ( dirname(__FILE__) . '/GovTalk.php' );
class GiftAidGovTalk extends GovTalk {
	/**
	 * Sets the message CorrelationID for use in MessageDetails header.
	 *
	 * @param string $messageCorrelationId The correlation ID to set.
	 * @return boolean True if the CorrelationID is valid and set, false if it's invalid (and therefore not set).
	 * @see function getResponseCorrelationId
	 */
	public function setMessageCorrelationId( $messageCorrelationId = null ) {
    if ( empty( $messageCorrelationId ) ) {
      return true;
    }
    return parent::setMessageCorrelationId( $messageCorrelationId);
  }

}

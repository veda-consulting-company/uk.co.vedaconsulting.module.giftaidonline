INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'VENDOR_ID'
, '2355'
, 'The Vendor Id credential for communicating with HRMCR Gateway'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'SENDER_ID'
, null
, 'Your HMRC User ID (SDS Reference is Sender ID)'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'SENDER_VALUE'
, null
, 'Your HMRC User Password (SDS Reference is Sender Value)'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'CHAR_ID'
, null
, 'The Charitity Id credential for communicating with HRMCR Gateway. This is the XR number you would have been supplied by the HMRC.'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'CLAIMER_ORG_NAME'
, null
, 'Name of the Organisation that is submitting the claim'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'CLAIMER_ORG_HMRC_REF'
, null
, 'The Charitity Id credential for communicating with HRMCR Gateway. This is the XR number you would have been supplied by the HMRC. Same value as CHAR_ID.'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'CLAIMER_ORG_REGULATOR_NAME'
, null
, 'Abbereviated name of the Regulator belonging the Claiment organisation, for example Charity Comission of England and Wales is CCEW.'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'CLAIMER_ORG_REGULATOR_NO'
, null
, 'The Regulator Number belonging the Claiment organisation'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'MODE'
, 'live'
, 'Are we in Live or Dev Mode'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'PERIOD_END'
, '2013-03-31'
, 'The period end date of the current claim (set to YYYY-DD-MM format)'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'SENDER_TYPE'
, 'Individual'
, 'Should be either Individual or Organisation'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'AUTH_OFF_SURNAME'
, ''
, 'Surname of the Authorising Officer for your organisation'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'AUTH_OFF_FORENAME'
, ''
, 'Forename of the Authorising Officer for your organisation'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'AUTH_OFF_PHONE'
, ''
, 'Phone Number of the Authorising Officer for your organisation'
);

INSERT INTO `civicrm_gift_aid_submission_setting` 
( `name`
, `value`
, `description`
) VALUES 
( 'AUTH_OFF_POSTCODE'
, ''
, 'Postcode of the Authorising Officer for your organisation e.g. EC2A 3AY'
);

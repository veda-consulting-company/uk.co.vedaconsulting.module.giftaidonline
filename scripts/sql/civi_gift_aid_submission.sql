CREATE TABLE IF NOT EXISTS `civicrm_gift_aid_submission` (
`id`                        int(10) unsigned NOT NULL auto_increment,
`batch_id`                  int(10) unsigned NOT NULL,
`created_date`              timestamp        DEFAULT CURRENT_TIMESTAMP,
`request_xml`               longtext         NOT NULL,
`response_xml`              longtext         NOT NULL,
`response_status`           varchar(100)     NOT NULL,
PRIMARY KEY  (`id`),
KEY `batch_id` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

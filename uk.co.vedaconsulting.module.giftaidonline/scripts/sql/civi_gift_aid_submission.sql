CREATE TABLE IF NOT EXISTS `civicrm_gift_aid_submission` (
`id`                          int(10) unsigned NOT NULL auto_increment,
`batch_id`                    int(10) unsigned NOT NULL,
`created_date`                timestamp        DEFAULT CURRENT_TIMESTAMP,
`request_xml`                 longtext         NOT NULL,
`response_xml`                longtext         NOT NULL,
`response_qualifier`          varchar(50),
`response_errors`             longtext,
`response_end_point`          longtext,
`response_end_point_interval` int(3),
`response_correlation_id`     varchar(255),
`transaction_id`              varchar(255),
`IRMark`                      varchar(255),
`gateway_timestamp`           timestamp,
PRIMARY KEY  (`id`),
KEY `batch_id` (`batch_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

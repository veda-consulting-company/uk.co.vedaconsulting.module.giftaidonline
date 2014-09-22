CREATE TABLE IF NOT EXISTS `civicrm_gift_aid_submission_setting` (
`id`                        int(10) unsigned NOT NULL auto_increment,
`name`                      varchar(100)     NOT NULL,
`value`                     longtext,
`description`               longtext,
PRIMARY KEY  (`id`),
KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

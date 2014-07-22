CREATE TABLE IF NOT EXISTS `civi_gift_aid_rejected_contributions` (
`id`                          int(10) unsigned NOT NULL auto_increment,
`batch_id`                    int(10) unsigned NOT NULL,
`contribution_id`             int(10) unsigned NOT NULL,
`created_date`                timestamp        DEFAULT CURRENT_TIMESTAMP,
`rejection_reason`            varchar(255)      NOT NULL,
PRIMARY KEY  (`id`),
KEY `git_aid_rejections_contribution_id` (`contribution_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;

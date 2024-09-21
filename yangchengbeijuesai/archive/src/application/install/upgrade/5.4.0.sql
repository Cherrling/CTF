ALTER TABLE `catfish_posts` ADD `zuozhe` VARCHAR( 60 ) NOT NULL DEFAULT '' AFTER `post_source` ;
ALTER TABLE `catfish_posts` ADD `bianji` VARCHAR( 60 ) NOT NULL DEFAULT '' AFTER `zuozhe` ;
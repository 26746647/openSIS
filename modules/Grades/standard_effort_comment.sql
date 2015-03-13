CREATE TABLE IF NOT EXISTS `course_standards` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `syear` int(8) NOT NULL,
  `course_id` int(8) NOT NULL,
  `school_id` int(8) NOT NULL,
  `title` text NOT NULL,
  `sort_order` int(8) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


ALTER TABLE `course_standards` DROP `sort_order` ;
ALTER TABLE `course_standards` CHANGE `title` `standard_id` INT NOT NULL ;
ALTER TABLE `course_standards` ADD `standard_type` VARCHAR( 50 ) NOT NULL ;

--ALTER TABLE `course_periods` ADD `use_standards` ENUM( 'Y', 'N' ) NOT NULL DEFAULT 'N';
ALTER TABLE `course_periods` CHANGE `use_standards` `use_standards` VARCHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_general_ci 
NULL DEFAULT NULL ;
ALTER TABLE `course_periods` ADD `standard_scale_id` INT NULL DEFAULT '0';
ALTER TABLE `course_periods` CHANGE `standard_scale_id` `standard_scale_id` INT( 11 ) NULL DEFAULT NULL ;

CREATE TABLE IF NOT EXISTS `student_standards` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `student_id` int(8) NOT NULL,
  `course_period_id` int(8) NOT NULL,
  `course_standard_id` int(8) DEFAULT NULL,
  `grade_id` int(8) DEFAULT NULL,
  `marking_period_id` int(8) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `report_card_grade_scales` ADD `standard_grade_scale` TEXT NULL DEFAULT NULL ;

CREATE TABLE `us_common_core_standards` (
`standard_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`subject` VARCHAR( 500 ) NULL DEFAULT NULL ,
`grade` VARCHAR( 200 ) NULL DEFAULT NULL ,
`course` VARCHAR( 500 ) NULL DEFAULT NULL ,
`domain` VARCHAR( 500 ) NULL DEFAULT NULL ,
`topic` VARCHAR( 500 ) NULL DEFAULT NULL ,
`standard_ref_no` VARCHAR( 300 ) NOT NULL ,
`standard_details` LONGTEXT NULL DEFAULT NULL
) ENGINE = InnoDB;


CREATE TABLE `school_specific_standards` (
`standard_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`course_id` INT NOT NULL ,
`domain` VARCHAR( 500 ) NULL DEFAULT NULL ,
`topic` VARCHAR( 500 ) NULL DEFAULT NULL ,
`standard_ref_no` VARCHAR( 300 ) NOT NULL ,
`standard_details` LONGTEXT NULL DEFAULT NULL
) ENGINE = InnoDB;

ALTER TABLE `school_specific_standards` ADD `grade` INT NOT NULL AFTER `course_id` ;
ALTER TABLE `school_specific_standards` CHANGE `grade` `grade` INT( 11 ) NULL DEFAULT NULL ;

CREATE TABLE  `report_card_comment` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`syear` DECIMAL( 4, 0 ) NOT NULL ,
`school_id` INT NOT NULL ,
`student_id` INT NOT NULL ,
`posted_by` INT NOT NULL ,
`comment` LONGTEXT NULL DEFAULT NULL
) ENGINE = INNODB;

ALTER TABLE `report_card_comment` ADD `marking_period_id` INT NOT NULL AFTER `school_id`; 
ALTER TABLE  `report_card_comment` ADD  `modified_time` TIMESTAMP NOT NULL AFTER  `posted_by`;


CREATE TABLE IF NOT EXISTS `effort_grade_scales` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`syear` INT NOT NULL ,
`school_id` INT NOT NULL ,
`value` VARCHAR( 100 ) NOT NULL ,
`comment` VARCHAR( 300 ) NULL DEFAULT NULL ,
`sort_order` INT NOT NULL
) ENGINE = InnoDB;

CREATE TABLE `effort_grades` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`syear` INT NOT NULL ,
`school_id` INT NOT NULL ,
`title` VARCHAR( 300 ) NOT NULL ,
`short_name` VARCHAR( 100 ) NULL DEFAULT NULL ,
`sort_order` INT NULL DEFAULT NULL
) ENGINE = InnoDB;

CREATE TABLE `student_efforts` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`student_id` INT NOT NULL ,
`marking_period_id` INT NOT NULL ,
`effort_value` INT NOT NULL ,
`grade_value` INT NOT NULL
) ENGINE = InnoDB;

ALTER TABLE `student_efforts` CHANGE `grade_value` `grade_value` INT( 11 ) NULL DEFAULT NULL ;


CREATE TABLE `effort_grade_categories` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`title` VARCHAR( 300 ) NOT NULL ,
`sort_order` INT NULL DEFAULT NULL ,
`school_id` INT NOT NULL ,
`syear` INT NOT NULL
) ENGINE = InnoDB;


ALTER TABLE `effort_grades` ADD `effort_cat` INT NOT NULL AFTER `school_id` ;

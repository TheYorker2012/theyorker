CREATE TABLE `crossword_layouts` (
	`crossword_layout_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`crossword_layout_name` VARCHAR( 32 ) NOT NULL ,
	`crossword_layout_description` TEXT NOT NULL ,
	UNIQUE (
		`crossword_layout_name` 
	)
) ENGINE = INNODB COMMENT = 'Layout of crosswords in html';

CREATE TABLE `crossword_categories` (
	`crossword_category_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`crossword_category_name` VARCHAR( 255 ) NOT NULL ,
	`crossword_category_short_name` VARCHAR( 32 ) NOT NULL COMMENT 'URI Compatible',
	`crossword_category_default_width` TINYINT UNSIGNED NOT NULL ,
	`crossword_category_default_height` TINYINT UNSIGNED NOT NULL ,
	`crossword_category_default_layout_id` INT NOT NULL ,
	`crossword_category_default_has_normal_clues` TINYINT( 1 ) NOT NULL ,
	`crossword_category_default_has_cryptic_clues` TINYINT( 1 ) NOT NULL ,
	`crossword_category_default_winners` INT UNSIGNED NOT NULL ,
	UNIQUE (
		`crossword_category_short_name` 
	),
	FOREIGN KEY (
		`crossword_category_default_layout_id`
	) REFERENCES `crossword_layouts`(
		`crossword_layout_id`
	)
) ENGINE = INNODB COMMENT = 'Public categories of crosswords';

CREATE TABLE `crosswords` (
	`crossword_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`crossword_width` TINYINT UNSIGNED NOT NULL ,
	`crossword_height` TINYINT UNSIGNED NOT NULL ,
	`crossword_has_normal_clues` TINYINT( 1 ) NOT NULL ,
	`crossword_has_cryptic_clues` TINYINT( 1 ) NOT NULL ,
	`crossword_completeness` TINYINT UNSIGNED NOT NULL COMMENT '[0,100]',
	`crossword_category_id` INT NOT NULL ,
	`crossword_layout_id` INT NOT NULL ,
	`crossword_deadline` TIMESTAMP NULL ,
	`crossword_publication` TIMESTAMP NULL ,
	`crossword_expiry` TIMESTAMP NULL ,
	`crossword_winners` INT UNSIGNED NOT NULL ,
	`crossword_public_comment_thread_id` INT NOT NULL,
	FOREIGN KEY (
		`crossword_category_id`
	) REFERENCES `crossword_categories` (
		`crossword_category_id`
	),
	FOREIGN KEY (
		`crossword_layout_id`
	) REFERENCES `crossword_layouts` (
		`crossword_layout_id`
	),
	FOREIGN KEY (
		`crossword_public_comment_thread_id`
	) REFERENCES `comment_threads` (
		`comment_thread_id`
	)
) ENGINE = INNODB COMMENT = 'Individual crossword information';

CREATE TABLE `crossword_authors` (
	`crossword_author_crossword_id` INT NOT NULL ,
	`crossword_author_user_entity_id` INT NOT NULL ,
	PRIMARY KEY (
		`crossword_author_crossword_id` ,
		`crossword_author_user_entity_id`
	),
	FOREIGN KEY (
		`crossword_author_crossword_id`
	) REFERENCES `crosswords` (
		`crossword_id`
	)
) ENGINE = INNODB COMMENT = 'Crossword-author link table';

CREATE TABLE `crossword_tip_categories` (
	`crossword_tip_category_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`crossword_tip_category_name` VARCHAR( 255 ) NOT NULL ,
	`crossword_tip_category_description` TEXT NOT NULL 
) ENGINE = INNODB COMMENT = 'Categories in which to group tips';

CREATE TABLE `crossword_tips` (
	`crossword_tip_id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
	`crossword_tip_category_id` INT NOT NULL ,
	`crossword_tip_crossword_id` INT NOT NULL COMMENT 'Crossword to publish with',
	`crossword_tip_content_wikitext` TEXT NOT NULL ,
	`crossword_tip_content_xml` TEXT NOT NULL COMMENT 'Cache of comment_tip_content_wikitext',
	FOREIGN KEY (
		`crossword_tip_category_id`
	) REFERENCES `crossword_tip_categories` (
		`crossword_tip_category_id`
	),
	FOREIGN KEY (
		`crossword_tip_crossword_id`
	) REFERENCES `crosswords` (
		`crossword_id`
	)
) ENGINE = INNODB COMMENT = 'Tips published with crosswords';

CREATE TABLE `crossword_winners` (
	`crossword_winner_crossword_id` INT NOT NULL AUTO_INCREMENT ,
	`crossword_winner_user_entity_id` INT NOT NULL ,
	`crossword_winner_time` TIMESTAMP NOT NULL COMMENT 'Time of completion',
	PRIMARY KEY ( `crossword_winner_crossword_id` , `crossword_winner_user_entity_id` ) ,
	FOREIGN KEY (
		`crossword_winner_crossword_id`
	) REFERENCES `crosswords` (
		`crossword_id`
	)
) ENGINE = INNODB COMMENT = 'Winners of crosswords';

CREATE TABLE `crossword_lights` (
	`crossword_light_crossword_id` INT NOT NULL AUTO_INCREMENT ,
	`crossword_light_posx` TINYINT UNSIGNED NOT NULL,
	`crossword_light_posy` TINYINT UNSIGNED NOT NULL,
	`crossword_light_orientation` ENUM ('horizontal','vertical') NOT NULL,
	`crossword_light_solution` VARCHAR(255) NOT NULL,
	`crossword_light_normal_clue` VARCHAR(255) NULL,
	`crossword_light_cryptic_clue` VARCHAR(255) NULL,
	PRIMARY KEY (
		`crossword_light_crossword_id`,
		`crossword_light_posx`,
		`crossword_light_posy`,
		`crossword_light_orientation`
	),
	FOREIGN KEY (
		`crossword_light_crossword_id`
	) REFERENCES `crosswords` (
		`crossword_id`
	)
) ENGINE = INNODB COMMENT = 'Lights in crosswords';

CREATE TABLE `crossword_saves` (
	`crossword_save_id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
	`crossword_save_crossword_id` INT NOT NULL,
	`crossword_save_user_entity_id` INT NOT NULL,
	`crossword_save_time` TIMESTAMP NOT NULL,
	FOREIGN KEY (
		`crossword_save_crossword_id`
	) REFERENCES `crosswords` (
		`crossword_id`
	)
) ENGINE = INNODB COMMENT = 'User crossword saves';

CREATE TABLE `crossword_light_saves` (
	`crossword_light_save_save_id` INT NOT NULL,
	`crossword_light_save_posx` TINYINT UNSIGNED NOT NULL,
	`crossword_light_save_posy` TINYINT UNSIGNED NOT NULL,
	`crossword_light_save_orientation` ENUM ('horizontal','vertical') NOT NULL,
	`crossword_light_save_answer` VARCHAR(255) NOT NULL COMMENT 'May include hyphens, spaces indicate empty cells',
	PRIMARY KEY (
		`crossword_light_save_save_id`,
		`crossword_light_save_posx`,
		`crossword_light_save_posy`,
		`crossword_light_save_orientation`
	)
) ENGINE = INNODB COMMENT = 'Individual lights saved';


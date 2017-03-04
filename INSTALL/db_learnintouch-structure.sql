set foreign_key_checks = 0;

DROP TABLE IF EXISTS `address`;
CREATE TABLE `address` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_box` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `admin`;
CREATE TABLE `admin` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `login` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `password_salt` varchar(50) NOT NULL,
  `super_admin` bit(1) NOT NULL,
  `preference_admin` bit(1) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `profile` text,
  `post_login_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `admin_module`;
CREATE TABLE `admin_module` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `module` varchar(50) NOT NULL,
  `admin_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `admin_module_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`)
);


DROP TABLE IF EXISTS `admin_option`;
CREATE TABLE `admin_option` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `admin_id` bigint(20) unsigned NOT NULL,
  `value` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `admin_option_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`)
);


DROP TABLE IF EXISTS `client`;
CREATE TABLE `client` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `contact`;
CREATE TABLE `contact` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `contact_datetime` datetime DEFAULT NULL,
  `contact_status_id` bigint(20) unsigned DEFAULT NULL,
  `contact_referer_id` bigint(20) unsigned DEFAULT NULL,
  `garbage` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `contact_status_id` (`contact_status_id`),
  KEY `contact_referer_id` (`contact_referer_id`),
  CONSTRAINT `contact_ibfk_1` FOREIGN KEY (`contact_status_id`) REFERENCES `contact_status` (`id`),
  CONSTRAINT `contact_ibfk_2` FOREIGN KEY (`contact_referer_id`) REFERENCES `contact_referer` (`id`)
);


DROP TABLE IF EXISTS `contact_referer`;
CREATE TABLE `contact_referer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `description` text,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `contact_status`;
CREATE TABLE `contact_status` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `container`;
CREATE TABLE `container` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `content` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `content_import`;
CREATE TABLE `content_import` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `is_importing` bit(1) NOT NULL,
  `is_exporting` bit(1) NOT NULL,
  `permission_key` varchar(10) DEFAULT NULL,
  `permission_status` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `content_import_history`;
CREATE TABLE `content_import_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `domain_name` varchar(255) NOT NULL,
  `course` varchar(255) DEFAULT NULL,
  `lesson` varchar(255) DEFAULT NULL,
  `exercise` varchar(255) DEFAULT NULL,
  `import_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `document`;
CREATE TABLE `document` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `hide` bit(1) NOT NULL,
  `secured` bit(1) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `document_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `document_category` (`id`)
);


DROP TABLE IF EXISTS `document_category`;
CREATE TABLE `document_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_answer`;
CREATE TABLE `elearning_answer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `answer` varchar(255) DEFAULT NULL,
  `explanation` text,
  `image` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `elearning_question_id` bigint(20) unsigned NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_question_id` (`elearning_question_id`),
  CONSTRAINT `elearning_answer_ibfk_1` FOREIGN KEY (`elearning_question_id`) REFERENCES `elearning_question` (`id`)
);


DROP TABLE IF EXISTS `elearning_assignment`;
CREATE TABLE `elearning_assignment` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `elearning_subscription_id` bigint(20) unsigned NOT NULL,
  `elearning_exercise_id` bigint(20) unsigned NOT NULL,
  `elearning_result_id` bigint(20) unsigned DEFAULT NULL,
  `only_once` bit(1) NOT NULL,
  `opening_date` datetime DEFAULT NULL,
  `closing_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elearning_subscription_id_2` (`elearning_subscription_id`,`elearning_exercise_id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_subscription_id` (`elearning_subscription_id`),
  KEY `elearning_exercise_id` (`elearning_exercise_id`),
  KEY `elearning_result_id` (`elearning_result_id`),
  CONSTRAINT `elearning_assignment_ibfk_1` FOREIGN KEY (`elearning_subscription_id`) REFERENCES `elearning_subscription` (`id`),
  CONSTRAINT `elearning_assignment_ibfk_2` FOREIGN KEY (`elearning_exercise_id`) REFERENCES `elearning_exercise` (`id`),
  CONSTRAINT `elearning_assignment_ibfk_3` FOREIGN KEY (`elearning_result_id`) REFERENCES `elearning_result` (`id`)
);


DROP TABLE IF EXISTS `elearning_category`;
CREATE TABLE `elearning_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_class`;
CREATE TABLE `elearning_class` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_course`;
CREATE TABLE `elearning_course` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `instant_correction` bit(1) NOT NULL,
  `instant_congratulation` bit(1) NOT NULL,
  `instant_solution` bit(1) NOT NULL,
  `importable` bit(1) NOT NULL,
  `locked` bit(1) NOT NULL,
  `secured` bit(1) NOT NULL,
  `free_samples` int(10) unsigned DEFAULT NULL,
  `auto_subscription` bit(1) NOT NULL,
  `auto_unsubscription` bit(1) NOT NULL,
  `interrupt_timed_out_exercise` bit(1) NOT NULL,
  `reset_exercise_answers` bit(1) NOT NULL,
  `exercise_only_once` bit(1) NOT NULL,
  `exercise_any_order` bit(1) NOT NULL,
  `save_result_option` varchar(50) DEFAULT NULL,
  `shuffle_questions` bit(1) NOT NULL,
  `shuffle_answers` bit(1) NOT NULL,
  `matter_id` bigint(20) unsigned NOT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `matter_id` (`matter_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `elearning_course_ibfk_1` FOREIGN KEY (`matter_id`) REFERENCES `elearning_matter` (`id`),
  CONSTRAINT `elearning_course_ibfk_2` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `elearning_course_info`;
CREATE TABLE `elearning_course_info` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `information` text,
  `list_order` int(10) unsigned NOT NULL,
  `elearning_course_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_course_id` (`elearning_course_id`),
  CONSTRAINT `elearning_course_info_ibfk_1` FOREIGN KEY (`elearning_course_id`) REFERENCES `elearning_course` (`id`)
);


DROP TABLE IF EXISTS `elearning_course_item`;
CREATE TABLE `elearning_course_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `elearning_course_id` bigint(20) unsigned NOT NULL,
  `elearning_exercise_id` bigint(20) unsigned DEFAULT NULL,
  `elearning_lesson_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `elearning_course_id_2` (`elearning_course_id`,`elearning_exercise_id`),
  UNIQUE KEY `elearning_course_id_3` (`elearning_course_id`,`elearning_lesson_id`),
  KEY `elearning_course_id` (`elearning_course_id`),
  KEY `elearning_exercise_id` (`elearning_exercise_id`),
  KEY `elearning_lesson_id` (`elearning_lesson_id`),
  CONSTRAINT `elearning_course_item_ibfk_1` FOREIGN KEY (`elearning_course_id`) REFERENCES `elearning_course` (`id`),
  CONSTRAINT `elearning_course_item_ibfk_2` FOREIGN KEY (`elearning_exercise_id`) REFERENCES `elearning_exercise` (`id`),
  CONSTRAINT `elearning_course_item_ibfk_3` FOREIGN KEY (`elearning_lesson_id`) REFERENCES `elearning_lesson` (`id`)
);


DROP TABLE IF EXISTS `elearning_exercise`;
CREATE TABLE `elearning_exercise` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `instructions` text,
  `introduction` text,
  `hide_introduction` bit(1) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `public_access` bit(1) NOT NULL,
  `max_duration` int(10) unsigned DEFAULT NULL,
  `release_date` datetime NOT NULL,
  `secured` bit(1) NOT NULL,
  `skip_exercise_introduction` bit(1) NOT NULL,
  `social_connect` bit(1) NOT NULL,
  `hide_solutions` bit(1) NOT NULL,
  `hide_progression_bar` bit(1) NOT NULL,
  `hide_page_tabs` bit(1) NOT NULL,
  `disable_next_page_tabs` bit(1) NOT NULL,
  `number_page_tabs` int(10) unsigned DEFAULT NULL,
  `hide_keyboard` bit(1) NOT NULL,
  `contact_page` bit(1) NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `webpage_id` varchar(255) DEFAULT NULL,
  `level_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  `scoring_id` bigint(20) unsigned DEFAULT NULL,
  `garbage` bit(1) NOT NULL,
  `locked` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `category_id` (`category_id`),
  KEY `level_id` (`level_id`),
  KEY `subject_id` (`subject_id`),
  KEY `scoring_id` (`scoring_id`),
  CONSTRAINT `elearning_exercise_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `elearning_category` (`id`),
  CONSTRAINT `elearning_exercise_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `elearning_level` (`id`),
  CONSTRAINT `elearning_exercise_ibfk_3` FOREIGN KEY (`subject_id`) REFERENCES `elearning_subject` (`id`),
  CONSTRAINT `elearning_exercise_ibfk_4` FOREIGN KEY (`scoring_id`) REFERENCES `elearning_scoring` (`id`)
);


DROP TABLE IF EXISTS `elearning_exercise_page`;
CREATE TABLE `elearning_exercise_page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `instructions` text,
  `text` text,
  `hide_text` bit(1) NOT NULL,
  `text_max_height` int(10) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `video` varchar(1024) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `question_type` varchar(50) DEFAULT NULL,
  `hint_placement` varchar(50) DEFAULT NULL,
  `elearning_exercise_id` bigint(20) unsigned NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_exercise_id` (`elearning_exercise_id`),
  CONSTRAINT `elearning_exercise_page_ibfk_1` FOREIGN KEY (`elearning_exercise_id`) REFERENCES `elearning_exercise` (`id`)
);


DROP TABLE IF EXISTS `elearning_lesson`;
CREATE TABLE `elearning_lesson` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text,
  `instructions` text,
  `image` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `introduction` text,
  `secured` bit(1) NOT NULL,
  `public_access` bit(1) NOT NULL,
  `release_date` datetime NOT NULL,
  `garbage` bit(1) NOT NULL,
  `locked` bit(1) NOT NULL,
  `lesson_model_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `level_id` bigint(20) unsigned DEFAULT NULL,
  `subject_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `lesson_model_id` (`lesson_model_id`),
  KEY `category_id` (`category_id`),
  KEY `level_id` (`level_id`),
  KEY `subject_id` (`subject_id`),
  CONSTRAINT `elearning_lesson_ibfk_1` FOREIGN KEY (`lesson_model_id`) REFERENCES `elearning_lesson_model` (`id`),
  CONSTRAINT `elearning_lesson_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `elearning_category` (`id`),
  CONSTRAINT `elearning_lesson_ibfk_3` FOREIGN KEY (`level_id`) REFERENCES `elearning_level` (`id`),
  CONSTRAINT `elearning_lesson_ibfk_4` FOREIGN KEY (`subject_id`) REFERENCES `elearning_subject` (`id`)
);


DROP TABLE IF EXISTS `elearning_lesson_heading`;
CREATE TABLE `elearning_lesson_heading` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` text,
  `list_order` int(10) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `elearning_lesson_model_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_lesson_model_id` (`elearning_lesson_model_id`),
  CONSTRAINT `elearning_lesson_heading_ibfk_1` FOREIGN KEY (`elearning_lesson_model_id`) REFERENCES `elearning_lesson_model` (`id`)
);


DROP TABLE IF EXISTS `elearning_lesson_model`;
CREATE TABLE `elearning_lesson_model` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `instructions` text,
  `locked` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_lesson_paragraph`;
CREATE TABLE `elearning_lesson_paragraph` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `headline` varchar(255) NOT NULL,
  `body` text,
  `image` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `video` varchar(1024) DEFAULT NULL,
  `video_url` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `elearning_lesson_id` bigint(20) unsigned NOT NULL,
  `elearning_lesson_heading_id` bigint(20) unsigned DEFAULT NULL,
  `elearning_exercise_id` bigint(20) unsigned DEFAULT NULL,
  `exercise_title` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_lesson_id` (`elearning_lesson_id`),
  KEY `elearning_lesson_heading_id` (`elearning_lesson_heading_id`),
  KEY `elearning_exercise_id` (`elearning_exercise_id`),
  CONSTRAINT `elearning_lesson_paragraph_ibfk_1` FOREIGN KEY (`elearning_lesson_id`) REFERENCES `elearning_lesson` (`id`),
  CONSTRAINT `elearning_lesson_paragraph_ibfk_2` FOREIGN KEY (`elearning_lesson_heading_id`) REFERENCES `elearning_lesson_heading` (`id`),
  CONSTRAINT `elearning_lesson_paragraph_ibfk_3` FOREIGN KEY (`elearning_exercise_id`) REFERENCES `elearning_exercise` (`id`)
);


DROP TABLE IF EXISTS `elearning_level`;
CREATE TABLE `elearning_level` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_matter`;
CREATE TABLE `elearning_matter` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_question`;
CREATE TABLE `elearning_question` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `question` text,
  `explanation` text,
  `elearning_exercise_page_id` bigint(20) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `audio` varchar(255) DEFAULT NULL,
  `hint` text,
  `points` int(10) unsigned DEFAULT NULL,
  `answer_nb_words` int(10) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_exercise_page_id` (`elearning_exercise_page_id`),
  CONSTRAINT `elearning_question_ibfk_1` FOREIGN KEY (`elearning_exercise_page_id`) REFERENCES `elearning_exercise_page` (`id`)
);


DROP TABLE IF EXISTS `elearning_question_result`;
CREATE TABLE `elearning_question_result` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `elearning_result_id` bigint(20) unsigned NOT NULL,
  `elearning_question_id` bigint(20) unsigned NOT NULL,
  `elearning_answer_id` bigint(20) unsigned DEFAULT NULL,
  `elearning_answer_text` text,
  `elearning_answer_order` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_result_id` (`elearning_result_id`),
  KEY `elearning_question_id` (`elearning_question_id`),
  KEY `elearning_answer_id` (`elearning_answer_id`),
  CONSTRAINT `elearning_question_result_ibfk_1` FOREIGN KEY (`elearning_result_id`) REFERENCES `elearning_result` (`id`),
  CONSTRAINT `elearning_question_result_ibfk_2` FOREIGN KEY (`elearning_question_id`) REFERENCES `elearning_question` (`id`),
  CONSTRAINT `elearning_question_result_ibfk_3` FOREIGN KEY (`elearning_answer_id`) REFERENCES `elearning_answer` (`id`)
);


DROP TABLE IF EXISTS `elearning_result`;
CREATE TABLE `elearning_result` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `elearning_exercise_id` bigint(20) unsigned DEFAULT NULL,
  `subscription_id` bigint(20) unsigned DEFAULT NULL,
  `exercise_datetime` datetime DEFAULT NULL,
  `exercise_elapsed_time` int(10) unsigned DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `message` text,
  `text_comment` text,
  `hide_comment` bit(1) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `nb_reading_questions` int(10) unsigned DEFAULT NULL,
  `nb_correct_reading_answers` int(10) unsigned DEFAULT NULL,
  `nb_incorrect_reading_answers` int(10) unsigned DEFAULT NULL,
  `nb_reading_points` int(10) unsigned DEFAULT NULL,
  `nb_writing_questions` int(10) unsigned DEFAULT NULL,
  `nb_correct_writing_answers` int(10) unsigned DEFAULT NULL,
  `nb_incorrect_writing_answers` int(10) unsigned DEFAULT NULL,
  `nb_writing_points` int(10) unsigned DEFAULT NULL,
  `nb_listening_questions` int(10) unsigned DEFAULT NULL,
  `nb_correct_listening_answers` int(10) unsigned DEFAULT NULL,
  `nb_incorrect_listening_answers` int(10) unsigned DEFAULT NULL,
  `nb_listening_points` int(10) unsigned DEFAULT NULL,
  `nb_not_answered` int(10) unsigned DEFAULT NULL,
  `nb_incorrect_answers` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_exercise_id` (`elearning_exercise_id`),
  KEY `subscription_id` (`subscription_id`),
  CONSTRAINT `elearning_result_ibfk_1` FOREIGN KEY (`elearning_exercise_id`) REFERENCES `elearning_exercise` (`id`),
  CONSTRAINT `elearning_result_ibfk_2` FOREIGN KEY (`subscription_id`) REFERENCES `elearning_subscription` (`id`)
);


DROP TABLE IF EXISTS `elearning_result_range`;
CREATE TABLE `elearning_result_range` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `upper_range` int(10) unsigned NOT NULL,
  `grade` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_scoring`;
CREATE TABLE `elearning_scoring` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `required_score` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_scoring_range`;
CREATE TABLE `elearning_scoring_range` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `upper_range` int(10) unsigned NOT NULL,
  `score` text,
  `advice` text,
  `proposal` text,
  `link_text` varchar(255) DEFAULT NULL,
  `link_url` varchar(255) DEFAULT NULL,
  `elearning_scoring_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_scoring_id` (`elearning_scoring_id`),
  CONSTRAINT `elearning_scoring_range_ibfk_1` FOREIGN KEY (`elearning_scoring_id`) REFERENCES `elearning_scoring` (`id`)
);


DROP TABLE IF EXISTS `elearning_session`;
CREATE TABLE `elearning_session` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `opening_date` datetime NOT NULL,
  `closing_date` datetime DEFAULT NULL,
  `closed` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_session_course`;
CREATE TABLE `elearning_session_course` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `elearning_session_id` bigint(20) unsigned NOT NULL,
  `elearning_course_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elearning_session_id_2` (`elearning_session_id`,`elearning_course_id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_session_id` (`elearning_session_id`),
  KEY `elearning_course_id` (`elearning_course_id`),
  CONSTRAINT `elearning_session_course_ibfk_1` FOREIGN KEY (`elearning_session_id`) REFERENCES `elearning_session` (`id`),
  CONSTRAINT `elearning_session_course_ibfk_2` FOREIGN KEY (`elearning_course_id`) REFERENCES `elearning_course` (`id`)
);


DROP TABLE IF EXISTS `elearning_solution`;
CREATE TABLE `elearning_solution` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `elearning_question_id` bigint(20) unsigned NOT NULL,
  `elearning_answer_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elearning_question_id_2` (`elearning_question_id`,`elearning_answer_id`),
  UNIQUE KEY `id` (`id`),
  KEY `elearning_question_id` (`elearning_question_id`),
  KEY `elearning_answer_id` (`elearning_answer_id`),
  CONSTRAINT `elearning_solution_ibfk_1` FOREIGN KEY (`elearning_question_id`) REFERENCES `elearning_question` (`id`),
  CONSTRAINT `elearning_solution_ibfk_2` FOREIGN KEY (`elearning_answer_id`) REFERENCES `elearning_answer` (`id`)
);


DROP TABLE IF EXISTS `elearning_subject`;
CREATE TABLE `elearning_subject` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `elearning_subscription`;
CREATE TABLE `elearning_subscription` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `session_id` bigint(20) unsigned DEFAULT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `class_id` bigint(20) unsigned DEFAULT NULL,
  `subscription_date` datetime DEFAULT NULL,
  `subscription_close` datetime DEFAULT NULL,
  `watch_live` bit(1) NOT NULL,
  `last_exercise_id` bigint(20) unsigned DEFAULT NULL,
  `last_exercise_page_id` bigint(20) unsigned DEFAULT NULL,
  `last_active` datetime DEFAULT NULL,
  `whiteboard` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `session_id` (`session_id`),
  KEY `course_id` (`course_id`),
  KEY `class_id` (`class_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `elearning_subscription_ibfk_2` FOREIGN KEY (`teacher_id`) REFERENCES `elearning_teacher` (`id`),
  CONSTRAINT `elearning_subscription_ibfk_3` FOREIGN KEY (`session_id`) REFERENCES `elearning_session` (`id`),
  CONSTRAINT `elearning_subscription_ibfk_4` FOREIGN KEY (`course_id`) REFERENCES `elearning_course` (`id`),
  CONSTRAINT `elearning_subscription_ibfk_5` FOREIGN KEY (`class_id`) REFERENCES `elearning_class` (`id`),
  CONSTRAINT `elearning_subscription_ibfk_6` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `elearning_teacher`;
CREATE TABLE `elearning_teacher` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `user_id_2` (`user_account_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `elearning_teacher_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `flash`;
CREATE TABLE `flash` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `filename` varchar(50) DEFAULT NULL,
  `width` varchar(10) DEFAULT NULL,
  `height` varchar(10) DEFAULT NULL,
  `bgcolor` varchar(10) DEFAULT NULL,
  `wddx` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `form`;
CREATE TABLE `form` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `instructions` text,
  `acknowledge` text,
  `webpage_id` varchar(255) DEFAULT NULL,
  `mail_subject` varchar(255) DEFAULT NULL,
  `mail_message` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `form_item`;
CREATE TABLE `form_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `text` text,
  `help` varchar(255) DEFAULT NULL,
  `default_value` varchar(50) DEFAULT NULL,
  `item_size` varchar(3) DEFAULT NULL,
  `maxlength` varchar(4) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `in_mail_address` bit(1) NOT NULL,
  `mail_list_id` bigint(20) unsigned DEFAULT NULL,
  `form_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `mail_list_id` (`mail_list_id`),
  KEY `form_id` (`form_id`),
  KEY `form_id_2` (`form_id`,`list_order`),
  CONSTRAINT `form_item_ibfk_1` FOREIGN KEY (`mail_list_id`) REFERENCES `mail_list` (`id`),
  CONSTRAINT `form_item_ibfk_2` FOREIGN KEY (`form_id`) REFERENCES `form` (`id`)
);


DROP TABLE IF EXISTS `form_item_value`;
CREATE TABLE `form_item_value` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `value` varchar(50) DEFAULT NULL,
  `text` text,
  `form_item_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `form_item_id` (`form_item_id`),
  CONSTRAINT `form_item_value_ibfk_1` FOREIGN KEY (`form_item_id`) REFERENCES `form_item` (`id`)
);


DROP TABLE IF EXISTS `form_valid`;
CREATE TABLE `form_valid` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `type` varchar(30) NOT NULL,
  `message` text,
  `boundary` varchar(20) DEFAULT NULL,
  `form_item_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `form_item_id` (`form_item_id`),
  CONSTRAINT `form_valid_ibfk_1` FOREIGN KEY (`form_item_id`) REFERENCES `form_item` (`id`)
);


DROP TABLE IF EXISTS `guestbook_entry`;
CREATE TABLE `guestbook_entry` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `body` text NOT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `publication_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `guestbook_entry_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `lexicon_entry`;
CREATE TABLE `lexicon_entry` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `explanation` text,
  `image` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `name` (`name`)
);


DROP TABLE IF EXISTS `link`;
CREATE TABLE `link` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `link_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `link_category` (`id`)
);


DROP TABLE IF EXISTS `link_category`;
CREATE TABLE `link_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `mail`;
CREATE TABLE `mail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext,
  `description` varchar(255) DEFAULT NULL,
  `text_format` bit(1) NOT NULL,
  `attachments` text,
  `creation_datetime` datetime DEFAULT NULL,
  `send_datetime` datetime DEFAULT NULL,
  `locked` bit(1) NOT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `mail_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  CONSTRAINT `mail_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `mail_category` (`id`)
);


DROP TABLE IF EXISTS `mail_address`;
CREATE TABLE `mail_address` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `text_comment` text,
  `country` varchar(255) DEFAULT NULL,
  `subscribe` bit(1) NOT NULL,
  `imported` bit(1) NOT NULL,
  `creation_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `mail_category`;
CREATE TABLE `mail_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `mail_history`;
CREATE TABLE `mail_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext,
  `description` varchar(255) DEFAULT NULL,
  `attachments` text,
  `mail_list_id` bigint(20) unsigned DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `send_datetime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `mail_list_id` (`mail_list_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `mail_history_ibfk_1` FOREIGN KEY (`mail_list_id`) REFERENCES `mail_list` (`id`),
  CONSTRAINT `mail_history_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`)
);


DROP TABLE IF EXISTS `mail_list`;
CREATE TABLE `mail_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `auto_subscribe` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `mail_list_address`;
CREATE TABLE `mail_list_address` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `mail_list_id` bigint(20) unsigned NOT NULL,
  `mail_address_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `mail_list_id` (`mail_list_id`),
  KEY `mail_address_id` (`mail_address_id`),
  CONSTRAINT `mail_list_address_ibfk_1` FOREIGN KEY (`mail_list_id`) REFERENCES `mail_list` (`id`),
  CONSTRAINT `mail_list_address_ibfk_2` FOREIGN KEY (`mail_address_id`) REFERENCES `mail_address` (`id`)
);


DROP TABLE IF EXISTS `mail_list_user`;
CREATE TABLE `mail_list_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `mail_list_id` bigint(20) unsigned DEFAULT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `mail_list_id` (`mail_list_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `mail_list_user_ibfk_1` FOREIGN KEY (`mail_list_id`) REFERENCES `mail_list` (`id`),
  CONSTRAINT `mail_list_user_ibfk_2` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `mail_outbox`;
CREATE TABLE `mail_outbox` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(20) DEFAULT NULL,
  `sent` bit(1) NOT NULL,
  `error_message` varchar(255) DEFAULT NULL,
  `meta_names` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `navbar`;
CREATE TABLE `navbar` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `hide` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `navbar_item`;
CREATE TABLE `navbar_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_over` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `blank_target` bit(1) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hide` bit(1) NOT NULL,
  `template_model_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `navbar_language_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_model_id` (`template_model_id`),
  KEY `navbar_language_id` (`navbar_language_id`),
  KEY `navbar_language_id_2` (`navbar_language_id`,`list_order`),
  KEY `image` (`image`),
  KEY `image_over` (`image_over`),
  CONSTRAINT `navbar_item_ibfk_1` FOREIGN KEY (`template_model_id`) REFERENCES `template_model` (`id`),
  CONSTRAINT `navbar_item_ibfk_2` FOREIGN KEY (`navbar_language_id`) REFERENCES `navbar_language` (`id`)
);


DROP TABLE IF EXISTS `navbar_language`;
CREATE TABLE `navbar_language` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `language_code` varchar(2) DEFAULT NULL,
  `navbar_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `navbar_id` (`navbar_id`),
  KEY `navbar_id_2` (`navbar_id`,`language_code`),
  CONSTRAINT `navbar_language_ibfk_1` FOREIGN KEY (`navbar_id`) REFERENCES `navbar` (`id`)
);


DROP TABLE IF EXISTS `navlink`;
CREATE TABLE `navlink` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `navlink_item`;
CREATE TABLE `navlink_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_over` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `blank_target` bit(1) NOT NULL,
  `language_code` varchar(2) DEFAULT NULL,
  `template_model_id` bigint(20) unsigned DEFAULT NULL,
  `navlink_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_model_id` (`template_model_id`),
  KEY `navlink_id` (`navlink_id`),
  KEY `navlink_id_2` (`navlink_id`,`language_code`),
  KEY `image` (`image`),
  KEY `image_over` (`image_over`),
  CONSTRAINT `navlink_item_ibfk_1` FOREIGN KEY (`template_model_id`) REFERENCES `template_model` (`id`),
  CONSTRAINT `navlink_item_ibfk_2` FOREIGN KEY (`navlink_id`) REFERENCES `navlink` (`id`)
);


DROP TABLE IF EXISTS `navmenu`;
CREATE TABLE `navmenu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `hide` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `navmenu_item`;
CREATE TABLE `navmenu_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_over` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `blank_target` bit(1) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hide` bit(1) NOT NULL,
  `template_model_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_model_id` (`template_model_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `navmenu_item_ibfk_1` FOREIGN KEY (`template_model_id`) REFERENCES `template_model` (`id`),
  CONSTRAINT `navmenu_item_ibfk_2` FOREIGN KEY (`parent_id`) REFERENCES `navmenu_item` (`id`)
);


DROP TABLE IF EXISTS `navmenu_language`;
CREATE TABLE `navmenu_language` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `language_code` varchar(2) DEFAULT NULL,
  `navmenu_id` bigint(20) unsigned NOT NULL,
  `navmenu_item_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `navmenu_id` (`navmenu_id`),
  KEY `navmenu_item_id` (`navmenu_item_id`),
  CONSTRAINT `navmenu_language_ibfk_1` FOREIGN KEY (`navmenu_id`) REFERENCES `navmenu` (`id`),
  CONSTRAINT `navmenu_language_ibfk_2` FOREIGN KEY (`navmenu_item_id`) REFERENCES `navmenu_item` (`id`)
);


DROP TABLE IF EXISTS `news_editor`;
CREATE TABLE `news_editor` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `admin_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `admin_id_2` (`admin_id`),
  UNIQUE KEY `id` (`id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `news_editor_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`)
);


DROP TABLE IF EXISTS `news_feed`;
CREATE TABLE `news_feed` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `news_paper_id` bigint(20) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `max_display_number` int(10) unsigned DEFAULT NULL,
  `image_align` varchar(10) DEFAULT NULL,
  `image_width` int(10) unsigned DEFAULT NULL,
  `with_excerpt` bit(1) NOT NULL,
  `with_image` bit(1) NOT NULL,
  `search_options` bit(1) NOT NULL,
  `search_calendar` bit(1) NOT NULL,
  `display_upcoming` bit(1) NOT NULL,
  `search_title` varchar(255) DEFAULT NULL,
  `search_display_as_page` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `news_heading`;
CREATE TABLE `news_heading` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `news_publication_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `news_publication_id` (`news_publication_id`),
  CONSTRAINT `news_heading_ibfk_1` FOREIGN KEY (`news_publication_id`) REFERENCES `news_publication` (`id`)
);


DROP TABLE IF EXISTS `news_paper`;
CREATE TABLE `news_paper` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `header` text,
  `footer` text,
  `release_date` datetime DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `not_published` bit(1) NOT NULL,
  `news_publication_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `news_publication_id` (`news_publication_id`),
  CONSTRAINT `news_paper_ibfk_1` FOREIGN KEY (`news_publication_id`) REFERENCES `news_publication` (`id`)
);


DROP TABLE IF EXISTS `news_publication`;
CREATE TABLE `news_publication` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `nb_columns` int(10) unsigned DEFAULT NULL,
  `slide_down` bit(1) NOT NULL,
  `align` varchar(10) DEFAULT NULL,
  `with_archive` bit(1) NOT NULL,
  `with_others` bit(1) NOT NULL,
  `with_by_heading` bit(1) NOT NULL,
  `hide_heading` bit(1) NOT NULL,
  `auto_archive` int(3) unsigned DEFAULT NULL,
  `auto_delete` int(3) unsigned DEFAULT NULL,
  `secured` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `news_story`;
CREATE TABLE `news_story` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `headline` varchar(255) NOT NULL,
  `excerpt` text,
  `audio` varchar(255) DEFAULT NULL,
  `audio_url` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `release_date` datetime DEFAULT NULL,
  `archive_date` datetime DEFAULT NULL,
  `event_start_date` datetime DEFAULT NULL,
  `event_end_date` datetime DEFAULT NULL,
  `news_editor_id` bigint(20) unsigned DEFAULT NULL,
  `news_paper_id` bigint(20) unsigned NOT NULL,
  `news_heading_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `news_editor_id` (`news_editor_id`),
  KEY `news_paper_id` (`news_paper_id`),
  KEY `news_heading_id` (`news_heading_id`),
  CONSTRAINT `news_story_ibfk_1` FOREIGN KEY (`news_editor_id`) REFERENCES `news_editor` (`id`),
  CONSTRAINT `news_story_ibfk_2` FOREIGN KEY (`news_paper_id`) REFERENCES `news_paper` (`id`),
  CONSTRAINT `news_story_ibfk_3` FOREIGN KEY (`news_heading_id`) REFERENCES `news_heading` (`id`)
);


DROP TABLE IF EXISTS `news_story_image`;
CREATE TABLE `news_story_image` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `news_story_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `news_story_id` (`news_story_id`),
  CONSTRAINT `news_story_image_ibfk_1` FOREIGN KEY (`news_story_id`) REFERENCES `news_story` (`id`)
);


DROP TABLE IF EXISTS `news_story_paragraph`;
CREATE TABLE `news_story_paragraph` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `header` text,
  `body` text,
  `footer` text,
  `news_story_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `news_story_id` (`news_story_id`),
  CONSTRAINT `news_story_paragraph_ibfk_1` FOREIGN KEY (`news_story_id`) REFERENCES `news_story` (`id`)
);


DROP TABLE IF EXISTS `people`;
CREATE TABLE `people` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `work_phone` varchar(20) DEFAULT NULL,
  `mobile_phone` varchar(20) DEFAULT NULL,
  `profile` text,
  `image` varchar(255) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `people_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `people_category` (`id`)
);


DROP TABLE IF EXISTS `people_category`;
CREATE TABLE `people_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `photo`;
CREATE TABLE `photo` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `reference` varchar(50) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `text_comment` text,
  `image` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `price` int(10) unsigned DEFAULT NULL,
  `photo_album_id` bigint(20) unsigned DEFAULT NULL,
  `photo_format_id` bigint(20) unsigned DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `photo_album_id` (`photo_album_id`),
  KEY `photo_format_id` (`photo_format_id`),
  CONSTRAINT `photo_ibfk_1` FOREIGN KEY (`photo_album_id`) REFERENCES `photo_album` (`id`),
  CONSTRAINT `photo_ibfk_2` FOREIGN KEY (`photo_format_id`) REFERENCES `photo_format` (`id`)
);


DROP TABLE IF EXISTS `photo_album`;
CREATE TABLE `photo_album` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `folder_name` varchar(50) NOT NULL,
  `event` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `publication_date` datetime DEFAULT NULL,
  `price` int(10) unsigned DEFAULT NULL,
  `hide` bit(1) NOT NULL,
  `no_slide_show` bit(1) NOT NULL,
  `no_zoom` bit(1) NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `photo_album_format`;
CREATE TABLE `photo_album_format` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `photo_album_id` bigint(20) unsigned NOT NULL,
  `photo_format_id` bigint(20) unsigned NOT NULL,
  `price` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `photo_album_id` (`photo_album_id`),
  KEY `photo_format_id` (`photo_format_id`),
  CONSTRAINT `photo_album_format_ibfk_1` FOREIGN KEY (`photo_album_id`) REFERENCES `photo_album` (`id`),
  CONSTRAINT `photo_album_format_ibfk_2` FOREIGN KEY (`photo_format_id`) REFERENCES `photo_format` (`id`)
);


DROP TABLE IF EXISTS `photo_format`;
CREATE TABLE `photo_format` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `price` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `preference`;
CREATE TABLE `preference` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text,
  `type` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `name_2` (`name`)
);


DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `name_2` (`name`)
);


DROP TABLE IF EXISTS `property`;
CREATE TABLE `property` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `name_2` (`name`)
);


DROP TABLE IF EXISTS `rss_feed`;
CREATE TABLE `rss_feed` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `rss_feed_language`;
CREATE TABLE `rss_feed_language` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `language_code` varchar(2) DEFAULT NULL,
  `title` varchar(50) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `rss_feed_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `rss_feed_id` (`rss_feed_id`),
  CONSTRAINT `rss_feed_language_ibfk_1` FOREIGN KEY (`rss_feed_id`) REFERENCES `rss_feed` (`id`)
);


DROP TABLE IF EXISTS `shop_affiliate`;
CREATE TABLE `shop_affiliate` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `user_id_2` (`user_account_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `shop_affiliate_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `shop_category`;
CREATE TABLE `shop_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `shop_category_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `shop_category` (`id`)
);


DROP TABLE IF EXISTS `shop_discount`;
CREATE TABLE `shop_discount` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `discount_code` varchar(12) NOT NULL,
  `discount_rate` varchar(5) NOT NULL,
  `shop_affiliate_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `shop_affiliate_id` (`shop_affiliate_id`),
  CONSTRAINT `shop_discount_ibfk_1` FOREIGN KEY (`shop_affiliate_id`) REFERENCES `shop_affiliate` (`id`)
);


DROP TABLE IF EXISTS `shop_item`;
CREATE TABLE `shop_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `long_description` text,
  `reference` varchar(30) DEFAULT NULL,
  `weight` varchar(3) DEFAULT NULL,
  `price` varchar(12) DEFAULT NULL,
  `vat_rate` varchar(5) DEFAULT NULL,
  `shipping_fee` varchar(10) DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `hide` bit(1) NOT NULL,
  `added` datetime NOT NULL,
  `last_modified` datetime NOT NULL,
  `available` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `shop_item_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `shop_category` (`id`)
);


DROP TABLE IF EXISTS `shop_item_image`;
CREATE TABLE `shop_item_image` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `shop_item_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `shop_item_id` (`shop_item_id`),
  CONSTRAINT `shop_item_image_ibfk_1` FOREIGN KEY (`shop_item_id`) REFERENCES `shop_item` (`id`)
);


DROP TABLE IF EXISTS `shop_order`;
CREATE TABLE `shop_order` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `vat_number` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `mobile_phone` varchar(20) DEFAULT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `message` text,
  `handling_fee` int(10) unsigned DEFAULT NULL,
  `discount_code` varchar(12) DEFAULT NULL,
  `discount_amount` varchar(10) DEFAULT NULL,
  `currency` varchar(3) NOT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_note` varchar(1024) DEFAULT NULL,
  `invoice_language_code` varchar(2) DEFAULT NULL,
  `invoice_address_id` bigint(20) unsigned NOT NULL,
  `shipping_address_id` bigint(20) unsigned DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `due_date` datetime NOT NULL,
  `client_ip` varchar(20) NOT NULL,
  `status` varchar(10) NOT NULL,
  `payment_type` varchar(10) NOT NULL,
  `payment_transaction_id` varchar(50) DEFAULT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `invoice_address_id` (`invoice_address_id`),
  KEY `shipping_address_id` (`shipping_address_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `shop_order_ibfk_1` FOREIGN KEY (`invoice_address_id`) REFERENCES `address` (`id`),
  CONSTRAINT `shop_order_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `address` (`id`),
  CONSTRAINT `shop_order_ibfk_3` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `shop_order_item`;
CREATE TABLE `shop_order_item` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `reference` varchar(30) DEFAULT NULL,
  `price` varchar(12) NOT NULL,
  `vat_rate` varchar(5) DEFAULT NULL,
  `shipping_fee` varchar(10) DEFAULT NULL,
  `quantity` int(2) unsigned NOT NULL,
  `is_gift` bit(1) NOT NULL,
  `options` varchar(255) DEFAULT NULL,
  `shop_order_id` bigint(20) unsigned NOT NULL,
  `shop_item_id` bigint(20) unsigned DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `shop_order_id` (`shop_order_id`),
  KEY `shop_item_id` (`shop_item_id`),
  CONSTRAINT `shop_order_item_ibfk_1` FOREIGN KEY (`shop_order_id`) REFERENCES `shop_order` (`id`),
  CONSTRAINT `shop_order_item_ibfk_2` FOREIGN KEY (`shop_item_id`) REFERENCES `shop_item` (`id`)
);


DROP TABLE IF EXISTS `sms`;
CREATE TABLE `sms` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `body` text NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `admin_id` (`admin_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `sms_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`),
  CONSTRAINT `sms_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `sms_category` (`id`)
);


DROP TABLE IF EXISTS `sms_category`;
CREATE TABLE `sms_category` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `sms_history`;
CREATE TABLE `sms_history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `sms_id` bigint(20) unsigned NOT NULL,
  `sms_list_id` bigint(20) unsigned DEFAULT NULL,
  `mobile_phone` varchar(50) DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  `send_datetime` datetime DEFAULT NULL,
  `nb_recipients` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `sms_id` (`sms_id`),
  KEY `sms_list_id` (`sms_list_id`),
  KEY `admin_id` (`admin_id`),
  CONSTRAINT `sms_history_ibfk_1` FOREIGN KEY (`sms_id`) REFERENCES `sms` (`id`),
  CONSTRAINT `sms_history_ibfk_2` FOREIGN KEY (`sms_list_id`) REFERENCES `sms_list` (`id`),
  CONSTRAINT `sms_history_ibfk_3` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`)
);


DROP TABLE IF EXISTS `sms_list`;
CREATE TABLE `sms_list` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `auto_subscribe` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `sms_list_number`;
CREATE TABLE `sms_list_number` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `sms_list_id` bigint(20) unsigned NOT NULL,
  `sms_number_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `sms_list_id` (`sms_list_id`),
  KEY `sms_number_id` (`sms_number_id`),
  CONSTRAINT `sms_list_number_ibfk_1` FOREIGN KEY (`sms_list_id`) REFERENCES `sms_list` (`id`),
  CONSTRAINT `sms_list_number_ibfk_2` FOREIGN KEY (`sms_number_id`) REFERENCES `sms_number` (`id`)
);


DROP TABLE IF EXISTS `sms_list_user`;
CREATE TABLE `sms_list_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `sms_list_id` bigint(20) unsigned NOT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `sms_list_id` (`sms_list_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `sms_list_user_ibfk_1` FOREIGN KEY (`sms_list_id`) REFERENCES `sms_list` (`id`)
);


DROP TABLE IF EXISTS `sms_number`;
CREATE TABLE `sms_number` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `mobile_phone` varchar(20) NOT NULL,
  `subscribe` bit(1) NOT NULL,
  `imported` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `sms_outbox`;
CREATE TABLE `sms_outbox` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `mobile_phone` varchar(20) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(20) DEFAULT NULL,
  `sent` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `social_user`;
CREATE TABLE `social_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `facebook_user_id` varchar(12) DEFAULT NULL,
  `linkedin_user_id` varchar(48) DEFAULT NULL,
  `google_user_id` varchar(48) DEFAULT NULL,
  `twitter_user_id` varchar(48) DEFAULT NULL,
  `user_account_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `facebook_user_id` (`facebook_user_id`),
  UNIQUE KEY `linkedin_user_id` (`linkedin_user_id`),
  UNIQUE KEY `google_user_id` (`google_user_id`),
  UNIQUE KEY `twitter_user_id` (`twitter_user_id`),
  KEY `user_account_id` (`user_account_id`),
  CONSTRAINT `social_user_ibfk_1` FOREIGN KEY (`user_account_id`) REFERENCES `user_account` (`id`)
);


DROP TABLE IF EXISTS `statistics_page`;
CREATE TABLE `statistics_page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `page` varchar(100) NOT NULL,
  `hits` int(10) unsigned NOT NULL,
  `month` int(10) unsigned NOT NULL,
  `year` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `page` (`page`,`month`,`year`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `statistics_referer`;
CREATE TABLE `statistics_referer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `statistics_visit`;
CREATE TABLE `statistics_visit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `visit_datetime` datetime NOT NULL,
  `visitor_host_address` varchar(255) NOT NULL,
  `visitor_browser` varchar(255) NOT NULL,
  `visitor_referer` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `visit_datetime` (`visit_datetime`),
  KEY `visitor_host_address` (`visitor_host_address`),
  KEY `visitor_browser` (`visitor_browser`),
  KEY `visitor_referer` (`visitor_referer`)
);


DROP TABLE IF EXISTS `template_container`;
CREATE TABLE `template_container` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `template_model_id` bigint(20) unsigned NOT NULL,
  `row_nb` int(10) unsigned NOT NULL,
  `cell_nb` int(10) unsigned NOT NULL,
  `template_property_set_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_model_id` (`template_model_id`),
  KEY `template_property_set_id` (`template_property_set_id`),
  CONSTRAINT `template_container_ibfk_1` FOREIGN KEY (`template_model_id`) REFERENCES `template_model` (`id`),
  CONSTRAINT `template_container_ibfk_2` FOREIGN KEY (`template_property_set_id`) REFERENCES `template_property_set` (`id`)
);


DROP TABLE IF EXISTS `template_element`;
CREATE TABLE `template_element` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `element_type` varchar(50) NOT NULL,
  `object_id` bigint(20) unsigned DEFAULT NULL,
  `template_container_id` bigint(20) unsigned NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `hide` bit(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_container_id` (`template_container_id`),
  CONSTRAINT `template_element_ibfk_1` FOREIGN KEY (`template_container_id`) REFERENCES `template_container` (`id`)
);


DROP TABLE IF EXISTS `template_element_language`;
CREATE TABLE `template_element_language` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `language_code` varchar(2) DEFAULT NULL,
  `object_id` bigint(20) unsigned DEFAULT NULL,
  `template_element_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_element_id` (`template_element_id`),
  CONSTRAINT `template_element_language_ibfk_1` FOREIGN KEY (`template_element_id`) REFERENCES `template_element` (`id`)
);


DROP TABLE IF EXISTS `template_element_tag`;
CREATE TABLE `template_element_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `template_element_id` bigint(20) unsigned NOT NULL,
  `template_property_set_id` bigint(20) unsigned DEFAULT NULL,
  `dom_tag_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_element_id_2` (`template_element_id`,`dom_tag_id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_element_id` (`template_element_id`),
  KEY `template_property_set_id` (`template_property_set_id`),
  CONSTRAINT `template_element_tag_ibfk_1` FOREIGN KEY (`template_element_id`) REFERENCES `template_element` (`id`),
  CONSTRAINT `template_element_tag_ibfk_2` FOREIGN KEY (`template_property_set_id`) REFERENCES `template_property_set` (`id`)
);


DROP TABLE IF EXISTS `template_model`;
CREATE TABLE `template_model` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `model_type` varchar(50) NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `template_property_set_id` bigint(20) unsigned DEFAULT NULL,
  `inner_template_property_set_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `id` (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `template_property_set_id` (`template_property_set_id`),
  KEY `inner_template_property_set_id` (`inner_template_property_set_id`),
  CONSTRAINT `template_model_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `template_model` (`id`),
  CONSTRAINT `template_model_ibfk_2` FOREIGN KEY (`template_property_set_id`) REFERENCES `template_property_set` (`id`),
  CONSTRAINT `template_model_ibfk_3` FOREIGN KEY (`inner_template_property_set_id`) REFERENCES `template_property_set` (`id`)
);


DROP TABLE IF EXISTS `template_page`;
CREATE TABLE `template_page` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `system_page` varchar(50) NOT NULL,
  `template_model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_model_id` (`template_model_id`),
  CONSTRAINT `template_page_ibfk_1` FOREIGN KEY (`template_model_id`) REFERENCES `template_model` (`id`)
);


DROP TABLE IF EXISTS `template_page_tag`;
CREATE TABLE `template_page_tag` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `template_page_id` bigint(20) unsigned NOT NULL,
  `template_property_set_id` bigint(20) unsigned DEFAULT NULL,
  `dom_tag_id` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `template_page_id_2` (`template_page_id`,`dom_tag_id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_page_id` (`template_page_id`),
  KEY `template_property_set_id` (`template_property_set_id`),
  CONSTRAINT `template_page_tag_ibfk_1` FOREIGN KEY (`template_page_id`) REFERENCES `template_page` (`id`),
  CONSTRAINT `template_page_tag_ibfk_2` FOREIGN KEY (`template_property_set_id`) REFERENCES `template_property_set` (`id`)
);


DROP TABLE IF EXISTS `template_property`;
CREATE TABLE `template_property` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) DEFAULT NULL,
  `template_property_set_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `template_property_set_id` (`template_property_set_id`),
  CONSTRAINT `template_property_ibfk_1` FOREIGN KEY (`template_property_set_id`) REFERENCES `template_property_set` (`id`)
);


DROP TABLE IF EXISTS `template_property_set`;
CREATE TABLE `template_property_set` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `unique_token`;
CREATE TABLE `unique_token` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `value` varchar(50) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `expiration_datetime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `value` (`value`),
  UNIQUE KEY `id` (`id`)
);


DROP TABLE IF EXISTS `user_account`;
CREATE TABLE `user_account` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `organisation` varchar(255) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `fax` varchar(20) DEFAULT NULL,
  `home_phone` varchar(20) DEFAULT NULL,
  `work_phone` varchar(20) DEFAULT NULL,
  `mobile_phone` varchar(20) DEFAULT NULL,
  `password` varchar(100) NOT NULL,
  `password_salt` varchar(50) NOT NULL,
  `readable_password` varchar(50) DEFAULT NULL,
  `unconfirmed_email` bit(1) NOT NULL,
  `valid_until` datetime DEFAULT NULL,
  `last_login` datetime NOT NULL,
  `profile` text,
  `image` varchar(255) DEFAULT NULL,
  `imported` bit(1) NOT NULL,
  `mail_subscribe` bit(1) NOT NULL,
  `sms_subscribe` bit(1) NOT NULL,
  `creation_datetime` datetime NOT NULL,
  `address_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `id` (`id`),
  KEY `address_id` (`address_id`),
  KEY `image` (`image`),
  CONSTRAINT `user_account_ibfk_1` FOREIGN KEY (`address_id`) REFERENCES `address` (`id`)
);


DROP TABLE IF EXISTS `webpage`;
CREATE TABLE `webpage` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `content` longtext,
  `hide` bit(1) NOT NULL,
  `garbage` bit(1) NOT NULL,
  `list_order` int(10) unsigned NOT NULL,
  `secured` bit(1) NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `admin_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`),
  KEY `parent_id` (`parent_id`),
  KEY `admin_id` (`admin_id`),
  KEY `parent_id_2` (`parent_id`,`name`),
  KEY `parent_id_3` (`parent_id`,`list_order`),
  CONSTRAINT `webpage_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `webpage` (`id`),
  CONSTRAINT `webpage_ibfk_2` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`id`)
);


DROP TABLE IF EXISTS `webpage_navmenu`;
CREATE TABLE `webpage_navmenu` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `version` int(10) unsigned NOT NULL,
  `parent_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
);



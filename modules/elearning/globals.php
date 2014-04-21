<?PHP

// Name of the database table(s)
define('DB_TABLE_ELEARNING_ANSWER', "elearning_answer");
define('DB_TABLE_ELEARNING_CATEGORY', "elearning_category");
define('DB_TABLE_ELEARNING_MATTER', "elearning_matter");
define('DB_TABLE_ELEARNING_SUBJECT', "elearning_subject");
define('DB_TABLE_ELEARNING_LEVEL', "elearning_level");
define('DB_TABLE_ELEARNING_QUESTION', "elearning_question");
define('DB_TABLE_ELEARNING_EXERCISE_PAGE', "elearning_exercise_page");
define('DB_TABLE_ELEARNING_RESULT', "elearning_result");
define('DB_TABLE_ELEARNING_QUESTION_RESULT', "elearning_question_result");
define('DB_TABLE_ELEARNING_RESULT_RANGE', "elearning_result_range");
define('DB_TABLE_ELEARNING_SOLUTION', "elearning_solution");
define('DB_TABLE_ELEARNING_EXERCISE', "elearning_exercise");
define('DB_TABLE_ELEARNING_LESSON', "elearning_lesson");
define('DB_TABLE_ELEARNING_LESSON_PARAGRAPH', "elearning_lesson_paragraph");
define('DB_TABLE_ELEARNING_LESSON_MODEL', "elearning_lesson_model");
define('DB_TABLE_ELEARNING_LESSON_HEADING', "elearning_lesson_heading");
define('DB_TABLE_ELEARNING_COURSE', "elearning_course");
define('DB_TABLE_ELEARNING_COURSE_ITEM', "elearning_course_item");
define('DB_TABLE_ELEARNING_COURSE_INFO', "elearning_course_info");
define('DB_TABLE_ELEARNING_TEACHER', "elearning_teacher");
define('DB_TABLE_ELEARNING_CLASS', "elearning_class");
define('DB_TABLE_ELEARNING_SESSION', "elearning_session");
define('DB_TABLE_ELEARNING_SESSION_COURSE', "elearning_session_course");
define('DB_TABLE_ELEARNING_SUBSCRIPTION', "elearning_subscription");
define('DB_TABLE_ELEARNING_ASSIGNMENT', "elearning_assignment");
define('DB_TABLE_ELEARNING_ASSIGNMENT_CLASS', "elearning_assignment_class");
define('DB_TABLE_ELEARNING_SCORING', "elearning_scoring");
define('DB_TABLE_ELEARNING_SCORING_RANGE', "elearning_scoring_range");

// The set of user images
define('IMAGE_ELEARNING_ANSWER_TRUE', 'elearning_answer_true.png');
define('IMAGE_ELEARNING_ANSWER_FALSE', 'elearning_answer_false.png');
define('IMAGE_ELEARNING_EXERCISE_VIEW', 'view.png');
define('IMAGE_ELEARNING_EXERCISE', 'elearning_exercise.png');
define('IMAGE_ELEARNING_LESSON', 'elearning_lesson.png');
define('IMAGE_ELEARNING_COURSE', 'elearning_course.png');
define('IMAGE_ELEARNING_RESULT', 'elearning_result.png');
define('IMAGE_ELEARNING_TEXT', 'text.png');
define('IMAGE_ELEARNING_INFO', 'info.png');
define('IMAGE_ELEARNING_EXERCISE_START_OVER', 'reload.png');
define('IMAGE_ELEARNING_EXERCISE_CORRECTION', 'view.png');
define('IMAGE_ELEARNING_COURSE_GRAPH', 'chart.png');
define('IMAGE_ELEARNING_WHITEBOARD', 'whiteboard.png');

// Display states
define('ELEARNING_COLLAPSED', 1);
define('ELEARNING_FOLDED', 2);

// The public/protected status for the exercises
define('ELEARNING_PUBLIC', 1);
define('ELEARNING_PROTECTED', 2);

// The not yet opened/opened/closed status for the sessions
define('ELEARNING_SESSION_NOT_OPENED', 1);
define('ELEARNING_SESSION_OPEN', 2);
define('ELEARNING_SESSION_CLOSED', 3);

// The types of objects being serialized
define('ELEARNING_XML', "elearning");
define('ELEARNING_XML_MATTER', "elearning_matter");
define('ELEARNING_XML_COURSE', "elearning_course");
define('ELEARNING_XML_COURSE_ITEM', "elearning_course_item");
define('ELEARNING_XML_EXERCISE', "elearning_exercise");
define('ELEARNING_XML_EXERCISE_INTRODUCTION', "elearning_exercise_introduction");
define('ELEARNING_XML_EXERCISE_INSTRUCTIONS', "elearning_exercise_instructions");
define('ELEARNING_XML_QUESTION', "elearning_question");
define('ELEARNING_XML_QUESTION_EXPLANATION', "elearning_question_explanation");
define('ELEARNING_XML_EXERCISE_PAGE', "elearning_exercise_page");
define('ELEARNING_XML_EXERCISE_PAGE_TEXT', "elearning_exercise_page_text");
define('ELEARNING_XML_EXERCISE_PAGE_INSTRUCTIONS', "elearning_exercise_page_instructions");
define('ELEARNING_XML_ANSWER', "elearning_answer");
define('ELEARNING_XML_ANSWER_EXPLANATION', "elearning_answer_explanation");
define('ELEARNING_XML_SOLUTION', "elearning_solution");
define('ELEARNING_XML_LESSON', "elearning_lesson");
define('ELEARNING_XML_LESSON_INTRODUCTION', "elearning_lesson_introduction");
define('ELEARNING_XML_LESSON_PARAGRAPH', "elearning_lesson_paragraph");
define('ELEARNING_XML_LESSON_INSTRUCTIONS', "elearning_lesson_instructions");
define('ELEARNING_XML_LESSON_PARAGRAPH_BODY', "elearning_lesson_paragraph_body");

// The answer marker used to place the answer multiple choice question select list
define('ELEARNING_ANSWER_MCQ_MARKER', '???');
define('ELEARNING_ANSWER_UNDERSCORE', '__________');

// The separator used to concatenate several answer ids for a participant answer
// like one of the drag and drop under any questions type
define('ELEARNING_ANSWERS_SEPARATOR', '|');

// The default size of input fields in the questions
define('ELEARNING_DEFAULT_INPUT_SIZE', 10);

// Prefix of the name of a duplicated exercise
define('ELEARNING_DUPLICATA', '_DUPLICATA');

// Prefix of the name of an exercise put into the garbage
define('ELEARNING_GARBAGE', '_GARBAGE');

// The token names
define('ELEARNING_EXERCISE_ALERT_TOKEN_NAME', "elearning_exercise_alert_token_name");

// Some import constants
define('ELEARNING_IMPORT_LAST_COURSE_ID', 'elearning_import_last_course_id');

// Some DOM ids
define('ELEARNING_DOM_ID_CORRECT_V', 'elearning_correct_v_');
define('ELEARNING_DOM_ID_INCORRECT_V', 'elearning_incorrect_v_');
define('ELEARNING_DOM_ID_NO_ANSWER_V', 'elearning_no_answer_v_');
define('ELEARNING_DOM_ID_CORRECT_H', 'elearning_correct_h_');
define('ELEARNING_DOM_ID_INCORRECT_H', 'elearning_incorrect_h_');
define('ELEARNING_DOM_ID_NO_ANSWER_H', 'elearning_no_answer_h_');
define('ELEARNING_DOM_ID_INACTIVE', 'elearning_inactive_');
define('ELEARNING_DOM_ID_LIVE_RESULT', 'elearning_live_result_');
define('ELEARNING_DOM_ID_RESULT_GRADE', 'elearning_result_grade_');
define('ELEARNING_DOM_ID_RESULT_RATIO', 'elearning_result_ratio_');
define('ELEARNING_DOM_ID_RESULT_ANSWER', 'elearning_result_answer_');
define('ELEARNING_DOM_ID_RESULT_POINT', 'elearning_result_point_');
define('ELEARNING_DOM_ID_READING', 'reading_');
define('ELEARNING_DOM_ID_WRITING', 'writing_');
define('ELEARNING_DOM_ID_LISTENING', 'listening_');
define('ELEARNING_DOM_ID_QUESTION_RESULT_ANSWERS', 'elearning_question_result_answers_');
define('ELEARNING_DOM_ID_QUESTION_RESULT_THUMB', 'elearning_question_result_thumb_');
define('ELEARNING_DOM_ID_QUESTION_RESULT_POINT', 'elearning_question_result_point_');
define('ELEARNING_DOM_ID_QUESTION_RESULT_SOLUTIONS', 'elearning_question_result_solutions_');
define('ELEARNING_QUESTION_ID', 'elearning_question_');
define('ELEARNING_INSTANT_FEEDBACK_ID', 'elearning_question_instant_feedback_');
define('ELEARNING_ANSWER_ORDER_ID', 'elearning_question_answer_order_');
define('ELEARNING_QUESTION_RESET_ID', 'elearning_question_reset_');
define('ELEARNING_WRITE_TEXT', 'elearning_write_text_');
define('ELEARNING_WRITE_TEXT_NB_WORDS', 'elearning_write_text_nb_words_');
define('ELEARNING_WRITE_TEXT_PROGRESS', 'elearning_write_text_progressbar_');
define('ELEARNING_WRITE_IN_QUESTION', 'elearning_write_in_question_');

// The type of tabs for the pages of questions
define('ELEARNING_PAGE_TAB_IS_NUMBER', 1);
define('ELEARNING_PAGE_TAB_WITH_NUMBER', 2);

// Some ids
define('ELEARNING_IMPORT_OTHER_EXERCISE', 1);
define('ELEARNING_IMPORT_OTHER_LESSON', 2);

// The number of seconds before a participant is considered inactive 
// when doing an exercise watched live
define('ELEARNING_INACTIVE_TIME', 60);

// The number of seconds before a participant is considered absent 
// when doing an exercise watched live
define('ELEARNING_ABSENT_TIME', 60 * 30);

// The session variable
define('ELEARNING_SESSION_LESSON', "admin_elearning_lesson");
define('ELEARNING_SESSION_EXERCISE', "admin_elearning_exercise");
define('ELEARNING_SESSION_COURSE', "admin_elearning_course");
define('ELEARNING_SESSION_CATEGORY', "admin_elearning_category");
define('ELEARNING_SESSION_LEVEL', "admin_elearning_level");
define('ELEARNING_SESSION_SUBJECT', "admin_elearning_subject");
define('ELEARNING_SESSION_SESSION', "admin_elearning_session");
define('ELEARNING_SESSION_TEACHER', "admin_elearning_teacher");
define('ELEARNING_SESSION_CLASS', "admin_elearning_class");
define('ELEARNING_SESSION_MATTER', "admin_elearning_matter");
define('ELEARNING_SESSION_SUBSCRIPTION', "admin_elearning_subscription");
define('ELEARNING_SESSION_SCORING', "admin_elearning_scoring");
define('ELEARNING_SESSION_DURATION', "admin_elearning_duration");
define('ELEARNING_SESSION_STATUS', "admin_elearning_status");
define('ELEARNING_SESSION_EXERCISE_TIME_OUT', "admin_elearning_exercise_time_out");
define('ELEARNING_SESSION_EXERCISE_START_TIME', "admin_elearning_exercise_start_time");
define('ELEARNING_SESSION_EXERCISE_END_TIME', "admin_elearning_exercise_end_time");
define('ELEARNING_SESSION_EXERCISE_NB_CORRECT_ANSWERS', "admin_elearning_exercise_nb_correct_answers");
define('ELEARNING_SESSION_PUBLIC_ACCESS', "admin_elearning_public_access");
define('ELEARNING_SESSION_SESSION_STATUS', "admin_elearning_session_status");
define('ELEARNING_SESSION_DISPLAY_EXERCISE', "admin_elearning_display_exercise");
define('ELEARNING_SESSION_DISPLAY_EXERCISE_PAGE', "admin_elearning_display_exercise_page");
define('ELEARNING_SESSION_DISPLAY_QUESTION', "admin_elearning_display_question");
define('ELEARNING_SESSION_SUBSCRIPTION_SEARCH_PATTERN', "admin_elearning_subscription_search_pattern");
define('ELEARNING_SESSION_LESSON_SEARCH_PATTERN', "admin_elearning_lesson_search_pattern");
define('ELEARNING_SESSION_EXERCISE_SEARCH_PATTERN', "admin_elearning_exercise_search_pattern");
define('ELEARNING_SESSION_TEACHER_SEARCH_PATTERN', "admin_elearning_teacher_search_pattern");
define('ELEARNING_SESSION_COURSE_SEARCH_PATTERN', "admin_elearning_course_search_pattern");
define('ELEARNING_SESSION_RESULT_SEARCH_PATTERN', "admin_elearning_result_search_pattern");
define('ELEARNING_SESSION_RESULT_ID', 'elearning_result_id');
define('ELEARNING_SESSION_IMPORT_OTHER', 'elearning_import_other');

?>

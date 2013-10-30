<?PHP

// Name of the database table(s)
define('DB_TABLE_NEWS_PUBLICATION', "news_publication");
define('DB_TABLE_NEWS_PAPER', "news_paper");
define('DB_TABLE_NEWS_STORY', "news_story");
define('DB_TABLE_NEWS_STORY_IMAGE', "news_story_image");
define('DB_TABLE_NEWS_STORY_PARAGRAPH', "news_story_paragraph");
define('DB_TABLE_NEWS_HEADING', "news_heading");
define('DB_TABLE_NEWS_EDITOR', "news_editor");
define('DB_TABLE_NEWS_FEED', "news_feed");

// The set of user images
define('IMAGE_NEWS_ARCHIVE', 'news_archive.png');
define('IMAGE_NEWS_STORY_BACK', 'left.png');

// The publication status of the newspapers
define('NEWS_STATUS_PUBLISHED', 1);
define('NEWS_STATUS_DEFERRED', 2);
define('NEWS_STATUS_ARCHIVED', 3);
define('NEWS_STATUS_NOT_PUBLISHED', 4);

// Prefix of the name of a duplicated news story
define('NEWS_DUPLICATA', '_DUPLICATA');

// The alignment of the news story image in the newspaper
define('NEWS_ABOVE_EXCERPT', 1);
define('NEWS_LEFT_CORNER_EXCERPT', 2);
define('NEWS_RIGHT_CORNER_EXCERPT', 3);
define('NEWS_ABOVE_HEADLINE', 4);
define('NEWS_LEFT_CORNER_HEADLINE', 5);
define('NEWS_RIGHT_CORNER_HEADLINE', 6);

// The periods to search news story events
define('NEWS_EVENT_SEARCH_TODAY', 1);
define('NEWS_EVENT_SEARCH_TOMORROW', 2);
define('NEWS_EVENT_SEARCH_THISWEEK', 3);
define('NEWS_EVENT_SEARCH_NEXTWEEK', 4);
define('NEWS_EVENT_SEARCH_THISMONTH', 5);
define('NEWS_EVENT_SEARCH_NEXTMONTH', 6);

// The session variable
define('NEWS_SESSION_NEWSSTORY_SEARCH_PATTERN', "admin_news_newsstory_search_pattern");
define('NEWS_SESSION_NEWSPAPER_SEARCH_PATTERN', "admin_news_newspaper_search_pattern");
define('NEWS_SESSION_NEWSPAPER_STATUS', "admin_news_status");
define('NEWS_SESSION_NEWSPUBLICATION', "admin_newspublication");
define('NEWS_SESSION_NEWSPAPER', "admin_newspaper");
define('NEWS_SESSION_NEWSSTORY', "admin_newsstory");
define('NEWS_SESSION_NEWSHEADING', "admin_newsheading");
define('NEWS_SESSION_NEWSEDITOR', "admin_newseditor");

?>

<?PHP

// Name of the database table(s)
define('DB_TABLE_PHOTO', "photo");
define('DB_TABLE_PHOTO_ALBUM', "photo_album");
define('DB_TABLE_PHOTO_FORMAT', "photo_format");
define('DB_TABLE_PHOTO_ALBUM_FORMAT', "photo_album_format");

// The set of user images
define('IMAGE_PHOTO_ADD_TO_CART', 'add_to_cart.png');
define('IMAGE_PHOTO_SELECTION', 'selection.png');
define('IMAGE_PHOTO_ADD_TO_SELECTION', 'add_to_selection.png');
define('IMAGE_PHOTO_ALBUM_LIST', 'list.png');
define('IMAGE_PHOTO_SEARCH', 'view.png');
define('IMAGE_PHOTO_VIEW', 'view.png');
define('IMAGE_PHOTO_CART', 'cart.png');
define('IMAGE_PHOTO_DOWNLOAD', 'download.png');

// The session variable
define('PHOTO_SESSION_REFERENCE', "admin_photo_reference");
define('PHOTO_SESSION_SEARCH_PATTERN', "admin_photo_search_pattern");
define('PHOTO_SESSION_PUBLICATION_DATE', "admin_photo_publication_date");
define('PHOTO_SESSION_ALBUM', "admin_photo_album");

// Some admin variables
define('PHOTO_REPLACE_ALBUM', 1);
define('PHOTO_ADD_TO_ALBUM', 2);

?>

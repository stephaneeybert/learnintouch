<?PHP

// Name of the database table(s)
define('DB_TABLE_SHOP_ITEM', "shop_item");
define('DB_TABLE_SHOP_ITEM_IMAGE', "shop_item_image");
define('DB_TABLE_SHOP_CATEGORY', "shop_category");
define('DB_TABLE_SHOP_ORDER', "shop_order");
define('DB_TABLE_SHOP_ORDER_ITEM', "shop_order_item");
define('DB_TABLE_SHOP_PAYMENT', "shop_payment");
define('DB_TABLE_SHOP_AFFILIATE', "shop_affiliate");
define('DB_TABLE_SHOP_DISCOUNT', "shop_discount");

// The set of user images
define('IMAGE_SHOP_ADD_TO_CART', 'add_to_cart.png');
define('IMAGE_SHOP_CART_UPDATE', 'okay.png');
define('IMAGE_SHOP_CART_CHECKOUT', 'okay.png');
define('IMAGE_SHOP_ORDER_PDF', 'pdf.png');
define('IMAGE_SHOP_ORDER_VIEW', 'view.png');
define('IMAGE_SHOP_CART', 'cart.png');
define('IMAGE_SHOP_SELECTION', 'selection.png');
define('IMAGE_SHOP_SEARCH_ITEM', 'view.png');
define('IMAGE_SHOP_ITEM_LIST', 'list.png');
define('IMAGE_SHOP_CART_DELETE', 'close.png');
define('IMAGE_SHOP_ADD_TO_SELECTION', 'add_to_selection.png');
define('IMAGE_SHOP_CANCEL', 'close.png');

// The character to separate the item ids in the selection string
define('SHOP_SELECTION_SEPARATOR', ':');
define('SHOP_SELECTION_ITEM_SEPARATOR', ',');
define('SHOP_SELECTION_EMPTY', 'empty');

// The character to separate the item ids in the cart string
define('SHOP_CART_SEPARATOR', '|');
define('SHOP_CART_ITEM_SEPARATOR', ':');
define('SHOP_CART_OPTION_SEPARATOR', ',');
define('SHOP_CART_EMPTY', 'empty');

// The types of items in the cart
define('SHOP_CART_ITEM', 'shopItem');
define('SHOP_CART_PHOTO', 'photo');

// The aspect of the photos (mate or shiny)
define('SHOP_CART_PHOTO_MATTE', 'photo_matte');
define('SHOP_CART_PHOTO_SHINY', 'photo_shiny');

// The status of an order
define('SHOP_ORDER_STATUS_PENDING', 'pending');
define('SHOP_ORDER_STATUS_INVOICED', 'invoiced');
define('SHOP_ORDER_STATUS_PAID', 'paid');
define('SHOP_ORDER_STATUS_SHIPPED', 'shipped');
define('SHOP_ORDER_STATUS_CANCELLED', 'cancelled');
define('SHOP_ORDER_STATUS_REFUND', 'refund');

// The banks offering online payment
define('SHOP_BANK_PAYPAL', 'bank_paypal');
define('SHOP_BANK_TRANSFERT', 'bank_transfert');

// The types of payment
define('SHOP_ORDER_PAYMENT_CARD', 'card');
define('SHOP_ORDER_PAYMENT_CHECK', 'check');
define('SHOP_ORDER_PAYMENT_BANK', 'bank');

// The banks logo image files
$gImageBank = "bank.png";
$gImagePaypal = "paypal.jpg";

// The length of a password created for the shopper
define('SHOP_NEW_PASSWORD_LENGTH', 6);

// The token name
define('SHOP_CHECKOUT_TOKEN_NAME', "shop_checkout");

// The setup potential problems
define('SHOP_SETUP_BANK_UNKNOWN', 'bank_unknown');
define('SHOP_SETUP_BANK_ACCOUNT_UNSET', 'bank_account_unset');

// The paypal post payment redirection urls
define('SHOP_PAYMENT_PAYPAL_NOTIFY', "$gShopUrl/payment/paypal/notify.php");
define('SHOP_PAYMENT_PAYPAL_COMPLETE', "$gShopUrl/payment/paypal/message_complete.php");
define('SHOP_PAYMENT_PAYPAL_CANCEL', "$gShopUrl/payment/paypal/message_cancel.php");

// The session variable
define('SHOP_SESSION_ORDER', "admin_shop_order");
define('SHOP_SESSION_ITEM', "admin_shop_item");
define('SHOP_SESSION_SELECTION', "admin_shop_selection");
define('SHOP_SESSION_CART', "admin_shop_cart");
define('SHOP_SESSION_DISCOUNT', "admin_shop_discount");
define('SHOP_SESSION_ORDER_STATUS', "admin_shop_order_status");
define('SHOP_SESSION_MONTH', "admin_shop_month");
define('SHOP_SESSION_SEARCH_PATTERN', "admin_shop_search_pattern");
define('SHOP_SESSION_AFFILIATE', "admin_shop_affiliate");
define('SHOP_SESSION_AFFILIATE_SEARCH_PATTERN', "admin_shop_affiliate_search_pattern");
define('SHOP_SESSION_REFERENCE', "admin_shop_reference");
define('SHOP_SESSION_AVAILABLE', "admin_shop_available");
define('SHOP_SESSION_CATEGORY', "admin_shop_category");
define('SHOP_SESSION_PRICE_MIN', "admin_shop_price_min");
define('SHOP_SESSION_PRICE_MAX', "admin_shop_price_max");

?>

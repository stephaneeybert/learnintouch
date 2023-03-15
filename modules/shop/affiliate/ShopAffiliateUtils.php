<?

class ShopAffiliateUtils extends ShopAffiliateDB {

  var $shopDiscountUtils;
  var $shopItemUtils;
  var $userUtils;

  function __construct() {
    parent::__construct();
  }

  // Get the user id of an affiliate
  function getUserId($shopAffiliateId) {
    $userId = '';

    if ($shopAffiliate = $this->selectById($shopAffiliateId)) {
      $userId = $shopAffiliate->getUserId();
    }

    return($userId);
  }

  // Get the email of an affiliate
  function getEmail($shopAffiliateId) {
    $email = '';

    if ($shopAffiliate = $this->selectById($shopAffiliateId)) {
      $userId = $shopAffiliate->getUserId();
      if ($user = $this->userUtils->selectById($userId)) {
        $email = $user->getEmail();
      }
    }

    return($email);
  }

  // Get the firstname of an affiliate
  function getFirstname($shopAffiliateId) {
    $firstname = '';

    if ($shopAffiliate = $this->selectById($shopAffiliateId)) {
      $userId = $shopAffiliate->getUserId();
      if ($user = $this->userUtils->selectById($userId)) {
        $firstname = $user->getFirstname();
      }
    }

    return($firstname);
  }

  // Get the lastname of an affiliate
  function getLastname($shopAffiliateId) {
    $lastname = '';

    if ($shopAffiliate = $this->selectById($shopAffiliateId)) {
      $userId = $shopAffiliate->getUserId();
      if ($user = $this->userUtils->selectById($userId)) {
        $lastname = $user->getLastname();
      }
    }

    return($lastname);
  }

  // Render the email
  function renderEmail($email) {
    if ($email) {
      $str = "<a href='mailto:$email'>$email</a>";
    } else {
      $str = '';
    }

    return($str);
  }

  function affiliateHasDiscountCodes($shopAffiliateId) {
    if ($shopDiscounts = $this->shopDiscountUtils->selectByAffiliateId($shopAffiliateId)) {
      if (count($shopDiscounts) > 0) {
        return(true);
      }
    }

    return(false);
  }

  function getDiscountCodes($shopAffiliateId) {
    $discountCodes = '';

    if ($shopDiscounts = $this->shopDiscountUtils->selectByAffiliateId($shopAffiliateId)) {
      foreach ($shopDiscounts as $shopDiscount) {
        $discountCode = $shopDiscount->getDiscountCode();
        $discountRate = $shopDiscount->getDiscountRate();
        $discountRate = $this->shopItemUtils->decimalFormat($discountRate);
        $discountCodes .= " <span title='$discountRate %'>$discountCode</span>";
      }
    }

    return($discountCodes);
  }

  // Delete an affiliate
  function deleteAffiliate($shopAffiliateId) {
    if ($shopDiscounts = $this->shopDiscountUtils->selectByAffiliateId($shopAffiliateId)) {
      foreach ($shopDiscounts as $shopDiscount) {
        $this->shopDiscountUtils->delete($shopDiscount->getId());
      }
    }

    $this->delete($shopAffiliateId);
  }

}

?>

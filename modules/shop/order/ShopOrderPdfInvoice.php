<?

class ShopOrderPdfInvoice extends PdfUtils {

  var $mlText;

  var $currency;
  var $rowPerPage;
  var $itemHeaders;
  var $invoiceItems;

  var $totalQuantity;
  var $totalQuantityLabel;
  var $totalPrice;
  var $totalVAT;
  var $totalVATLabel;
  var $totalFee;
  var $totalFeeLabel;
  var $discountAmount;
  var $discountAmountLabel;
  var $totalToPay;
  var $totalToPayLabel;

  var $languageUtils;
  var $preferenceUtils;
  var $addressUtils;
  var $propertyUtils;
  var $clockUtils;
  var $shopOrderUtils;
  var $shopOrderItemUtils;
  var $shopItemUtils;

  function __construct() {
    $this->init();
  }

  function init() {
    $this->rowPerPage = 8;
  }

  function loadLanguageTexts() {
    $this->mlText = $this->languageUtils->getMlText(__FILE__);
  }

  function loadInvoiceLanguageTexts($languageCode) {
    $this->mlText = $this->languageUtils->getText(__FILE__, $languageCode, false);
  }

  function renderLegalNotice(&$pdfDocument) {
    $legalNotice = $this->preferenceUtils->getValue("SHOP_INVOICE_LEGAL_NOTICE");
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $legalNotice, 0, 'L');
  }

  function renderClientName(&$pdfDocument, $name) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $this->mlText[17] . ' ' . $name, 0, 'L');
  }

  function renderClientVatNumber(&$pdfDocument, $vatNumber) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $this->mlText[22] . ' ' . $vatNumber, 0, 'L');
  }

  function renderDate(&$pdfDocument, $orderDate) {
    $y1 = 15;
    $pdfDocument->SetY($y1);
    $invoiceDate = $this->mlText[15] . ' ' . $this->clockUtils->systemToLocalNumericDate($orderDate);
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $invoiceDate, 0, 'L');
  }

  function renderTitle(&$pdfDocument, $invoiceTitle) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', 'B', 16);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $invoiceTitle, 0, 'C');
  }

  function renderInvoiceNumber(&$pdfDocument, $invoiceNumber) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $invoiceNumber, 0, 'L');
  }

  function renderInvoiceDueDate(&$pdfDocument, $invoiceDueDate) {
    $pdfDocument->Ln(-($pdfDocument->lineHeight));
    $invoiceDueDate = $this->clockUtils->systemToLocalNumericDate($invoiceDueDate);
    $invoiceDueDate = $this->mlText[14] . ' ' . $invoiceDueDate;
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $invoiceDueDate, 0, 'R');
  }

  function renderClient(&$pdfDocument, $name, $email, $address1, $address2, $zipCode, $city, $state, $country, $postalBox) {
    $y1 = 15;
    $pdfDocument->SetY($y1);
    $pdfDocument->SetFont('Arial', 'B', 12);
    $pdfDocument->MultiCell(0, 4, $name, 0, 'R');
    $pdfDocument->SetY($y1 + 6);
    $pdfDocument->SetFont('Arial', '', 12);
    $address = $address1;
    if ($address2) {
      $address .= "\n" . $address2;
    }
    $address .= "\n" . $zipCode . ' ' . $city;
    if ($state) {
      $address .= "\n" . $state;
    }
    if ($postalBox) {
      $address .= "\n" . $postalBox;
    }
    $address .= "\n" . $country;
    if ($email) {
      $address .= "\n" . $email;
    }
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $address, 0, 'R');
  }

  function renderBank(&$pdfDocument) {
    $shopName = $this->propertyUtils->retrieve('SHOP_NAME');
    $shopTelephone = $this->propertyUtils->retrieve('SHOP_TELEPHONE');
    $shopMobilePhone = $this->propertyUtils->retrieve('SHOP_MOBILE_PHONE');
    $shopEmail = $this->propertyUtils->retrieve('SHOP_EMAIL');
    $shopRegistrationNumber = $this->propertyUtils->retrieve('SHOP_REGISTRATION_NUMBER');
    $shopVATNumber = $this->propertyUtils->retrieve('SHOP_VAT_NUMBER');
    $shopBankName = $this->propertyUtils->retrieve('SHOP_BANK_NAME');
    $shopBankAccount = $this->propertyUtils->retrieve('SHOP_BANK_ACCOUNT');
    $shopBankIBAN = $this->propertyUtils->retrieve('SHOP_BANK_IBAN');
    $shopBankBIC = $this->propertyUtils->retrieve('SHOP_BANK_BIC');

    $shopName = $this->pdfCleanString($shopName);
    $shopRegistrationNumber = $this->pdfCleanString($shopRegistrationNumber);
    $shopVATNumber = $this->pdfCleanString($shopVATNumber);
    $shopBankName = $this->pdfCleanString($shopBankName);

    $bank = $this->mlText[5] . ' ' . $shopName . "\n";
    if ($shopTelephone) {
      $bank .= $this->mlText[18] . ' ' . $shopTelephone . "\n";
    }
    if ($shopMobilePhone) {
      $bank .= $this->mlText[19] . ' ' . $shopMobilePhone . "\n";
    }
    if ($shopEmail) {
      $bank .= $this->mlText[20] . ' ' . $shopEmail . "\n";
    }
    if ($shopRegistrationNumber) {
      $bank .= $shopRegistrationNumber . "\n";
    }
    if ($shopVATNumber) {
      $bank .= $this->mlText[16] . ' ' . $shopVATNumber . "\n";
    }
    $bank .= $this->mlText[6] . ' ' . $shopBankName . "\n"
      . $this->mlText[7] . ' ' . $shopBankAccount . "\n"
      . $this->mlText[8] . ' ' . $shopBankIBAN . "\n"
      . $this->mlText[12] . ' ' . $shopBankBIC;

    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $bank, 0, "L");
  }

  function countNbTextLineBreaks(&$pdfDocument, $str, $width) {
    $nbLineBreaks = 0;
    $words = explode(' ', $str);
    $line = '';
    for ($i = 0; $i < count($words); $i++) {
      $tmpLine = $line;
      if ($tmpLine) {
        $tmpLine .= ' ';
      }
      $tmpLine .= $words[$i];
      $lineWidth = $pdfDocument->GetStringWidth($tmpLine);
      if ($lineWidth < $width) {
        $line = $tmpLine;
      } else {
        $line = $words[$i];
        $nbLineBreaks++;
      }
    }

    return($nbLineBreaks);
  }

  function buildTable(&$pdfDocument) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('','B');

    // Header
    $textColumnWidth = 70;
    $otherColumnWidth = 30;
    $width = array($textColumnWidth, $otherColumnWidth, $otherColumnWidth, $otherColumnWidth, $otherColumnWidth);
    $pdfDocument->SetFont('Arial','B',13);
    for($i = 0; $i < count($this->itemHeaders); $i++) {
      $pdfDocument->Cell($width[$i], 10, $this->itemHeaders[$i], 1, 0, 'C');
    }
    $pdfDocument->Ln();

    // Items
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetWidths(array($textColumnWidth, $otherColumnWidth, $otherColumnWidth, $otherColumnWidth, $otherColumnWidth));
    $pdfDocument->SetAligns(array('L', 'R', 'R', 'C', 'R'));
    $pdfDocument->SetBorders(array('LR', 'LR', 'LR', 'LR', 'LR'));
    $count = 0;
    foreach($this->invoiceItems as $invoiceItem) {
      list($strName, $options, $reference, $quantity, $priceInclVAT, $VAT) = $invoiceItem;

      $strText = $strName;

      if ($reference) {
        $strText .= ' - ' . $this->mlText[21] . ' ' . $reference;
      }

      if ($options) {
        $strText .= ' - ' . $options;
      }

      $strPriceInclVAT = number_format($priceInclVAT, 2, '.', '') . ' ' . $this->currency;

      $strVAT = number_format($VAT, 2, '.', '') . ' ' . $this->currency;

      $quantity = $quantity;

      $strTotalItemPrice = number_format($priceInclVAT * $quantity, 2, '.', '') . ' ' . $this->currency;

      // Avoid gaps in the vertical borders
      $nbTextLineBreaks = $this->countNbTextLineBreaks($pdfDocument, $strText, $textColumnWidth);
      for ($i = 0; $i < $nbTextLineBreaks; $i++) {
        $strPriceInclVAT .= "\n ";
        $quantity .= "\n ";
        $strTotalItemPrice .= "\n ";
      }

      $pdfDocument->Row(array($strText, $strPriceInclVAT, $strVAT, $quantity, $strTotalItemPrice));

      $count++;
    }

    // Fill up with empty rows
    while ($count++ <= $this->rowPerPage) {
      $pdfDocument->Row(array('', '', '', '', ''));
    }

    $pdfDocument->Cell(array_sum($width), 0, '', 'T');
    $pdfDocument->Ln();

    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->Cell($width[0] + $width[1] + $width[2] + $width[3], 10, $this->totalQuantity . ' ' . $this->totalQuantityLabel, 1, 0, 'R');
    $pdfDocument->SetFont('Arial', 'B', 12);
    $pdfDocument->Cell($width[3], 10, number_format($this->totalPrice, 2, '.', '') . ' ' . $this->currency, 1, 0, 'R');
    $pdfDocument->Ln();

    if ($this->totalVAT > 0) {
      $pdfDocument->SetFont('Arial', '', 12);
      $pdfDocument->Cell($width[0] + $width[1] + $width[2] + $width[3], 10, $this->totalVATLabel, 1, 0, 'R');
      $pdfDocument->SetFont('Arial', 'B', 12);
      $pdfDocument->Cell($width[3], 10, number_format($this->totalVAT, 2, '.', '') . ' ' . $this->currency, 1, 0, 'R');
      $pdfDocument->Ln();
    }

    if ($this->totalFee > 0) {
      $pdfDocument->SetFont('Arial', '', 12);
      $pdfDocument->Cell($width[0] + $width[1] + $width[2] + $width[3], 10, $this->totalFeeLabel, 1, 0, 'R');
      $pdfDocument->SetFont('Arial', 'B', 12);
      $pdfDocument->Cell($width[3], 10, number_format($this->totalFee, 2, '.', '') . ' ' . $this->currency, 1, 0, 'R');
      $pdfDocument->Ln();
    }

    if ($this->discountAmount > 0) {
      $pdfDocument->SetFont('Arial', '', 12);
      $pdfDocument->Cell($width[0] + $width[1] + $width[2] + $width[3], 10, $this->discountAmountLabel, 1, 0, 'R');
      $pdfDocument->SetFont('Arial', 'B', 12);
      $pdfDocument->Cell($width[3], 10, number_format($this->discountAmount, 2, '.', '') . ' ' . $this->currency, 1, 0, 'R');
      $pdfDocument->Ln();
    }

    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->Cell($width[0] + $width[1] + $width[2] + $width[3], 10, $this->totalToPayLabel, 1, 0, 'R');
    $pdfDocument->SetFont('Arial', 'B', 12);
    $pdfDocument->Cell($width[3], 10, number_format($this->totalToPay, 2, '.', '') . ' ' . $this->currency, 1, 0, 'R');
    $pdfDocument->Ln();
  }

  function renderItems(&$pdfDocument, $shopOrderId) {
    if ($shopOrder = $this->shopOrderUtils->selectById($shopOrderId)) {
      $invoiceNote = $shopOrder->getInvoiceNote();
      $discountCode = $shopOrder->getDiscountCode();
      $discountAmount = $shopOrder->getDiscountAmount();

      if ($shopOrderItems = $this->shopOrderItemUtils->selectByShopOrderId($shopOrderId)) {
        $totalVAT = 0;
        $totalToPay = 0;
        $totalQuantity = 0;
        $totalPrice = 0;
        $totalFee = 0;

        $invoiceItems = array();
        foreach ($shopOrderItems as $shopOrderItem) {
          $name = $shopOrderItem->getName();
          $reference = $shopOrderItem->getReference();
          $shortDescription = $shopOrderItem->getShortDescription();
          $price = $shopOrderItem->getPrice();
          $shippingFee = $shopOrderItem->getShippingFee();
          $quantity = $shopOrderItem->getQuantity();
          $options = $shopOrderItem->getOptions();

          $vatRate = $shopOrderItem->getVatRate();

          $price = $this->shopItemUtils->decimalFormat($price);

          if ($vatRate > 0) {
            $VAT = round($price * $vatRate / 100, 2);
            $priceInclVAT = $price + $VAT;
          } else {
            $VAT = 0;
            $priceInclVAT = $price;
          }

          $strName = $name;
          if ($shortDescription) {
            $strName .= ' - ' . $shortDescription;
          }
          if ($invoiceNote) {
            $strName .= ' - ' . $invoiceNote;
          }

          array_push($invoiceItems, array($strName, $options, $reference, $quantity, $priceInclVAT, $VAT));

          $totalItemPrice = $quantity * $priceInclVAT;
          $totalVAT = $totalVAT + ($quantity * $VAT);
          $totalQuantity = $totalQuantity + $quantity;
          $totalPrice = $totalPrice + $totalItemPrice;
          $totalFee = $totalFee + ($shippingFee * $quantity);
        }

        $this->invoiceItems = $invoiceItems;

        $totalFee = $totalFee + $this->handlingFee;

        $totalToPay = $totalPrice + $totalFee;

        if ($discountAmount) {
          $totalToPay = $totalToPay - $discountAmount;
        }

        $this->totalQuantity = $totalQuantity;
        $this->totalQuantityLabel = $this->mlText[9];
        $this->totalPrice = $totalPrice;
        $this->totalVAT = $totalVAT;
        $this->totalVATLabel = $this->mlText[25];
        $this->totalFee = $totalFee;
        $this->totalFeeLabel = $this->mlText[11];
        $this->discountAmount = $discountAmount;
        $this->discountAmountLabel = $this->mlText[23];
        $this->totalToPay = $totalToPay;
        $this->totalToPayLabel = $this->mlText[10];

        $this->itemHeaders = array($this->mlText[1], $this->mlText[3], $this->mlText[24], $this->mlText[2], $this->mlText[4]);

        $this->buildTable($pdfDocument);
      }
    }
  }

  function render(&$pdfDocument, $shopOrder) {
    $this->loadLanguageTexts();

    $shopOrderId = $shopOrder->getId();
    $firstname = $shopOrder->getFirstname();
    $lastname = $shopOrder->getLastname();
    $organisation = $shopOrder->getOrganisation();
    $vatNumber = $shopOrder->getVatNumber();
    $email = $shopOrder->getEmail();
    $telephone = $shopOrder->getTelephone();
    $mobilePhone = $shopOrder->getMobilePhone();
    $invoiceNumber = $shopOrder->getInvoiceNumber();
    $invoiceLanguage = $shopOrder->getInvoiceLanguage();
    $orderDate = $shopOrder->getOrderDate();
    $dueDate = $shopOrder->getDueDate();
    $invoiceAddressId = $shopOrder->getInvoiceAddressId();
    $this->currency = $shopOrder->getCurrency();
    $this->handlingFee = $shopOrder->getHandlingFee();

    $firstname = $this->pdfCleanString($firstname);
    $lastname = $this->pdfCleanString($lastname);
    $organisation = $this->pdfCleanString($organisation);
    $vatNumber = $this->pdfCleanString($vatNumber);

    $this->loadInvoiceLanguageTexts($invoiceLanguage);

    $invoiceAddress = $this->addressUtils->selectById($invoiceAddressId);
    $invoiceAddress1 = $invoiceAddress->getAddress1();
    $invoiceAddress2 = $invoiceAddress->getAddress2();
    $invoiceZipCode = $invoiceAddress->getZipCode();
    $invoiceCity = $invoiceAddress->getCity();
    $invoiceState = $invoiceAddress->getState();
    $invoiceCountry = $invoiceAddress->getCountry();
    $invoicePostalBox = $invoiceAddress->getPostalBox();

    $invoiceAddress1 = $this->pdfCleanString($invoiceAddress1);
    $invoiceAddress2 = $this->pdfCleanString($invoiceAddress2);
    $invoiceCity = $this->pdfCleanString($invoiceCity);
    $invoiceState = $this->pdfCleanString($invoiceState);
    $invoiceCountry = $this->pdfCleanString($invoiceCountry);
    $invoicePostalBox = $this->pdfCleanString($invoicePostalBox);

    if ($organisation) {
      $strName = $organisation;
    } else {
      $strName = $firstname . ' ' . $lastname;
    }

    $this->renderDate($pdfDocument, $orderDate);

    $this->renderClient($pdfDocument, $strName, $email, $invoiceAddress1, $invoiceAddress2, $invoiceZipCode, $invoiceCity, $invoiceState, $invoiceCountry, $invoicePostalBox);

    $this->renderTitle($pdfDocument, $this->mlText[0]);

    $this->renderClientName($pdfDocument, $strName);

    if ($vatNumber) {
      $this->renderClientVatNumber($pdfDocument, $vatNumber);
    }

    if (!$invoiceNumber) {
      $invoiceNumber = $shopOrderId;
    }
    $invoiceNumber = $this->mlText[13] . ' ' . $invoiceNumber;
    $this->renderInvoiceNumber($pdfDocument, $invoiceNumber);

    if (!$dueDate) {
      $dueDate = $orderDate;
    }

    $this->renderInvoiceDueDate($pdfDocument, $dueDate);

    $this->renderItems($pdfDocument, $shopOrderId);

    $this->renderBank($pdfDocument);

    $this->renderLegalNotice($pdfDocument);
  }

}

?>

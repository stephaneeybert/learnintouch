<?

class ShopOrderPdfTemplate extends PdfTemplate {

  var $propertyUtils;
  var $pdfUtils;

  function Footer() {
    $recipientName = $this->propertyUtils->retrieve('SHOP_NAME');
    $recipientAddress = $this->propertyUtils->retrieve('SHOP_ADDRESS');
    $recipientZipCode = $this->propertyUtils->retrieve('SHOP_ZIPCODE');
    $recipientCountry = $this->propertyUtils->retrieve('SHOP_COUNTRY');
    $recipientTelephone = $this->propertyUtils->retrieve('SHOP_TELEPHONE');
    $registrationNumber = $this->propertyUtils->retrieve('SHOP_REGISTRATION_NUMBER');

    $recipientName = $this->pdfUtils->pdfCleanString($recipientName);
    $recipientAddress = $this->pdfUtils->pdfCleanString($recipientAddress);
    $recipientCountry = $this->pdfUtils->pdfCleanString($recipientCountry);

    $this->SetY(-($this->lineHeight * 3));
    $this->SetFont('Arial', '', 12);
    $this->SetTextColor(128);
    $recipient = $recipientName
      . " - " . $recipientAddress 
      . " - " . $recipientZipCode
      . " - " . $recipientCountry;
    $this->MultiCell(0, $this->lineHeight, $recipient, 0, 'C');

    if ($registrationNumber) {
      $this->SetY(-($this->lineHeight * 2));
      $this->MultiCell(0, $this->lineHeight, $registrationNumber, 0, 'C');
    }
  }

}

?>

<?

class PdfTemplate extends PdfTable {

  var $lineHeight;
  var $localDate;
  var $websiteName;
  var $copyright;

  function setLineHeight($lineHeight) {
    $this->lineHeight = $lineHeight;
  }

  function setLocalDate($localDate) {
    $this->localDate = $localDate;
  }

  function setWebsiteName($websiteName) {
    $this->websiteName = $websiteName;
  }

  function setCopyright($copyright) {
    $this->copyright = $copyright;
  }

  function Header() {
    $this->SetFont('Arial', '', 12);
    $this->SetTextColor(128);
    $this->SetY(PDF_LINE_HEIGHT);
    $header = '';
    if ($this->websiteName) {
      $header .= $this->websiteName;
    }
    if ($this->localDate) {
      if ($header) {
        $header .= " - ";
      }
      $header .= $this->localDate;
    }
    $this->MultiCell(0, PDF_LINE_HEIGHT, $header, 0, 'R');
    $this->Ln();
  }

  function Footer() {
    $this->SetFont('Arial', '', 12);
    $this->SetTextColor(128);
    $this->SetY(-(PDF_LINE_HEIGHT * 2));
    $footer = $this->copyright;
    $this->MultiCell(0, PDF_LINE_HEIGHT, $footer, 0, 'C');
    $this->SetY(-(PDF_LINE_HEIGHT * 2));
    $pageNo = $this->PageNo() . ' / totalNbPages';
    $this->MultiCell(0, PDF_LINE_HEIGHT, $pageNo, 0, 'R');
  }

}

?>

<?

class PdfUtils {

  function renderLogo(&$pdfDocument, $logo, $url) {
    $y = $pdfDocument->GetY();
    $x = $pdfDocument->GetX();
    $pdfDocument->Image($logo, $x, $y, '', '', '', $url);
  }

  function pdfCleanString($str) {
    $str = LibString::stripLineBreaks($str);
    $str = LibString::br2nl($str);
    $str = LibString::stripTags($str);
    $str = LibString::decodeHtmlspecialchars($str);
    $str = trim($str);

    return($str);
  }

  function lineBreakIfImageIsTooBig(&$pdfDocument, &$y, $imageHeight) {
    // If the image bites into the footer then insert a page break
    $freeHeight = PDF_A4_PAGE_HEIGHT - (PDF_LINE_HEIGHT * 2) - $y;
    if ($freeHeight - $imageHeight <= 0) {
      $pdfDocument->AddPage();
      $y = $pdfDocument->GetY();

      return(true);
    } else {
      return(false);
    }
  }

  function getImageWidthInMm($width) {
    $mmPerInch = PDF_MM_PER_INCH;
    $dotPerInch = PDF_IMAGE_DOT_PER_INCH;
    $mmWidth = $width / $dotPerInch * $mmPerInch;

    return($mmWidth);
  }

  function getImageHeightInMm($height) {
    $mmHeight = $this->getImageWidthInMm($height);

    return($mmHeight);
  }

  function getImageCenterPosition($imageWidth) {
    $imageWidthMm = $this->getImageWidthInMm($imageWidth);

    $pageWidth = PDF_A4_PAGE_WIDTH;

    $x = ($pageWidth - $imageWidthMm) / 2;

    return($x);
  }

}

?>

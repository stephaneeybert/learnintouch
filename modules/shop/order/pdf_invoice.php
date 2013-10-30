<?php

require_once("website.php");
require_once($gShopPath . 'order/ShopOrderPdfTemplate.php');
require_once($gShopPath . 'order/ShopOrderPdfInvoice.php');

$shopOrderId = LibEnv::getEnvHttpGET("shopOrderId");

if (!$shopOrder = $shopOrderUtils->selectById($shopOrderId)) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$systemDate = $clockUtils->getSystemDate();
$pdfFilename = "invoice_" . $systemDate;
$filename = LibString::stripNonFilenameChar($pdfFilename);
$pdfFilename = $filename . ".pdf";

$pdfDocument = new ShopOrderPdfTemplate('P', 'mm', 'A4');
$pdfDocument->propertyUtils = $propertyUtils;
$pdfDocument->pdfUtils = $pdfUtils;
$pdfDocument->Open();
$pdfDocument->SetFont('Arial', '', 12);
$pdfDocument->AliasNbPages("totalNbPages");
$pdfDocument->setLineHeight(5);

$pdfDocument->AddPage();

$shopOrderPdfInvoice = new ShopOrderPdfInvoice();

$shopOrderPdfInvoice->languageUtils = $languageUtils;
$shopOrderPdfInvoice->preferenceUtils = $preferenceUtils;
$shopOrderPdfInvoice->addressUtils = $addressUtils;
$shopOrderPdfInvoice->propertyUtils = $propertyUtils;
$shopOrderPdfInvoice->clockUtils = $clockUtils;
$shopOrderPdfInvoice->shopOrderUtils = $shopOrderUtils;
$shopOrderPdfInvoice->shopOrderItemUtils = $shopOrderItemUtils;
$shopOrderPdfInvoice->shopItemUtils = $shopItemUtils;

$shopOrderPdfInvoice->render($pdfDocument, $shopOrder);

$pdfDocument->Output($pdfFilename, 'I');

?>

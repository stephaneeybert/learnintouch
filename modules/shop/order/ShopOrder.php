<?php

class ShopOrder {

  var $id;
  var $firstname;
  var $lastname;
  var $organisation;
  var $vatNumber;
  var $email;
  var $telephone;
  var $mobilePhone;
  var $fax;
  var $message;
  var $handlingFee;
  var $discountCode;
  var $discountAmount;
  var $currency;
  var $invoiceNumber;
  var $invoiceNote;
  var $invoiceLanguage;
  var $invoiceAddressId;
  var $shippingAddressId;
  var $orderDate;
  var $dueDate;
  var $clientIP;
  var $status;
  var $paymentType;
  var $paymentTransactionID;
  var $userId;

  function __construct($id = '') {
  }

  function getId() {
    return($this->id);
  }

  function getFirstname() {
    return($this->firstname);
  }

  function getLastname() {
    return($this->lastname);
  }

  function getOrganisation() {
    return($this->organisation);
  }

  function getVatNumber() {
    return($this->vatNumber);
  }

  function getEmail() {
    return($this->email);
  }

  function getTelephone() {
    return($this->telephone);
  }

  function getMobilePhone() {
    return($this->mobilePhone);
  }

  function getFax() {
    return($this->fax);
  }

  function getMessage() {
    return($this->message);
  }

  function getInvoiceNumber() {
    return($this->invoiceNumber) ;
  }

  function getInvoiceNote() {
    return($this->invoiceNote) ;
  }

  function getInvoiceLanguage() {
    return($this->invoiceLanguage) ;
  }

  function getInvoiceAddressId() {
    return($this->invoiceAddressId) ;
  }

  function getShippingAddressId() {
    return($this->shippingAddressId);
  }

  function getOrderDate() {
    return($this->orderDate);
  }

  function getDueDate() {
    return($this->dueDate);
  }

  function getClientIP() {
    return($this->clientIP);
  }

  function getStatus() {
    return($this->status);
  }

  function getPaymentType() {
    return($this->paymentType);
  }

  function getUserId() {
    return($this->userId);
  }

  function getHandlingFee() {
    return($this->handlingFee);
  }

  function getDiscountCode() {
    return($this->discountCode);
  }

  function getDiscountAmount() {
    return($this->discountAmount);
  }

  function getCurrency() {
    return($this->currency);
  }

  function getPaymentTransactionID() {
    return($this->paymentTransactionID);
  }

  function setId($id) {
    $this->id = $id;
  }

  function setFirstname($firstname) {
    $this->firstname = $firstname;
  }

  function setLastname($lastname) {
    $this->lastname = $lastname;
  }

  function setOrganisation($organisation) {
    $this->organisation = $organisation;
  }

  function setVatNumber($vatNumber) {
    $this->vatNumber = $vatNumber;
  }

  function setEmail($email) {
    $this->email = $email;
  }

  function setTelephone($telephone) {
    $this->telephone = $telephone;
  }

  function setMobilePhone($mobilePhone) {
    $this->mobilePhone = $mobilePhone;
  }

  function setFax($fax) {
    $this->fax = $fax;
  }

  function setMessage($message) {
    $this->message = $message;
  }

  function setInvoiceNumber($invoiceNumber) {
    $this->invoiceNumber = $invoiceNumber;
  }

  function setInvoiceNote($invoiceNote) {
    $this->invoiceNote = $invoiceNote;
  }

  function setInvoiceLanguage($invoiceLanguage) {
    $this->invoiceLanguage = $invoiceLanguage;
  }

  function setInvoiceAddressId($invoiceAddressId) {
    $this->invoiceAddressId = $invoiceAddressId;
  }

  function setShippingAddressId($shippingAddressId) {
    $this->shippingAddressId = $shippingAddressId;
  }

  function setOrderDate($orderDate) {
    $this->orderDate = $orderDate;
  }

  function setDueDate($dueDate) {
    $this->dueDate = $dueDate;
  }

  function setClientIP($clientIP) {
    $this->clientIP = $clientIP;
  }

  function setStatus($status) {
    $this->status = $status;
  }

  function setPaymentType($paymentType) {
    $this->paymentType = $paymentType;
  }

  function setUserId($userId) {
    $this->userId = $userId;
  }

  function setHandlingFee($handlingFee) {
    $this->handlingFee = $handlingFee;
  }

  function setDiscountCode($discountCode) {
    $this->discountCode = $discountCode;
  }

  function setDiscountAmount($discountAmount) {
    $this->discountAmount = $discountAmount;
  }

  function setCurrency($currency) {
    $this->currency = $currency;
  }

  function setPaymentTransactionID($paymentTransactionID) {
    $this->paymentTransactionID = $paymentTransactionID;
  }

}

?>

<?php

class LibEmail {

  // Check for the validity of the email address format
  static function validate($email) {
    if (!$email) {
      return(false);
    }

    if (preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,20})$/iU", $email)) {
      return(true);
    } else {
      return(false);
    }
  }

  // Check if the domain name of the email address
  static function validateDomain($email) {
    // Do not check if on my local server
    $SCRIPT_FILENAME = $_SERVER["SCRIPT_FILENAME"];
    if (strstr($SCRIPT_FILENAME, "/home/stephane/dev")) {
      return(true);
    }

    // Check if the domain can handle email
    list($userName, $mailDomail) = explode("@", $email);
    if ($mailDomail) {
      if (checkdnsrr($mailDomail, "MX")) {
        return(true);
      } else {
        return(false);
      }
    } else {
      return(false);
    }
  }

  // Send an email
  static function sendMail($toEmail, $toName, $subject, $body, $fromEmail = '', $fromName = '', $attachedImages = '', $attachedFiles = '', $textFormat = false, $confirmReception = false) {
    global $gMailSMTPHost;
    global $gMailSMTPPort;
    global $gMailSMTPUsername;
    global $gMailSMTPPassword;
    global $gTemplateUrl;
    global $gSetupWebsiteUrl;
    global $gSetupPath;

    $mail = new PHPMailer;
    if ($gMailSMTPHost) {
      $mail->isSMTP();
      $mail->Host = $gMailSMTPHost;
      $mail->Port = $gMailSMTPPort;
      $mail->SMTPAuth = true;
      $mail->Username = $gMailSMTPUsername;
      $mail->Password = $gMailSMTPPassword;
    }
    $mail->Subject = $subject;
    $mail->Body = $body;
    $mail->From = $fromEmail;
    $mail->FromName = $fromName;
    $mail->addAddress($toEmail, $toName);
    $mail->addReplyTo($fromEmail, $fromName);
    
    $unsubscribeUrl = "$gTemplateUrl/display.php?pageId=SYSTEM_PAGE_USER_UNSUBSCRIBE";
    $mail->addCustomHeader("List-Unsubscribe: <mailto:$fromEmail?subject=Unsubscribe>, <$unsubscribeUrl>");          
    
    // Setup the SSL private key and the SSL public key
    // The SSL encryption to be used for PHPMailer 5.2.14 is SHA256
    // The public key must also be specified in the DNS Zone
    // An example of a DNS Zone DKIM key entry:
    // mailng._domainkey.europasprak.com. 0 DKIM k=rsa; p=MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAyaWk6dvLpoYfcUcJM6F6JvZa4sNOglm6ejPUPeC6Z/ENGExnVupjWy5nuUyMfTa5ASu9+b+h+vRZtiQTQLS+7YlkLxFKn6MSGcbufjtIvETYstmN2C5AylUT1CwFEDvW2YlemmXswML8uh3hLJPVWbj1wrAhVZWsNqnYUkf/XOgELBaASfhdhYU+JlqGP7ulTApkeCLRtVJ3e/i4dlIIq7aHfjvDns5UOAPgz87X87wkT92S1cxFRRB7BxEig6joh2ijELXFtVWvtyPhLmZ4HE7IRtnslZzsAtLh5eQ31vYCUaBQEV98qdlJSb4IIxg+OAZHYOlmnAINtdMeDglXcwIDAQAB
    $mail->DKIM_domain = $gSetupWebsiteUrl;
    $mail->DKIM_private = $gSetupPath . 'dkim.ssl.private.key'; // The file containing the private key 
    $mail->DKIM_selector = "mailing"; // The prefix (like mailing in mailing._domainkey.europasprak.com.) seen in the DNS zone
    $mail->DKIM_passphrase = ""; // The passphrase protecting the private key
    $mail->DKIM_identity = $mail->From;

    if ($attachedImages && is_array($attachedImages)) {
      foreach ($attachedImages as $attachedImage) {
        $mail->addEmbeddedImage($attachedImage, basename($attachedImage));
      }
    }
    if ($attachedFiles && is_array($attachedFiles)) {
      foreach ($attachedFiles as $attachedFile) {
        $mail->addAttachment($attachedFile);
      }
    }
    if (!$textFormat) {
      $mail->isHTML(true);
    }
    if (!$mail->send()) {
      error_log("The mail could not be sent - " . $mail->ErrorInfo);
    }
  }

  // Send an email
  static function sendMail_WORKS_BUT_NOT_USED($toEmail, $toName, $subject, $body, $fromEmail = '', $fromName = '', $attachedImages = '', $attachedFiles = '', $textFormat = false, $confirmReception = false) {

    // The subject must not contain html encoded characters
    $subject = html_entity_decode($subject, ENT_QUOTES);

    // The email is case insensitive
    $toEmail = strtolower($toEmail);
    $fromEmail = strtolower($fromEmail);

    // Do not send the mail if on my local server
    if (isset($_SERVER["SCRIPT_FILENAME"])) {
      $SCRIPT_FILENAME = $_SERVER["SCRIPT_FILENAME"];
    } else {
      $SCRIPT_FILENAME = '';
    }

    if (strstr($SCRIPT_FILENAME, "/home/stephane/dev")) {
      return(false);
    }

    if (!$toEmail || !LibEmail::validate($toEmail)) {
      return(false);
    }

    if ($fromEmail && !$fromName) {
      $fromName = $fromEmail;
    }

    // Create a fallback text formatted message for the email clients that do
    // not support html messages
    $textMessage = LibString::br2nl($body);
    $textMessage = LibString::p2nl($textMessage);
    $textMessage = LibString::stripTags($textMessage);
    $textMessage = LibString::normalizeLinebreaks($textMessage);
    $textMessage = str_replace("&nbsp;", '', $textMessage);
    $textMessage = preg_replace("/\t+/iU", '', $textMessage);
    $textMessage = preg_replace("/\n +/iU", "\n", $textMessage);
    $textMessage = preg_replace("/\n{2,}/iU", "\n\n", $textMessage);
    $textMessage = nl2br($textMessage);

    // The email can be sent in an html format or in a text format
    if ($textFormat) {
      $htmlErrorMessage = $textMessage;
    } else {
      $htmlErrorMessage = $body;
    }

    // Make sure the attachment is an array
    if ($attachedImages && !is_array($attachedImages)) {
      $attachedImages = array($attachedImages);
    }
    if ($attachedFiles && !is_array($attachedFiles)) {
      $attachedFiles = array($attachedFiles);
    }

    LibEmail::mail($fromName, $fromEmail, $toName, $toEmail, $subject, $htmlErrorMessage, $textMessage, $attachedImages, $attachedFiles, $confirmReception);
  }

  static function mail($from_name, $from_email, $to_name, $to_email, $subject, $html_message, $text_message, $attachedImages, $attachedFiles, $confirmReception) {
    global $REMOTE_ADDR;
    global $gHomeUrl;

    $from = "$from_name <$from_email>";
    $to   = "$to_name <$to_email>";

    // The text_message is displayed only if the html_message cannot be displayed
    // The text_message is a fallback message for those email clients that do not support html
    // messages
    // If the email client supports html messages and the html_message is empty then no message
    // will be displayed, that is, the text_message fallback message will not be displayed
    // To force the display of a text formatted message, store the message in the html_message
    if (!$html_message && $text_message) {
      $html_message = $text_message;
    }

    $main_boundary = "----=_NextPart_".md5(rand());
    $text_boundary = "----=_NextPart_".md5(rand());
    $html_boundary = "----=_NextPart_".md5(rand());

    $headers  = "From: $from\n";
    $headers .= "Reply-To: $from\n";
    $headers .= "Return-Path: $from\n";
    if ($confirmReception) {
      $headers .= "Disposition-Notification-To: $from\n";
      $headers .= "X-Confirm-Reading-To: $from\n";
      $headers .= "Return-Receipt-To: $from\n";
    }

    $headers .= "X-Sender: $gHomeUrl\n";
    $headers .= "X-Mailer: PHP($REMOTE_ADDR)\n";
    $headers .= "X-auth-smtp-user: $from_email\n";
// Commented out following the extensive number of bouncing emails even if these have been delivered fine
//    $headers .= "Errors-To: $from\n";
//    $headers .= "X-abuse-contact: $from\n";

//$headers.="X-AntiAbuse: Servername - {www.[caché].fr}"."\r\n";/* remplace ici par ton domaine (après le www.) */
//$headers.="X-AntiAbuse: User - [email]"."\r\n";/* mets ici ton email */
//$headers.="X-Originating-Email: [[email]]"."\r\n";/* mets ici ton email */

    $headers .= "Date: ". date('r') ."\n";
    $headers .= "MIME-Version: 1.0\n";
    $headers .= "Content-Type: multipart/mixed;\n\tboundary=\"$main_boundary\"\n";

    $message  = "\n--$main_boundary\n";
    $message .= "Content-Type: multipart/alternative;\n\tboundary=\"$text_boundary\"\n";
    $message .= "\n--$text_boundary\n";
    $message .= "Content-Type: text/plain; charset=\"UTF-8\"\n";
    $message .= "Content-Transfer-Encoding: 8bit\n\n";
    $message .= ($text_message!="")?"$text_message":"Text portion of HTML Email";
    $message .= "\n--$text_boundary\n";
    $message .= "Content-Type: multipart/related;\n\tboundary=\"$html_boundary\"\n";
    $message .= "\n--$html_boundary\n";
    $message .= "Content-Type: text/html; charset=\"UTF-8\"\n";
    $message .= "Content-Transfer-Encoding: quoted-printable\n\n";
    $message .= str_replace("=", "=3D", $html_message)."\n";

    // If the email has an html format and the attached file is an image
    // referenced in the email content then display it inline
    // otherwise display it as an attached file
    if (isset($attachedImages) && $attachedImages != '' && count($attachedImages) >= 1) {
      for ($i = 0; $i < count($attachedImages); $i++) {
        $attachedImage = $attachedImages[$i];
        $imageName = basename($attachedImage);
        if (is_file($attachedImage) && $html_message && LibImage::isImage($attachedImage) && strstr($html_message, $imageName)) {
          $fp = fopen ($attachedImage, "r");
          $fcontent = '';
          while (!feof ($fp)) {
            $fcontent .= fgets ($fp, 1024);
          }
          $fcontent = chunk_split (base64_encode($fcontent));
          fclose ($fp);
          $message .= "\n--$html_boundary\n";
          $message .= "Content-Type: image/png; name=\"$imageName\"\n";
          $message .= "Content-Transfer-Encoding: base64\n";
          $message .= "Content-Disposition: inline; filename=\"$imageName\"\n";
          $message .= "Content-ID: <$imageName>\n\n";
          $message .= $fcontent;
        }
      }
    }

    if (isset($attachedFiles) && $attachedFiles != '' && count($attachedFiles) >= 1) {
      for ($i = 0; $i < count($attachedFiles); $i++) {
        $attachedFile = $attachedFiles[$i];
        if (is_file($attachedFile)) {
          $fileName = basename($attachedFile);
          $fp = fopen ($attachedFile, "r");
          $fcontent = '';
          while (!feof ($fp)) {
            $fcontent .= fgets ($fp, 1024);
          }
          $fcontent = chunk_split (base64_encode($fcontent));
          fclose ($fp);
          $message .= "\n--$html_boundary\n";
          $message .= "Content-Type: application/octetstream\n";
          $message .= "Content-Transfer-Encoding: base64\n";
          $message .= "Content-Disposition: attachment; filename=\"$fileName\"\n";
          $message .= "Content-ID: <$fileName>\n\n";
          $message .= $fcontent;
        }
      }
    }

    $message .= "\n--$html_boundary--\n";
    $message .= "\n--$text_boundary--\n";
    $message .= "\n--$main_boundary--\n";
    mail($to, $subject, $message, $headers);
  }

}

?>

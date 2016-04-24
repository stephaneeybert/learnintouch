<?php

class LibMailbox {

  // Open a mailbox
  static function openMailbox($mailServer, $email, $password) {
    $mailbox = imap_open($mailServer, $email, $password);

    return($mailbox);
    }

  // Close a mailbox
  static function closeMailbox($mailbox) {
    imap_close($mailbox);
    }

  // Parse a mail body for an email address
  static function getEmailAddressFromMailBody($content) {
    $email = '';

    if (preg_match_all("`\w([-_.]?\w)*@\w([-_.]?\w)*\.([a-z]{2,4})`", $content, $matches)) {
      $matches = $matches[0];
      $matches = array_unique($matches);
      if (count($matches) > 0) {
        $email = $matches[0];
        }
      }

    return($email);
    }

  // Delete an email from the mailbox
  static function deleteMail($mailbox, $mailNumber) {
    imap_delete($mailbox, $mailNumber);
    imap_expunge($mailbox);
    }

  // Get the mails of a mailbox
  static function getMailboxMails($mailbox) {
    $mails = array();

    $mailHeaders = imap_headers($mailbox);

    if ($mailHeaders) {
      while (list($mailNumber, $value) = each($mailHeaders)) {
        $mailNumber = $mailNumber + 1;
        $header = imap_headerinfo($mailbox, $mailNumber, 100, 100);
        if ($header) {
          $mailDate = date("d/m/Y", $header->udate);
          $mailSubject = $header->fetchsubject;
          $mailSubject = imap_utf8($mailSubject);
          $from = $header->from;
          $mailFrom = $from[0]->mailbox . "@" . $from[0]->host;
          $mailBody = LibMailbox::getEmailBody($mailbox, $mailNumber, "TEXT/PLAIN");
          $mailBody = imap_utf8($mailBody);

          $mail = array("number" => $mailNumber, "date" => $mailDate, "from" => $mailFrom, "subject" => $mailSubject, "body" => $mailBody);
          array_push($mails, $mail);
          }
        }
      }

    return($mails);
    }

  static function getMimeType(& $structure) {
    $primary_mime_type = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");

    if ($structure->subtype) {
      return($primary_mime_type[(int) $structure->type] . '/' .$structure->subtype);
      }

    return("TEXT/PLAIN");
    }

  // Retrieve the email body
  static function getEmailBody($stream, $msg_number, $mime_type, $structure = false, $part_number = false) {
    if (!$structure) {
      $structure = imap_fetchstructure($stream, $msg_number);
      }

    if ($structure) {
      if ($mime_type == LibMailbox::getMimeType($structure)) {
        if (!$part_number) {
          $part_number = "1";
          }
        $text = imap_fetchbody($stream, $msg_number, $part_number);
        if ($structure->encoding == 3) {
          return imap_base64($text);
          } else if ($structure->encoding == 4) {
          return imap_qprint($text);
          } else {
          return $text;
          }
        }

      // Check for a multipart email body
      if ($structure->type == 1) {
        while (list($index, $sub_structure) = each($structure->parts)) {
          if ($part_number) {
            $prefix = $part_number . '.';
            }
          $data = LibMailbox::getEmailBody($stream, $msg_number, $mime_type, $sub_structure, $prefix . ($index + 1));
          if ($data) {
            return $data;
            }
          }
        }
      }

    return false;
    }

  }

?>

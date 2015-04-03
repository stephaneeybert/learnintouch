<?php

/*

This file is part of LearnInTouch.

LearnInTouch is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

LearnInTouch is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with LearnInTouch.  If not, see <http://www.gnu.org/licenses/>.

*/

require_once("website.php");

$phone = LibEnv::getEnvHttpGET("phone");

if ($templateUtils->detectUserAgent() || $phone) {
  $templateModelId = $templateUtils->getPhoneEntry();
  $templateUtils->setCurrentModel($templateModelId);
  $templateUtils->setPhoneClient();
  require_once($gTemplatePath . "display.php");
} else {
  $templateModelId = $templateUtils->getComputerDefault();
  $templateUtils->setCurrentModel($templateModelId);
  require_once($gFlashPath . "display.php");
}

?>

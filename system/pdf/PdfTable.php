<?php

class PdfTable extends FPDF {

  var $widths;
  var $aligns;
  var $borders;
  var $fills;

  // Set the array of column widths
  function SetWidths($w) {
    $this->widths = $w;
  }

  // Set the array of column alignments
  function SetAligns($aligns) {
    $this->aligns = $aligns;
  }

  // Set the array of column borders
  function SetBorders($borders) {
    $this->borders = $borders;
  }

  // Set the array of column filling colors
  function SetFills($fills) {
    $this->fills = $fills;
  }

  function Row($data) {
    // Calculate the height of the row
    $nbRows = 0;
    for ($i = 0; $i < count($data); $i++) {
      $nbRows = max($nbRows, $this->NbLines($this->widths[$i], $data[$i]));
    }
    $height = 6;
    $totalHeight = $height * $nbRows;

    // Issue a page break first if needed
    $this->CheckPageBreak($totalHeight);

    // Draw the cells of the row
    for ($i = 0; $i < count($data); $i++) {
      $text = isset($data[$i]) ? $data[$i] : ' ';
      $width = $this->widths[$i];
      $align = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
      $border = isset($this->borders[$i]) ? $this->borders[$i] : '';
      $fill = isset($this->fills[$i]) ? $this->fills[$i] : '';

      // Save the current position
      $x = $this->GetX();
      $y = $this->GetY();

      // Print the text
      $this->MultiCell($width, $height, $text, $border, $align, $fill);

      // Put the position to the right of the cell
      $this->SetXY($x + $width, $y);
    }

    // Go to the next line
    $this->Ln($totalHeight);
  }

  // If the height would cause an overflow, then add a new page immediately
  function CheckPageBreak($height) {
    if ($this->GetY() + $height > $this->PageBreakTrigger) {
      $this->AddPage($this->CurOrientation);
    }
  }

  // Computes the number of lines a MultiCell of width w will take
  function NbLines($w, $txt) {
    $cw = &$this->CurrentFont['cw'];
    if ($w == 0) {
      $w = $this->w - $this->rMargin - $this->x;
    }
    $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
    $s = str_replace("\r", '', $txt);
    $nbRows =strlen($s);
    if ($nbRows>0 and $s[$nbRows-1] == "\n") {
      $nbRows--;
    }
    $sep = -1;
    $i = 0;
    $j = 0;
    $l = 0;
    $nl = 1;
    while ($i < $nbRows) {
      $c = $s[$i];
      if ($c == "\n") {
        $i++;
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
        continue;
      }
      if ($c == ' ') {
        $sep=$i;
      }
      $l +=$cw[$c];
      if ($l > $wmax) {
        if ($sep == -1) {
          if ($i == $j)
            $i++;
        } else {
          $i = $sep + 1;
        }
        $sep = -1;
        $j = $i;
        $l = 0;
        $nl++;
      } else {
        $i++;
      }
    }
    return $nl;
  }

}

?>

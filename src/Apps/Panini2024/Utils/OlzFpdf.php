<?php

namespace Olz\Apps\Panini2024\Utils;

use Fpdf\Fpdf;

class OlzFpdf extends Fpdf {
    protected $extgstates = [];

    // ### TextBox ###
    // Author: Darren Gates & Adrian Tufa <g8z@yahoo.com>
    // License: FPDF.

    /**
     * Draws text within a box defined by width = w, height = h, and aligns
     * the text vertically within the box ($valign = M/B/T for middle, bottom, or top)
     * Also, aligns the text horizontally ($align = L/C/R/J for left, centered, right or justified)
     * drawTextBox uses drawRows.
     *
     * This function is provided by TUFaT.com
     *
     * @param mixed $strText
     * @param mixed $xi
     * @param mixed $yi
     * @param mixed $w
     * @param mixed $h
     * @param mixed $align
     * @param mixed $valign
     * @param mixed $border
     */
    public function drawTextBox($strText, $xi, $yi, $w, $h, $align = 'L', $valign = 'T', $border = false) {
        $hrow = $this->FontSize;
        $textrows = $this->drawRows($w, $hrow, $strText, 0, $align, 0, 0, 0);
        $maxrows = floor($h / $this->FontSize);
        $rows = min($textrows, $maxrows);

        $dy = 0;
        if (strtoupper($valign) == 'M') {
            $dy = ($h - $rows * $this->FontSize) / 2;
        }
        if (strtoupper($valign) == 'B') {
            $dy = $h - $rows * $this->FontSize;
        }

        $this->SetY($yi + $dy);
        $this->SetX($xi);

        $this->drawRows($w, $hrow, $strText, 0, $align, false, $rows, 1);

        if ($border) {
            $this->Rect($xi, $yi, $w, $h);
        }
    }

    public function drawRows($w, $h, $txt, $border = 0, $align = 'J', $fill = false, $maxline = 0, $prn = 0) {
        if (!isset($this->CurrentFont)) {
            $this->Error('No font has been set');
        }
        $cw = $this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', (string) $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $b = 0;
        if ($border) {
            if ($border == 1) {
                $border = 'LTRB';
                $b = 'LRT';
                $b2 = 'LR';
            } else {
                $b2 = '';
                if (is_int(strpos($border, 'L'))) {
                    $b2 .= 'L';
                }
                if (is_int(strpos($border, 'R'))) {
                    $b2 .= 'R';
                }
                $b = is_int(strpos($border, 'T')) ? $b2.'T' : $b2;
            }
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $ns = 0;
        $nl = 1;
        while ($i < $nb) {
            // Get next character
            $c = $s[$i];
            if ($c == "\n") {
                // Explicit line break
                if ($this->ws > 0) {
                    $this->ws = 0;
                    if ($prn == 1) {
                        $this->_out('0 Tw');
                    }
                }
                if ($prn == 1) {
                    $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                }
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border && $nl == 2) {
                    $b = $b2;
                }
                if ($maxline && $nl > $maxline) {
                    return substr($s, $i);
                }
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
                $ls = $l;
                $ns++;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                // Automatic line break
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                    if ($this->ws > 0) {
                        $this->ws = 0;
                        if ($prn == 1) {
                            $this->_out('0 Tw');
                        }
                    }
                    if ($prn == 1) {
                        $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
                    }
                } else {
                    if ($align == 'J') {
                        $this->ws = ($ns > 1) ? ($wmax - $ls) / 1000 * $this->FontSize / ($ns - 1) : 0;
                        if ($prn == 1) {
                            $this->_out(sprintf('%.3F Tw', $this->ws * $this->k));
                        }
                    }
                    if ($prn == 1) {
                        $this->Cell($w, $h, substr($s, $j, $sep - $j), $b, 2, $align, $fill);
                    }
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $ns = 0;
                $nl++;
                if ($border && $nl == 2) {
                    $b = $b2;
                }
                if ($maxline && $nl > $maxline) {
                    return substr($s, $i);
                }
            } else {
                $i++;
            }
        }
        // Last chunk
        if ($this->ws > 0) {
            $this->ws = 0;
            if ($prn == 1) {
                $this->_out('0 Tw');
            }
        }
        if ($border && is_int(strpos($border, 'B'))) {
            $b .= 'B';
        }
        if ($prn == 1) {
            $this->Cell($w, $h, substr($s, $j, $i - $j), $b, 2, $align, $fill);
        }
        $this->x = $this->lMargin;
        return $nl;
    }

    // ### Transparency ###
    // Author: Martin Hall-May
    // License: FPDF.

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    public function SetAlpha($alpha, $bm = 'Normal') {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(['ca' => $alpha, 'CA' => $alpha, 'BM' => '/'.$bm]);
        $this->SetExtGState($gs);
    }

    public function AddExtGState($parms) {
        $n = count($this->extgstates) + 1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    public function SetExtGState($gs) {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    public function _enddoc() {
        if (!empty($this->extgstates) && $this->PDFVersion < '1.4') {
            $this->PDFVersion = '1.4';
        }
        parent::_enddoc();
    }

    public function _putextgstates() {
        for ($i = 1; $i <= count($this->extgstates); $i++) {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    public function _putresourcedict() {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach ($this->extgstates as $k => $extgstate) {
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        }
        $this->_put('>>');
    }

    public function _putresources() {
        $this->_putextgstates();
        parent::_putresources();
    }
}

<?php
// $Id: textoptions.class.php 825 2011-12-09 00:06:11Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

/**
 * @desc Clase para la creación de campos de opciones de texto
 */
class RMFormTextOptions extends RMFormElement
{
    private $html = 0;
    private $xcode = 0;
    private $smiley = 0;
    private $image = 0;
    private $br = 0;

    /**
     * @param string Texto del campo
     * @param string Nombre del Campo
     * @param int HTML activo (1) o inactivo (0)
     * @param int Xoops Code activo (1) o inactivo (0)
     * @param int Imágenes activo (1) o inactivo (0)
     * @param int Caritas activo (1) o inactivo (0)
     * @param int Saltos de Línea activo (1) o inactivo (0)
     * @param int Número de columnas (Máximo 5, Mínimo 1)
     * @param mixed $caption
     * @param mixed $html
     * @param mixed $xcode
     * @param mixed $image
     * @param mixed $smiley
     * @param mixed $br
     * @param mixed $cols
     */
    public function __construct($caption, $html = 0, $xcode = 0, $image = 0, $smiley = 0, $br = 0, $cols = 5)
    {
        $this->setCaption($caption);
        $this->html = $html;
        $this->xcode = $xcode;
        $this->smiley = $smiley;
        $this->image = $image;
        $this->br = $br;
        $this->cols = $cols > 5 ? 5 : ($cols <= 0 ? 1 : $cols);
    }

    public function html()
    {
        return $this->html;
    }

    /**
     * @param int 1 o 0
     * @param mixed $val
     */
    public function setHtml($val)
    {
        $this->html = $val;
    }

    public function xcode()
    {
        return $this->xcode;
    }

    /**
     * @param int 1 o 0
     * @param mixed $val
     */
    public function setXcode($val)
    {
        $this->xcode = $val;
    }

    public function image()
    {
        return $this->image;
    }

    /**
     * @param int 1 o 0
     * @param mixed $val
     */
    public function setImage($val)
    {
        $this->image = $val;
    }

    public function smiley()
    {
        return $this->smiley;
    }

    /**
     * @param int 1 o 0
     * @param mixed $val
     */
    public function setSmiley($val)
    {
        $this->smiley = $val;
    }

    public function br()
    {
        return $this->br;
    }

    /**
     * @param int 1 o 0
     * @param mixed $val
     */
    public function setBR($val)
    {
        $this->br = $val;
    }

    public function cols()
    {
        return $this->cols;
    }

    /**
     * @param int Mayor que 0
     * @param mixed $val
     */
    public function setCols($val)
    {
        $this->cols = $val;
    }

    public function render()
    {
        $rtn = '<table cellspacing="2" cellpadding="2" border="0">' . "\n<tr>";

        $cols = 1;
        for ($i = 1; $i <= 5; $i++) {
            if ($cols > $this->cols) {
                $rtn .= '</tr><tr>';
                $cols = 1;
            }
            $rtn .= '<td>';
            if (1 == $i) {
                $rtn .= '<label><input type="checkbox" name="dohtml" value="1"' . ($this->html ? ' checked' : '') . '> ' . __('Allow HTML', 'rmcommon') . "</label>\n";
            } elseif (2 == $i) {
                $rtn .= '<label><input type="checkbox" name="doxcode" value="1"' . ($this->xcode ? ' checked' : '') . '> ' . __('Allow Xoops Code', 'rmcommon') . "</label>\n";
            } elseif (3 == $i) {
                $rtn .= '<label><input type="checkbox" name="dosmiley" value="1"' . ($this->smiley ? ' checked' : '') . '> ' . __('Allow Smilies', 'rmcommon') . "</label>\n";
            } elseif (4 == $i) {
                $rtn .= '<label><input type="checkbox" name="dobr" value="1"' . ($this->br ? ' checked' : '') . '> ' . __('Allow break lines', 'rmcommon') . "</label>\n";
            } elseif (5 == $i) {
                $rtn .= '<label><input type="checkbox" name="doimage" value="1"' . ($this->image ? ' checked' : '') . '> ' . __('Do images', 'rmcommon') . "</label>\n";
            }
            $rtn .= '</td>';
        }
        $rtn .= "</tr>\n</table>";

        return $rtn;
    }
}

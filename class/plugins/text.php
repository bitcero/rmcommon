<?php
/*
    +--------------------------------------------------------------------------------------------+
    |   DISCLAIMER - LEGAL NOTICE -                                                              |
    +--------------------------------------------------------------------------------------------+
    |                                                                                            |
    |  This program is free for non comercial use, see the license terms available at            |
    |  http://www.francodacosta.com/licencing/ for more information                              |
    |                                                                                            |
    |  This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; |
    |  without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. |
    |                                                                                            |
    |  USE IT AT YOUR OWN RISK                                                                   |
    |                                                                                            |
    |                                                                                            |
    +--------------------------------------------------------------------------------------------+

*/
/**
 * phMagick - Text drawing functions
 *
 * @package    phMagick
 * @version    0.1.0
 * @author     Nuno Costa - sven@francodacosta.com
 * @copyright  Copyright (c) 2007
 * @license    http://www.francodacosta.com/phmagick/license/
 * @link       http://www.francodacosta.com/phmagick
 * @since      2008-03-13
 */
class phMagick_text
{
    /**
     * Draws an image with the submited string, usefull for water marks
     *
     * @param $text String - the text to draw an image from
     * @param $format phMagickTextObject - the text configuration
     */
    public function fromString(phmagick $p, $text = '', phMagickTextObject $format = null)
    {
        if (null === $format) {
            $format = new phMagickTextObject();
        }

        $cmd = $p->getBinary('convert');

        if (false !== $format->background) {
            $cmd .= ' -background "' . $format->background . '"';
        }

        if (false !== $format->color) {
            $cmd .= ' -fill "' . $format->color . '"';
        }

        if (false !== $format->font) {
            $cmd .= ' -font ' . $format->font;
        }

        if (false !== $format->fontSize) {
            $cmd .= ' -pointsize ' . $format->fontSize;
        }

        if (('' != $format->pText) && ($text = '')) {
            $text = $format->pText;
        }

        $cmd .= ' label:"' . $text . '"';
        $cmd .= ' "' . $p->getDestination() . '"';

        $p->execute($cmd);
        $p->setSource($p->getDestination());
        $p->setHistory($p->getDestination());

        return  $p;
    }
}

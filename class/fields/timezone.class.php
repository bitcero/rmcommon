<?php
// $Id: timezone.class.php 870 2011-12-22 08:51:07Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFormTimeZoneField extends RMFormElement
{
    private $multi = 0;
    private $type = 0;
    private $selected = null;
    private $size = 5;

    public function __construct($caption, $name, $type = 0, $multi = 0, $selected = null, $size = 5)
    {
        if (is_array($caption)) {
            parent::__construct($caption);
        } else {
            parent::__construct([]);
            $this->setWithDefaults('caption', $caption, '');
            $this->setWithDefaults('name', $name, 'name_error');
            $this->setWithDefaults('type', 0 == $type ? 'select' : (0 == $multi ? 'radio' : 'checkbox'), 'select');
            if (1 == $multi) {
                $this->setWithDefaults('multiple', null);
            }
            $this->setWithDefaults('selected', $selected, []);
        }

        $this->suppressRender(['caption', 'multiple', 'selected']);
    }

    public function multi()
    {
        return $this->multi;
    }

    public function setMulti($value)
    {
        return $this->multi = $value;
    }

    public function type()
    {
        return $this->type;
    }

    public function setType($value)
    {
        return $this->type = $value;
    }

    public function selected()
    {
        return $this->selected;
    }

    public function setSelected($value)
    {
        return $this->selected = $value;
    }

    public function size()
    {
        return $this->size;
    }

    public function setSize($value)
    {
        return $this->size = $value;
    }

    public function render()
    {
        require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
        $zonas = XoopsLists::getTimeZoneList();
        $selected = $this->get('selected');

        if ('readio' == $this->get('type') || 'checkbox' == $this->get('type')) {
            $this->suppressRender('class');
            $attributes = $this->renderAttributeString();

            $rtn = "<div class='checkbox'>";
            foreach ($zonas as $k => $v) {
                if ($this->has('multiple')) {
                    if (!is_array($selected)) {
                        $selected = [$selected];
                    }
                    $rtn .= "<label><input $attributes value='$k' " . (is_array($selected) ? (in_array($k, $selected, true) ? " checked" : '') : '') . "> $v</label>";
                } else {
                    $rtn .= "<label><input $attributes value='$k' " . ($k == $selected ? " checked" : '') . "> $v</label>";
                }
                $i++;
            }
            $rtn .= '</div>';
        } else {
            $attributes = $this->renderAttributeString();

            if (!is_array($selected)) {
                $selected = [$selected];
            }
            $rtn = "<select $attributes>";
            foreach ($zonas as $k => $v) {
                $rtn .= "<option value='$k'" . (is_array($selected) ? (in_array($k, $selected, true) ? " selected='selected'" : '') : '') . ">$v</option>";
            }
            $rtn .= '</select>';
        }

        return $rtn;
    }
}

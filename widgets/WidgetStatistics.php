<?php
/**
 * @copyright    Eduardo Cortés (https://bitcero.dev)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          https://rmcommon.bitcero.dev
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Widgets;

use Common\Core\Helpers\Widget;
use Common\Core\Helpers\WidgetAbstract;
use Common\Core\Helpers\WidgetInterface;

/**
 * This file contains a widget useful to show a counter with small information
 * and styled in different colors
 */
class WidgetStatistics extends WidgetAbstract implements WidgetInterface
{
  private $tplPath = '';
  private $cells = [];
  private $titles = [];
  private $statistics = [];

  /**
   * @param array $data
   * @return void
   */
  public function setup(array $data = [])
  {
    global $common;

    if (!array_key_exists('id', $data)) {
      $data['id'] = 'widget-statistics-' . $common->utilities()->randomString(5, true, false);
    }
    parent::__construct($data);
  }

  /**
   * Unique internal ID for this widget
   * @return string
   */
  public function id()
  {
    return 'CUWidgetStatistics';
  }

  /**
   * Gets the HTML code for this widget
   * @return string
   */
  public function getHtml()
  {
    global $cuIcons;

    $this->add('class', 'widget-statistics cu-box');

    // Widget color
    if ($this->has('color')) {
      $this->add('class', 'color-' . $this->get('color'));
    }

    $this->tpl->assign('attributes', $this->renderAttributeString());
    $this->tpl->assign('statistics', $this->statistics);
    $this->tpl->assign('titles', $this->titles);

    // Calculate cells

    return \RMTemplate::getInstance()->render($this->template());
  }

  /**
   * Get the template path used in this widget
   * @return string
   */
  public function template()
  {
    if ('' == $this->tplPath) {
      $this->tplPath = \RMTemplate::getInstance()->path('widgets/widget-statistics.php', 'module', 'rmcommon');
    }

    return $this->tplPath;
  }

  /**
   * Add a new tittle to the Widget.
   *
   * @param $title
   * @param Sizes $size Size of the title that can be sm, md or lg
   * @return void
   */
  public function add_title($title, string $size = 'lg')
  {
    $this->titles[] = (object)[
      'title' => $title,
      'size' => $size
    ];
  }

  public function add_stat(
    $value,
    string $title,
    string $icon = '',
    string $css_classes = '',
    string $color = 'primary'
  )
  {
    $this->statistics[] = (object)[
      'value' => $value,
      'title' => $title,
      'icon' => $icon,
      'css_classes' => $css_classes,
      'color' => $color,
    ];
  }
}

<?php
/**
 * Markdown Editor
 * A markdown editor for Common Utilities
 *
 * Copyright © 2015 Eduardo Cortés https://eduardocortes.mx
 * -----------------------------------------------------------------
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 * -----------------------------------------------------------------
 * @package       rmcommon
 * @subpackage    Editors
 * @since         2.2
 */

class Editor_Markdown extends RMEditor
{
    /*
     * Id for editor
     */
    private $id = '';
    /**
     * @var array Default configuration options
     * @link http://codemirror.net/doc/manual.html
     */
    private $defaults = array();
    /**
     * @var array Plugins that will be loaded when editor render
     */
    private $enabled_plugins = array(
        'link' => array()
    );

    public $content = '';

    /**
     * Instantiate the class
     * @param string $id Editor identifier (must be unique)
     * @param array $options Editor initial values (See {@link:http://codemirror.net/doc/manual.html#config Configuration})
     */
    public function __construct($id, $options = array())
    {
        $this->id = $id;

        $this->defaults = array(
            'value'             => '',
            'mode'              => 'gfm',
            'theme'             => 'default',
            'lineWrapping'      => 'true'
        );

        if (!empty($options)) {
            foreach ($options as $name => $value) {

                // Prevent change of markdown
                if ('mode' == $name) {
                    continue;
                }

                $this->defaults [$name] = $value;
            }
        }
    }

    /**
     * Set the plugins that will be loaded with editor
     * @param array $plugins Plugins active
     */
    public function set_plugins($plugins = array())
    {
        $this->enabled_plugins = $plugins;
    }

    /**
     * Set options for Codemirror
     * @param $name
     * @param $value
     */
    public function set_option($name, $value)
    {
        $this->defaults[ $name ] = $value;
    }

    /**
     * Make the JS code
     * @return string
     */
    public function render_js()
    {
        $options = '{';

        foreach ($this->defaults as $name => $value) {
            $options .= $name .': ' . (is_string($value) ? '"' . $value . '",' : $value . ';');
        }

        $options .= '}';

        /*
         * Create plugins
         */
        $plugins = '{';
        foreach ($this->enabled_plugins as $id => $data) {
            if (is_array($data) && !empty($data)) {
                $plugins .= '{' == $plugins ? '' : ',';
                $plugins .= $id . ': {file: "' . $id . '.js", path: "' . $data['path'] . '"}';
            } else {
                $plugins .= '{' == $plugins ? '' : ',';
                $plugins .= $id . ': {file: "plugin.js", path: "' . RMUris::relative_url(RMCURL . '/api/editors/markdown/plugins/' . $id) . '"}';
            }
        }
        $plugins .= '}';

        $script = 'function initMD(id){$(document).ready( function() {
mdEditor.init(id, ' . $options . ','. $plugins .'); } );} initMD("'.$this->id.'");';

        return $script;
    }

    /**
     * Render the editor
     * @return string
     */
    public function render()
    {
        global $cuIcons;

        RMTemplate::get()->add_script('codemirror.js', 'rmcommon', array('footer' => 1, 'directory' => 'api/editors/markdown'));
        RMTemplate::get()->add_script('mode/overlay.js', 'rmcommon', array('footer' => 1, 'directory' => 'api/editors/markdown'));
        RMTemplate::get()->add_script('mode/xml.js', 'rmcommon', array('footer' => 1, 'directory' => 'api/editors/markdown'));
        RMTemplate::get()->add_script('mode/markdown.js', 'rmcommon', array('footer' => 1, 'directory' => 'api/editors/markdown'));
        RMTemplate::get()->add_script('mode/gfm.js', 'rmcommon', array('footer' => 1, 'directory' => 'api/editors/markdown'));
        RMTemplate::get()->add_script('mode/meta.js', 'rmcommon', array('footer' => 1, 'directory' => 'api/editors/markdown'));
        RMTemplate::get()->add_script('markdown-editor.min.js', 'rmcommon', array('footer' => 1, 'directory' => 'api/editors/markdown'));
        RMTemplate::get()->add_style('markdown-editor.min.css', 'rmcommon', array('directory' => 'api/editors/markdown'));
        RMTemplate::getInstance()->add_fontawesome();
        RMTemplate::get()->add_inline_script($this->render_js(), 1);

        $plugins = array();
        $plugins = RMEvents::get()->run_event('rmcommon.editor.top.plugins', $plugins, 'markdown', $this->id);

        $rtn = 	"<div class='ed-container' id='".$this->id."-ed-container'>";
        $rtn .= "  <div class='ed-plugins' id='".$this->id."-ed-plugins'>
		              <span class='plugin'>".implode('</span> <span class="plugin">', $plugins).'</span>
		              <button type="button" class="plugin full-screen" accesskey="s" title="'. __('Toggle full screen [S]', 'rmcommon') . '">
		              '.$cuIcons->getIcon('svg-rmcommon-fullscreen').$cuIcons->getIcon('svg-rmcommon-exit-fullscreen').'
		              </button>
                   </div>';
        $rtn .= "  <div class='ed-buttons' id='".$this->id."-buttons-container'>";
        $rtn .= '    <div class="toolbar-1"></div>';
        $rtn .= "  </div>";
        $rtn .= "  <div class=\"txtarea-container\"". $this->render_attributes() ."><textarea id='".$this->id."' name='".$this->id."'>".$this->content."</textarea></div>";
        $rtn .= "</div>";

        return $rtn;
    }
}

<?php
/**
 * Common Utilities Framework for Xoops
 *
 * Copyright © 2015 Eduardo Cortés http://www.redmexico.com.mx
 * -------------------------------------------------------------
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
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 * MA 02110-1301, USA.
 * -------------------------------------------------------------
 * @copyright    Eduardo Cortés (http://www.redmexico.com.mx)
 * @license      GNU GPL 2
 * @package      rmcommon
 * @author       Eduardo Cortés (AKA bitcero)    <i.bitcero@gmail.com>
 * @url          http://www.redmexico.com.mx
 * @url          http://www.eduardocortes.mx
 */

namespace Common\Core\Helpers;

class Uploader
{
    /**
     * Options storing
     * @var array
     */
    protected $options = [];

    private $acceptedOptions = [
        'url', 'method', 'parallelUploads', 'maxFilesize', 'filesizeBase', 'paramName', 'uploadMultiple',
        'headers', 'addRemoveLinks', 'previewsContainer', 'hiddenInputContainer', 'clickable', 'createImageThumbnails',
        'maxThumbnailFilesize', 'thumbnailWidth', 'thumbnailHeight', 'maxFiles', 'resize', 'init', 'acceptedFiled',
        'accept', 'renameFilename', 'autoProcessQueue', 'previewTemplate', 'forceFallback', 'fallback',
        //language options
        'dictDefaultMessage', 'dictFallbackMessage', 'dictFallbackText', 'dictInvalidFileType', 'dictFileTooBig',
        'dictReponseError', 'dictCancelUpload', 'dictCancelUploadConfirmation', 'dictRemoveFile', 'dictMaxFilesEsceeded',
    ];

    protected $htmlID = '';
    protected $camelizedID = '';

    /**
     * Uploader constructor.
     * @param array $options
     * @param mixed $htmlID
     */
    public function __construct($htmlID, $options = [])
    {
        $this->htmlID = $htmlID;

        $explodedID = explode('-', $htmlID);

        foreach ($explodedID as $part) {
            $this->camelizedID .= '' == $this->camelizedID ? mb_strtolower($part) : ucfirst($part);
        }

        $this->options = $options;
    }

    /**
     * Set an option value. If option does nto exists, it will be created.
     *
     * @param $name
     * @param $value
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;
    }

    /**
     * Get an option value. When option does not exists the returned value will be false.
     * @param $name
     * @return mixed|bool
     */
    public function getOption($name)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return false;
    }

    /**
     * Retrieves all stored options
     * @return array
     */
    public function getAllOptions()
    {
        return $this->options;
    }

    /**
     * Empty stored options
     */
    public function clearOptions()
    {
        $this->options = [];
    }

    /**
     * Include the dropzone js file required to work.
     */
    public function includeDropzone()
    {
        global $common;

        $common->template()->add_script('dropzone.min.js', 'rmcommon', ['id' => 'dropzone', 'footer' => 1]);
    }

    /**
     * This method is used to create dropzones programatically.
     * {@link http://www.dropzonejs.com/#usage}
     * @param bool $addDropzone
     */
    public function render($addDropzone = true)
    {
        global $common;

        if ($addDropzone) {
            $this->includeDropzone();
        }

        $params = '';

        foreach ($this->options as $name => $value) {
            if (in_array($name, $this->acceptedOptions, true)) {
                $params .= '' == $params ? '' : ",\n";
                $params .= $name . ':' . (is_string($value) ? '"' . $value . '"' : $value);
            }
        }

        $script = 'Dropzone.options.' . $this->camelizedID . ' = {';

        $script .= $params;

        $script .= '};';

        $common->template()->add_inline_script($script, 1);
    }
}

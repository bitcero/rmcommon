<?php
// $Id: flashuploader.php 999 2012-07-02 03:53:17Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

class RMFlashUploader
{
    private $settings = [];
    public $name = '';

    /**
     * Constructor
     *
     * @param string Name of instance
     * @param string Target uploader url (e.g. uploader.php)
     * @param array SWFUploader settings
     * @param mixed $name
     * @param mixed $url
     * @param mixed $settings
     * @return RMFlashUploader
     */
    public function __construct($name, $url, $settings = [])
    {
        // Generate settings for uploadify
        $this->settings = [
            'swf' => RMCURL . '/include/uploadify.swf',
            'uploader' => $url,
            'auto' => false,
            'buttonClass' => '',
            'buttonCursor' => 'hand',
            'buttonImage' => null,
            'buttonText' => __('Select Files', 'rmcommon'),
            'checkExisting' => false,
            'debug' => false,
            'fileObjName' => 'Filedata',
            'fileSizeLimit' => '512KB',
            'fileTypeDesc' => __('All Files', 'rmcommon'),
            'fileTypeExts' => '*.*',
            'formData' => [],
            'height' => 30,
            'method' => 'post',
            'multi' => true,
            'overrideEvents' => '',
            'preventCaching' => true,
            'progressData' => 'percentage',
            'queueID' => false,
            'queueSizeLimit' => 100,
            'removeCompleted' => true,
            'removeTimeout' => 2,
            'requeueErrors' => false,
            'successTimeout' => 30,
            'uploadLimit' => 999,
            'width' => 120,
            'onCancel' => '',
            'onClearQueue' => '',
            'onDestroy' => '',
            'onDialogClose' => '',
            'onDialogOpen' => '',
            'onDisable' => '',
            'onEnable' => '',
            'onFallback' => '',
            'onInit' => '',
            'onQueueComplete' => '',
            'onSelect' => '',
            'onSelectError' => '',
            'onSWFReady' => '',
            'onUploadComplete' => '',
            'onUploadError' => '',
            'onUploadProgress' => '',
            'onUploadStart' => '',
            'onUploadSuccess' => '',
        ];

        foreach ($settings as $key => $value) {
            if (!isset($this->settings[$key])) {
                continue;
            }
            $this->settings[$key] = $value;
        }

        $this->name = $name;
    }

    public function add_setting($name, $value)
    {
        $convert = [
            'scriptData' => 'formData',
            'onComplete' => 'onUploadComplete',
            'onAllComplete' => 'onQueueComplete',
            'fileExt' => 'fileTypeExts',
            'fileDesc' => 'fileTypeDesc',
            'buttonImg' => 'buttonImage',
            'fileDataName' => 'fileObjName',
            'checkScript' => 'checkExisting',
            'sizeLimit' => 'fileSizeLimit',
            'onOpen' => 'onDialogOpen',
            'onError' => 'onUploadError',
            'onProgress' => 'onUploadProgress',
        ];

        if (isset($convert[$name])) {
            $name = $convert[$name];
        }

        if (!isset($this->settings[$name])) {
            return false;
        }
        $this->settings[$name] = $value;

        return true;
    }

    public function get_setting($name)
    {
        if (!isset($this->settings[$name])) {
            return false;
        }

        return $this->settings[$name];
    }

    /**
     * Add several settings items at once
     *
     * @param array $settings
     */
    public function add_settings($settings)
    {
        foreach ($settings as $key => $value) {
            if (!isset($this->settings[$key])) {
                continue;
            }
            $this->settings[$key] = $value;
        }
    }

    public function settings()
    {
        return $this->settings;
    }

    public function render()
    {
        RMTemplate::get()->add_script('swfobject.js', 'rmcommon', ['directory' => 'include']);
        RMTemplate::get()->add_script('jquery.uploadify.js', 'rmcommon', ['directory' => 'include']);
        RMTemplate::get()->add_style('uploadify.css', 'rmcommon');

        ob_start();
        include RMTemplate::get()->get_template('uploadify.js.php', 'module', 'rmcommon');
        $script = ob_get_clean();

        return $script;
    }
}

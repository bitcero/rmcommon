<?php
/**
 * $Id$
 * --------------------------------------------------------------
 * Common Utilities
 * Author: Eduardo Cortes
 * Email: i.bitcero@gmail.com
 * License: GPL 2.0
 * URI: http://www.redmexico.com.mx
 */

/**
 * This trait allows to integrate AJAX capabilities with rmcommon modules.
 * Class RMModuleAjax
 */
trait RMModuleAjax
{

    /**
     * Prepares the system for an AJAX response.
     * This function deactivate the XoopsLogger rendering
     */
    public function prepare()
    {
        error_reporting(0);
        XoopsLogger::getInstance()->activated = false;
    }

    /**
     * Sends an AJAX response to client.
     * Each parameter correspond to a specifi data to be sent via AJAX.
     * <h3>$data parameter</h3>
     * Contains all customized data to be sent to client, but there exists some required information in order
     * to work properly.
     * <h3>$token parameter</h3>
     * This parameter indicates to method that must to send the securityt token to cliente. Generally, this token
     * can be used in new AJAX transactions.
     *
     * <strong>Example:</strong>
     * <pre>
     * $ajax_data = array(
     *  'fruits' => array('apple','orange','lemon')
     * );
     *
     * $this->ajax_response('Message to sent', 0, 1, $ajax_data);
     * </pre>
     * @param string $message <p>Text message to send to client.</p>
     * @param int $level <p>Indicate it the response sent is an error result (1) or a successful result (0).</p>
     * @param int $token <p>If this parameter is set to 1, then a security token will be sent to client.</p>
     * @param array $data <p>Array with data to send. Each array index must correspond to a parameter to send to client.</p>
     */
    public function response($message, $level = 0, $token = 1, $data = array())
    {
        global $xoopsSecurity;

        if (1 == $token)
            $data['token'] = $xoopsSecurity->createToken(0, 'CUTOKEN');

        $data['type'] = 1 == $level ? 'error' : 'success';
        $data['message'] = $message;

        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        echo json_encode($data);
        die();

    }

    public function responseRaw($data){
        echo json_encode($data);
        die();
    }

    public function notifyError($message, $token = 1){
        $this->response($message, 1, $token == 1 ? 1 : 0, [
            'notify' => [
                'type' => 'alert-danger',
                'icon' => 'svg-rmcommon-error'
            ]
        ]);
    }

    /**
     * Alias for prepare()
     * @deprecated
     */
    public function prepare_ajax_response()
    {
        $this->prepare();
    }

    /**
     * Alias for response()
     * @param $message
     * @param int $level
     * @param int $token
     * @param array $data
     * @deprecated
     */
    public function ajax_response($message, $level = 0, $token = 1, $data = array())
    {
        $this->response($message, $level, $token, $data);
    }
}

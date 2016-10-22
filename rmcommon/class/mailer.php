<?php
// $Id: mailer.php 1019 2012-09-01 06:26:52Z i.bitcero $
// --------------------------------------------------------------
// Red México Common Utilities
// A framework for Red México Modules
// Author: Eduardo Cortés <i.bitcero@gmail.com>
// Email: i.bitcero@gmail.com
// License: GPL 2.0
// --------------------------------------------------------------

include_once RMCPATH.'/class/swift/swift_required.php';

/**
* Mailer Class
*
* @since 2.0
* @package API
* @author Eduardo Cortés <i.bitcero@gmail.com>
* @license: GPL v2
*/
class RMMailer
{
	/**
	* The transport class
	*/
	private $swTransport;
	/**
	* The message class
	*/
	private $swMessage;
	/**
	* The mailet class
	*/
	private $swMailer;
	/**
	* Errors when sending
	*/
	private $errors = array();

    private $vars = array();
    private $template = '';
    private $tpl_type = '';

    private $xusers = array();


	/**
	* Class constructor.
	* Load all data from configurations and generate the initial clases
	* to manage the email
	*
	* @param string Content type for message body. It usually text/plain or text/html.
	* 		Default is 'text/plain' but can be changed later
	*/
	public function __construct($content_type = 'text/plain'){

        $config = RMSettings::cu_settings();
        $config_handler = xoops_gethandler('config');
        $xconfig = $config_handler->getConfigsByCat(XOOPS_CONF_MAILER);

		// Instantiate the Swit Transport according to our preferences
		// We can change this preferences later
		switch($config->transport){
			case 'mail':
				$this->swTransport = Swift_MailTransport::newInstance();
				break;
			case 'smtp':
				$this->swTransport = Swift_SmtpTransport::newInstance($config->smtp_server, $config->smtp_port, $config->smtp_crypt!='none' ? $config->smtp_crypt : '');
				$this->swTransport->setUsername($config->smtp_user);
				$this->swTransport->setPassword($config->smtp_pass);
				break;
			case 'sendmail':
				$this->swTransport = Swift_SendmailTransport::newInstance($config->sendmail_path);
				break;
		}

		// Create the message object
		// Also this object could be change later with message() method
		$this->swMessage = Swift_Message::newInstance();
		$this->swMessage->setReplyTo($xconfig['from']);
		$this->swMessage->setFrom(array($xconfig['from'] => $xconfig['fromname']));
		$this->swMessage->setContentType($content_type);

	}

    public function set_from($mail, $name){
        $this->swMessage->setFrom($mail, $name);
    }

    public function set_from_xuser($user){
        if (strtolower(get_class($user))=='xoopsuser')
            $this->fromuser = $user;
        elseif($user>0)
            $this->fromuser = new XoopsUser($user);
    }

	/**
	* Creating the mail transport
	*/
	public function &transport(){
		return $this->swTransport;
	}

	/**
	* Next functions allows to manage the Message object.
	* You can obtain directly the Swift Message object and work directly with it.
	*/

	/**
	* Get the message object
	*
	* @return object
	*/
	public function &message(){
		return $this->swMessage;
	}

	/**
	* Get the subject line
	*
	* @return string
	*/
	public function get_subject(){
		return $this->swMessage->getSubject();
	}

	/**
	* Set the subject line
	*
	* @param string Subject line
	*/
	public function set_subject($subject){
		$this->swMessage->setSubject($subject);
	}

	/**
	* Returns the body content
	*
	*/
	public function get_body(){
		return $this->swMessage->getBody();
	}

	/**
	* Set the body content and additionally can set the content_type
	* If content type is not provided then it leaves without changes
	*
	* @param string $body
	* @param string $content_type
	*/
	public function set_body($body, $content_type=''){
		$this->swMessage->setBody($body, $content_type!='' ? $content_type : $this->swMessage->getContentType());
	}

	/**
	* Adds a part to message body.
	*
	* @param string Part body content
	* @param string Content type, default is text/html
	*/
	public function add_part($content, $type='text/html'){
		$this->swMessage->addPart($content, $type);
	}

	/**
	* Attach a file
	* Note: Always is preferible get the Message object and work directly with it and with Attachement object
	*
	* @param string Type of file. Can be 'path' for existent files or 'dynamic' for dynamic content
	* @param string File path, when type = 'path'. The file must exists. Also can be a URL
	* @param string Content type for file (mime type)
	* @param string Set file name in the message
	* @param mixed Content for dynamic content
	* @param string File disposition (inline)
	*/
	public function attach_content($type='path', $path = '', $content_type = '', $name='', $content=null, $disposition=''){

		switch($type){
			case 'path':

				if (trim($path)=='' || !is_file($path)) return;
				$att = Swift_Attachment::fromPath($path);

				break;
			case 'dynamic':

				if ($content==null || $content=='') return;
				$att = Swift_Attachment::newInstance($content);

				break;
		}

		if (trim($name)!='') $att->setFilename($name);
		if (trim($content_type)!='') $att->setContentType($content_type);
		if (trim($disposition)!='') $att->setDisposition($disposition);

		$this->swMessage->attach($att);

	}

	/**
	* Embed files into message
	* Note: Is better to use the Message object directly
	*
	* @param string Path or dynamic
	* @param string|mixed Path to file or file content when type is dynamic
	* @param string File name
	* @param string Mime type of image
	* @return int
	*/
	public function embed($type, $file='', $name=null, $content_type=null){

		switch($type){
			case 'path':
				if ($file=='') return null;
				return $this->swMessage->embed(Swift_Image::fromPath($file));
				break;
			case 'dynamic':
				if ($file==null || $file=='') return null;
				return $this->swMessage->embed(Swift_Image::newInstance($file, $name, $content_type));
		}

		return null;
	}

	/**
	* set all recipients in one go
	*
	* @param array Recipientes data array('recipient@address.com'=>'Recipient Name','recipent2@address.com',...)
	*/
	public function set_to($recipients = array()){
		if (empty($recipients)) return null;
		return $this->swMessage->setTo($recipients);
	}

	/**
	* Get recipients for this message
	* @return array
	*/
	public function get_to(){
		return $this->swMessage->getTo();
	}

	/**
	* Add single recipient
	* @param string Email address
	* @param string Name
	*/
	public function add_to($mail, $name=''){
		if ($mail=='') return null;
		return $this->swMessage->addTo($mail, $name);
	}

	/**
	* This methods are similar to previous but for Cc recipients
	* @param array Recipientes data array('recipient@address.com'=>'Recipient Name','recipent2@address.com',...)
	*/
	public function set_cc($recipients = array()){
		if (empty($recipients)) return null;
		return $this->swMessage->setCc($recipients);
	}

	/**
	* Get recipients for this message
	* @return array
	*/
	public function get_cc(){
		return $this->swMessage->getCc();
	}

	/**
	* Add single recipient
	* @param string Email address
	* @param string Name
	*/
	public function add_cc($mail, $name=''){
		if ($mail=='') return null;

		return $this->swMessage->addCc($mail, $name);
	}

	public function set_bcc($recipients = array()){
		if (empty($recipients)) return null;
		return $this->swMessage->seBcc($recipients);
	}

	public function get_bcc(){
		return $this->swMessage->getBcc();
	}

	public function add_bcc($mail, $name=''){
		if ($mail=='') return null;

		return $this->swMessage->addBcc($mail, $name);
	}

    /**
    * Add Users as recipients for message.
    * Users can be passed as object or as ids
    *
    * @param array Users (ids or objects)
    */
    public function add_users($users, $field='to'){
        if (!is_array($users)) return;

        foreach ($users as $user){
            if (is_a($user, "XoopsUser")){
                $this->add_user($user->getVar('email'), $user->getVar('name')!='' ? $user->getVar('name') : $user->getVar('uname'), $field);
            } else {
                $user = new XoopsUser($user);
                if ($user->isNew()) continue;
                $this->add_user($user->getVar('email'), $user->getVar('name')!='' ? $user->getVar('name') : $user->getVar('uname'), $field);
            }
            $this->xusers[] = $user;
        }

    }

    public function add_user($mail, $name='', $field='to'){
        switch($field){
            case 'to':
                $this->add_to($mail, $name);
                break;
            case 'cc':
                $this->add_cc($mail, $name);
                break;
            case 'bcc':
                $this->add_bcc($mail, $name);
                break;
        }
    }

    /**
    * Add xoops users object to recipients list
    *
    * @param mixed A xoopsuser object or an array of xoopsuser objects
    */
    public function add_xoops_users($users, $field='to'){

        if (is_array($users)){

            foreach($users as $uid){
                $user = new RMUser($uid);
                if (strtolower(get_class($user))=='rmuser'){
                    $this->xusers[] = $user;
                    $this->add_user($user->getVar('email'), $user->getVar('name')!='' ? $user->getVar('name') : $user->getVar('uname'), $field);
                }
            }

        } else {
            $user = new RMUser($users);

            if (strtolower(get_class($users))=='xoopsuser'){
                $this->xusers[] = $user;
                $this->add_user($users->getVar('email'), $users->getVar('name')!='' ? $users->getVar('name') : $users->getVar('uname'), $field);
            }

        }

    }

	/**
	* Sets the return path
	* @param string Email for return path
	*/
	public function set_return_path($mail){
		if ($mail=='') return;
		$this->swMessage->setReturnPath($mail);
	}
	public function get_return_path(){
		return $this->swMessage->getReturnPath();
	}

    public function assign($var, $value){
        $this->vars[$var] = $value;
    }

    /**
    * Assign a template to generate the message body
    *
    * @param string Path to file
    * @param string Template type. It can be "old" or ''
    */
    public function template($file, $type=''){
        if (file_exists($file))
            $this->template = $file;
    }

	/**
	* Get errors
	* @return array
	*/
	public function errors(){
		return $this->errors;
	}

    public function create_body(){

        if ($this->get_body()!='') return;

        if($this->template=='') return;

        if ($this->tpl_type=='old'){
            global $xoopsTpl;

            $ret = file_get_contents($this->template);

            foreach($this->vars as $name => $value){
                $ret = str_replace('{'.$name.'}', $value, $ret);
            }

            $this->set_body($ret);
            return;

        }

        extract($this->vars);
        ob_start();

        include $this->template;

        $ret = ob_get_clean();

        $this->set_body($ret);

    }

    function send_pm(){

        if(empty($this->xusers))
            return false;

        if (!$this->fromuser)
            return false;

        $this->create_body();

        $pm_handler = &xoops_gethandler('privmessage');
        $pm = &$pm_handler->create();
        $pm->setVar("subject", $this->get_subject());
        // RMV-NOTIFY
        $pm->setVar('from_userid', $this->fromuser->uid());
        $pm->setVar("msg_text", $this->get_body());

        foreach($this->xusers as $user){
            $pm->setVar("to_userid", $user->uid());
            $pm_handler->insert($pm);
        }

		return null;
	}

	function batchSend(){
        $this->create_body();
		$this->swMailer = Swift_Mailer::newInstance($this->swTransport);
		return $this->swMailer->send($this->swMessage, $this->errors);
	}

	function send(){
        $this->create_body();
		$this->swMailer = Swift_Mailer::newInstance($this->swTransport);
		return $this->swMailer->send($this->swMessage, $this->errors);
	}

}

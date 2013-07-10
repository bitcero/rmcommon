<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

// {{{ Crypt

/**
 * The Crypt class provides an easy and secure way to encrypt and
 * decrypt data using the Symmetric Cryptography method, that is a
 * low cost algorithm that encrypt data based on the shared keys
 * conecpt.
 *
 * @package     Crypt class
 * @category    Cryptography
 * @author      Arthur Furlan <arthur.furlan@gmail.com>
 * @copyright   2006 (c) - Arthur Furlan
 * @license     GPL v3.0 {@link http://gnu.org/licenses/gpl.txt}
 * @link        http://code.google.com/p/cryptclass
 * @version     2.2
 *
 * @todo        Improve class documentation
 */
class Crypt {

    // {{{ Constants
    
    /* Returning modes */
    const MODE_BIN = 0;     // same length of the original data
    const MODE_B64 = 1;     // about 66% bigger
    const MODE_HEX = 2;     // exactly 100% bigger

    // }}}
    // {{{ Attributes

    /**
     * The private key used in the cryptography method.
     * NOTE: This value should be as strange as possible.
     *
     * @name        $_key
     * @access      private
     * @var         string
     */
    private $_key  = __CLASS__;

    /**
     * The returning mode of the cryptography method.
     *
     * @name        $_mode
     * @access      private
     * @var         integer
     * @see         Crypt::{MODE_BIN, MODE_B64, MODE_HEX}
     */
    private $_mode = Crypt::MODE_HEX;

    // }}}
    // {{{ function __construct()

    /**
     * Configure the new object, setting the mode and key.
     *
     * @name        __construct()
     * @access      public
     * @param       [$mode]     integer
     * @param       [$key]      integer
     * @return      void
     */
    function __construct($mode = null, $key = null) {
        is_null($mode) || ($this->Mode = $mode);
        is_null($key)  || ($this->Key  = $key);
    }

    // }}}
    // {{{ function __toString()

    /**
     * Overload of the object conversion to string.
     *
     * @name        __toString()
     * @access      public
     * @method      void
     * @return      string
     */
    function __toString() {
        return __CLASS__ . " object\n"
            . "(\n"
            . "    [Key]  => {$this->_key}\n"
            . "    [Mode] => {$this->_mode}\n"
            . ")\n";
    }

    // }}}
    // {{{ function __set()

    /**
     * Properties write methods.
     *
     * @name        __set()
     * @param       $property   string
     * @param       $value      mixed
     * @return      void
     */
    function __set($property, $value) {
        switch ($property) {
		    case 'Key' : return $this->_setKey($value);
            case 'Mode': return $this->_setMode($value);
        }
    }

    // }}}
    // {{{ function __get()

    /**
     * Properties read methods.
     *
     * @name        __get()
     * @param       $key        string
     * @return      void
     */
    function __get($property) {
        switch ($property) {
            case 'Key' : return $this->_key;
            case 'Mode': return $this->_mode;
        }
    }

    // }}}
    // {{{ public function encrypt()

   /**
     * Encrypt the data using the current returning mode.
     *
     * @name        encrypt()
     * @access      public
     * @param       $data       mixed
     * @return      string
     */
    public function encrypt($data) {
        $data = (string) $data;
        for ($i=0;$i<strlen($data);$i++)
            @$encrypt .= $data[$i] ^
                $this->_key[$i % strlen($this->_key)];
        if ($this->_mode === Crypt::MODE_B64)
            return base64_encode(@$encrypt);
        if ($this->_mode === Crypt::MODE_HEX)
            return $this->_encodeHexadecimal(@$encrypt);
        return @$encrypt;
    }

    // }}}
    // {{{ public function decrypt()

    /**
     * Decrypt the data using the current returning mode.
     * NOTE: You must use the same $_mode of the creation process.
     *
     * @name        decrypt()
     * @access      public
     * @param       $crypt      string
     * @return      string
     */
    public function decrypt($crypt) {
        if ($this->_mode === Crypt::MODE_HEX)
            $crypt = $this->_decodeHexadecimal($crypt);
        if ($this->_mode === Crypt::MODE_B64)
            $crypt = (string)base64_decode($crypt);
        for ($i=0;$i<strlen($crypt);$i++)
            @$data .= $crypt[$i] ^ $this->_key[$i % strlen($this->_key)];
        return @$data;
    }

    // }}}
    // {{{ public static function supportedModes()

    /**
     * Return the list containing all supported modes.
     *
     * @name        supportedModes()
     * @access      public
     * @param       void
     * @return      void
     */
    public static function supportedModes() {
        return array(
            Crypt::MODE_BIN,
            Crypt::MODE_B64,
            Crypt::MODE_HEX
        );
    }

    // }}}
    // {{{ protected static function _isSupportedMode()

    /**
     * Checks if $mode is a valid returning mode of the class.
     *
     * @name        _isSupportedMode()
     * @access      public
     * @param       $mode       integer
     * @return      void
     */
    public static function _isSupportedMode($mode) { 
        return in_array($mode, Crypt::supportedModes());
    }

    // }}}
    // {{{ protected function _setKey()

    /**
     * Set the key used in the cryptography method.
     *
     * @name        _setMode()
     * @access      protected
     * @param       $key        string
     * @return      void
     */
    protected function _setKey($key) {
        $this->_key = (string) $key;
    }

    // }}}
    // {{{ protected function _setMode()

    /**
     * Set the current returning mode of the class.
     *
     * @name        _setMode()
     * @access      protected
     * @param       $mode       integer
     * @return      void
     */
    protected function _setMode($mode) {
        Crypt::_isSupportedMode($mode)
            && ($this->_mode = (int)$mode);
    }

    // }}}
    // {{{ protected function _encodeHexadecimal()

    /**
     * Encode the data using hexadecimal chars.
     *
     * @name        _encodeHexadecimal()
     * @access      protected
     * @param       $data       mixed
     * @return      string
     */
    protected function _encodeHexadecimal($data) {
        $data = (string) $data;
        for ($i=0;$i<strlen($data);$i++)
            @$hexcrypt .= str_pad(dechex(ord(
                $data[$i])), 2, 0, STR_PAD_LEFT);
        return @$hexcrypt;
    }

    // }}}
    // {{{ protected function _decodeHexadecimal()

    /**
     * Decode hexadecimal strings.
     *
     * @name        _decodeHexadecimal()
     * @access      protected
     * @param       $data       string
     * @return      string
     */
    protected function _decodeHexadecimal($hexcrypt) {
        $hexcrypt = (string) $hexcrypt;
        for ($i=0;$i<strlen($hexcrypt);$i+=2)
            @$data .= chr(hexdec(substr($hexcrypt, $i, 2)));
        return @$data;
    }

    // }}}

}

// }}}
?>

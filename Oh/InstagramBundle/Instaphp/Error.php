<?php
namespace Instaphp;

/**
 * Error Object
 * 
 *
 * @package Instaphp
 * @version 1.0
 * @author randy sesser <randy@instaphp.com>
 */
class Error
{
	/**
	 * Error Type
	 * @var string
	 * @access public
	 */
	public $type = null;
	/**
	 * Error Code
	 * @var int
	 * @access public
	 */
	public $code = null;
	/**
	 * Error Message
	 * @var string
	 * @access public
	 */
	public $message = null;
	/**
	 * The url associated with this error
	 *
	 * @var string
	 * @access public
	 **/
	public $url = null;

	/**
	 * The constructor constructs
	 * @param string $type The error type
	 * @param int $code The error code
	 * @param string $message The error message
	 * @return Error
	 * @access public
	 */
	public function __construct($type = null, $code = null, $message = null, $url = null)
	{
		$this->type = $type;
		$this->code = $code;
		$this->message = $message;
		$this->url = $url;
	}
}
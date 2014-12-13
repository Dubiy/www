<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Language Helpers
 *
 * @package		CodeIgniter
 * @subpackage	Helpers
 * @category	Helpers
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/helpers/language_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Lang
 *
 * Fetches a language variable and optionally outputs a form label
 *
 * @access	public
 * @param	string	the language line
 * @param	string	the id of the form element
 * @return	string
 */
if ( ! function_exists('t'))
{
	function t($line)
	{
		$CI =& get_instance();
		$res = $CI->lang->line($line);     
    if ($res == FALSE) {
      // $res = '{'.$line.'}';
      $res = $line;
      $sql = "INSERT INTO `translation_fails` (`lang` , `line`, `uri`, `datetime`) VALUES ('". $_SESSION['language'] ."', '$line', '".$_SERVER['REQUEST_URI']."', '" . date('Y-m-d H:i:s') . "');";
      mysql_query($sql);      
    }   


		return $res;
	}
}

// ------------------------------------------------------------------------
/* End of file language_helper.php */
/* Location: ./system/helpers/language_helper.php */
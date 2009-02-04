<?php
/**
* Recaptcha
* @version 1.0
* @package CalDate
* @author Mark Fabrizio Jr.
* @copyright (C) 2006 by Owl Watch Consulting Services, LLC.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
**/

defined( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

class botSystemRecaptcha{
	
	function &getInstance()
	{
		static $instance;
		if( !isset($instance)){
			$instance = new botSystemRecaptcha();
		}
		return $instance;
	}
	
	function botSystemRecaptcha()
	{
		global $_MAMBOTS, $database;	
		// load mambot params info
		$query = "SELECT params"
		. "\n FROM #__mambots"
		. "\n WHERE element = 'recaptcha'"
		. "\n AND folder = 'system'"
		;
		$database->setQuery( $query );
		$database->loadObject($mambot);
		
		$this->params = new mosParameters( $mambot->params );
		
		require_once(dirname(__FILE__).'/recaptcha/api.php');
		ReCaptcha::setKeys(
			$this->params->get('public', Recaptcha::get('publicKey')),
			$this->params->get('private', Recaptcha::get('privateKey'))
		);
	}
	
	function processPage()
	{
		$option = mosGetParam($_REQUEST, 'option');
		$task = mosGetParam($_REQUEST, 'op');
		
		if( $this->params->get('addToContact',0) == 1 &&
		   $option == 'com_contact' &&
		   $task == 'sendmail'
		){
			return true;
		}
		return false;
	}
	
	function addFormToBuffer()
	{
		$option = mosGetParam($_REQUEST, 'option');
		
		if( $this->params->get('addToContact',0) == 1 && $option == 'com_contact' ){
			return true;
		}
		return false;
	}
	
	function onStart()
	{
		ReCaptcha::process();
		
		if( !$this->processPage() ){
			return;
		}
		if( ReCaptcha::get('submitted') && !ReCaptcha::get('success') ){
			mosErrorAlert('The captcha phrase you entered is incorrect, please try again.');
		}
	}
	
	function onTemplateDisplay()
	{
		global $_MOS_OPTION;
		if( !$this->addFormToBuffer() ){
			return;
		}
		$buffer = $_MOS_OPTION['buffer'];
		
		// add it before the submit button
		$re = "/<(button|input)(.*name=['\"]send['\"].*)?>/i";
		$buffer = preg_replace_callback($re, array(&$this,'_addFormCallback'), $buffer);
		$_MOS_OPTION['buffer'] = $buffer;
	}
	
	function _addFormCallback($matches)
	{
		return ReCaptcha::get('html').'<br />'.$matches[0];
	}
	
}
function botSystemRecaptchaOnStart()
{
	$inst =& botSystemRecaptcha::getInstance();
	$inst->onStart();
}
function botSystemRecaptchaOnTemplateDisplay()
{
	$inst =& botSystemRecaptcha::getInstance();
	$inst->onTemplateDisplay();
}
$_MAMBOTS->registerFunction( 'onStart', 'botSystemRecaptchaOnStart' );
$_MAMBOTS->registerFunction( 'onTemplateDisplay', 'botSystemRecaptchaOnTemplateDisplay' );
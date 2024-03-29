<?php
/**
* CalDateBot - replace standard Joomla Dates with fun calendar dates.
* @version 1.0
* @package CalDate
* @author Mark Fabrizio Jr.
* @copyright (C) 2006 by Owl Watch Consulting Services, LLC.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
**/

defined( '_JEXEC') or ( '_VALID_MOS' ) or die( 'Direct Access to this location is not allowed.' );

jimport('joomla.plugin.plugin');

class plgSystemRecaptcha extends JPlugin{
	
	function plgSystemRecaptcha( &$subject, $config)
	{
		parent::__construct($subject, $config);
		
		require_once(dirname(__FILE__).'/recaptcha/api.php');
		ReCaptcha::setKeys(
			$this->params->get('public', Recaptcha::get('publicKey')),
			$this->params->get('private', Recaptcha::get('privateKey'))
		);
	}
	
	function processPage()
	{
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		$task = JRequest::getCmd('task');
		
		if( $this->params->get('addToContact',1) == 1 &&
		   $option == 'com_contact' &&
		   $task == 'submit'
		){
			return true;
		}
		return false;
	}
	
	function addFormToBuffer()
	{
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		if( $this->params->get('addToContact',1) == 1 && $option == 'com_contact' && $view == 'contact' ){
			return true;
		}
		return false;
	}
	
	function onAfterInitialise()
	{
		ReCaptcha::process();
	}
	
	function onAfterRoute()
	{
		if( !$this->processPage() ){
			return;
		}
		if( ReCaptcha::get('submitted') && !ReCaptcha::get('success') ){
			JRequest::setVar('task','display');
		}
	}
	
	function onAfterDispatch()
	{
		if( !$this->addFormToBuffer() ){
			return;
		}
		$document =& JFactory::getDocument();
		$buffer = $document->getBuffer('component');
		
		// add it before the submit button
		$re = "/<(button|input)(.*type=['\"]submit['\"].*)?>/i";
		$buffer = preg_replace_callback($re, array(&$this,'_addFormCallback'), $buffer);
		
		// set values...
		$inputsRe = "/<input(.*name=(['\"])(.+?)\\2.*)?>/i";
		$textareaRe = "/<textarea(.*name=(['\"])text\\2.*)?>(.*)?<\/textarea>/i";
		
		$buffer = preg_replace_callback($inputsRe, array(&$this,'_addInputValues'), $buffer);
		$buffer = preg_replace_callback($textareaRe, array(&$this,'_addTextareaValue'), $buffer);
		
		$document->setBuffer($buffer,'component');
	}
	
	function _addFormCallback($matches)
	{
		return ReCaptcha::get('html').'<br />'.$matches[0];
	}
	
	function _addInputValues($matches)
	{
		switch($matches[3]){
			case 'name':
			case 'email':
			case 'subject':
				$re = "/value=(['\"])(.*?)\\1/i";
				$this->_replacementValue = JRequest::getVar($matches[3]);
				$matches[0] = preg_replace_callback($re, array(&$this, '_replaceValue'), $matches[0]);
				break;
		}
		return $matches[0];
	}
	
	function _addTextareaValue($matches)
	{
		$attrs = $matches[1];
		$val = JRequest::getString('text');
		return "<textarea$attrs>$val</textarea>";
	}
	
	function _replaceValue($matches)
	{
		$val = addslashes($this->_replacementValue);
		return "value='$val'";
	}
	
	
}

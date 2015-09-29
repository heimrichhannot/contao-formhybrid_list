<?php 

define('FORMHYBRID_METHOD_GET', 'GET');
define('FORMHYBRID_METHOD_POST', 'POST');
define('FORMHYBRID_NAME_SUBMIT', 'submit');
define('FORMHYBRID_MESSAGE_SUCCESS', 'FORMHYBRID_SUCCESS');
define('FORMHYBRID_MESSAGE_ERROR', 'FORMHYBRID_ERROR');
define('FORMHYBRID_NAME_SKIP_VALIDATION', 'skipvalidation');

/**
 * Content elements
 */
$GLOBALS['TL_CTE']['formhybrid'] = array(
	'formhybridStart'   => 'HeimrichHannot\FormHybrid\ContentFormHybridStart',
	'formhybridElement' => 'HeimrichHannot\FormHybrid\ContentFormHybridElement',
	'formhybridStop'    => 'HeimrichHannot\FormHybrid\ContentFormHybridStop',
);

/**
 * Indent elements
 */
$GLOBALS['TL_WRAPPERS']['start'][] = 'formhybridStart';
$GLOBALS['TL_WRAPPERS']['stop'][]  = 'formhybridStop';

/**
 * Javascript
 */
if(TL_MODE == 'FE')
{
	$GLOBALS['TL_JAVASCRIPT']['jquery.formhybrid'] = 'system/modules/formhybrid/assets/js/jquery.formhybrid.js|static';
}
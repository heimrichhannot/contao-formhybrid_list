<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_settings'];

/**
 * Palettes
 */
$arrDca['palettes']['default'] .= '{formhybrid_list_legend},shareExpirationInterval;';

/**
 * Fields
 */
$arrFields = array(
	'shareExpirationInterval' => array
	(
		'label'     => &$GLOBALS['TL_LANG']['tl_settings']['shareExpirationInterval'],
		'exclude'   => true,
		'inputType' => 'timePeriod',
		'options'   => array('m', 'h', 'd'),
		'reference' => &$GLOBALS['TL_LANG']['MSC']['timePeriod'],
		'eval'      => array('mandatory' => true, 'tl_class' => 'w50')
	)
);

$arrDca['fields'] = array_merge($arrFields, $arrDca['fields']);
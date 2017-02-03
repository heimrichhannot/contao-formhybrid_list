<?php

$arrDca = &$GLOBALS['TL_DCA']['tl_settings'];

/**
 * Palettes
 */
$arrDca['palettes']['default'] .= '{formhybrid_list_legend},shareExpirationInterval;';

/**
 * Fields
 */
$arrFields = [
	'shareExpirationInterval' => [
        'label'     => &$GLOBALS['TL_LANG']['tl_settings']['shareExpirationInterval'],
        'exclude'   => true,
        'inputType' => 'timePeriod',
        'options'   => ['m', 'h', 'd'],
        'reference' => &$GLOBALS['TL_LANG']['MSC']['timePeriod'],
        'eval'      => ['mandatory' => true, 'tl_class' => 'w50']]
];

$arrDca['fields'] = array_merge($arrFields, $arrDca['fields']);
<?php
/**
 * Contao Open Source CMS
 * 
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package formhybrid
 * @author Rico Kaltofen <r.kaltofen@heimrich-hannot.de>
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */


$dc = &$GLOBALS['TL_DCA']['tl_content'];

// selector
array_insert($dc['palettes']['__selector__'], 0, array('formhybridElement')); // bug? mustn't be inserted after type selector

/**
 * Palettes
 */
$dc['palettes']['formhybridStart'] = '{type_legend},type;{formhybrid_legend},formhybridModule;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';
$dc['palettes']['formhybridElement'] = '{type_legend},type;{formhybrid_legend},formhybridElement;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space;{invisible_legend:hide},invisible,start,stop';

$arrFields = array
(
	'formhybridModule' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_content']['module'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('tl_content_formhybrid', 'getModules'),
		'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true),
		'wizard' => array
		(
			array('tl_content_formhybrid', 'editModule')
		),
		'sql'                     => "int(10) unsigned NOT NULL default '0'"
	),
	'formhybridElement' => array
	(
		'label'                   => &$GLOBALS['TL_LANG']['tl_content']['formhybridElement'],
		'exclude'                 => true,
		'inputType'               => 'select',
		'options_callback'        => array('tl_content_formhybrid', 'getElements'),
		'eval'                    => array('mandatory'=>true, 'chosen'=>true, 'submitOnChange'=>true, 'includeBlankOption' => true),
		'sql'                     => "varchar(64) NOT NULL default ''"
	),
);

$dc['fields'] = array_merge($dc['fields'], $arrFields);

class tl_content_formhybrid extends Backend
{

	public function getElements()
	{
		$arrOptions = array();

		$arrElements = &$GLOBALS['TL_FORMHYBRID_ELEMENTS'];

		if(!is_array($arrElements) || empty($arrElements)) return $arrOptions;

		foreach($arrElements as $key => $strClass)
		{
			$strLabel = isset($GLOBALS['TL_LANG']['tl_content']['formhybrid_element'][$key]) ? $GLOBALS['TL_LANG']['tl_content']['formhybrid_element'][$key] : $key;
			$arrOptions[$key] = $strLabel;
		}

		return $arrOptions;
	}


	/**
	 * Return the edit module wizard
	 * @param \DataContainer
	 * @return string
	 */
	public function editModule(DataContainer $dc)
	{
		return ($dc->value < 1) ? '' : ' <a href="contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $dc->value . '&amp;popup=1&amp;nb=1&amp;rt=' . REQUEST_TOKEN . '" title="' . sprintf(specialchars($GLOBALS['TL_LANG']['tl_content']['editalias'][1]), $dc->value) . '" style="padding-left:3px" onclick="Backend.openModalIframe({\'width\':768,\'title\':\'' . specialchars(str_replace("'", "\\'", sprintf($GLOBALS['TL_LANG']['tl_content']['editalias'][1], $dc->value))) . '\',\'url\':this.href});return false">' . Image::getHtml('alias.gif', $GLOBALS['TL_LANG']['tl_content']['editalias'][0], 'style="vertical-align:top"') . '</a>';
	}


	/**
	 * Get all modules and return them as array
	 * @return array
	 */
	public function getModules()
	{
		$arrModules = array();
		$objModules = $this->Database->prepare("SELECT m.id, m.name, t.name AS theme FROM tl_module m LEFT JOIN tl_theme t ON m.pid=t.id WHERE formHybridDataContainer!='default' AND formHybridDataContainer!='' ORDER BY t.name, m.name")->execute();

		while ($objModules->next())
		{
			$arrModules[$objModules->theme][$objModules->id] = $objModules->name . ' (ID ' . $objModules->id . ')';
		}

		return $arrModules;
	}
}
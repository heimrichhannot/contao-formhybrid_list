<?php

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\Haste\DateUtil;
use HeimrichHannot\Haste\Util\Url;

class FormHybridList
{
    const ACT_SHARE                                   = 'share';
    const PARAM_RANDOM                                = 'random';
    const PROXIMITY_SEARCH_RADIUS                     = 'proxRadius';
    const PROXIMITY_SEARCH_USE_LOCATION               = 'proxUseLocation';
    const PROXIMITY_SEARCH_LOCATION                   = 'proxLocation';
    const PROXIMITY_SEARCH_RADIUS_STEPS               = [
        '1km',
        '5km',
        '10km',
        '25km',
        '50km',
        '100km',
        '200km'
    ];
    const PROXIMITY_SEARCH_COORDINATES_MODE_COMPOUND  = 'compound';
    const PROXIMITY_SEARCH_COORDINATES_MODE_SEPARATED = 'separated';
    const PROXIMITY_SEARCH_COORDINATES_MODES          = [
        self::PROXIMITY_SEARCH_COORDINATES_MODE_COMPOUND,
        self::PROXIMITY_SEARCH_COORDINATES_MODE_SEPARATED
    ];

    public static function addShareFields($strDca)
    {
        \Controller::loadDataContainer($strDca);

        $arrDca = &$GLOBALS['TL_DCA'][$strDca];

        $arrDca['fields']['shareToken'] = [
            'eval' => ['doNotCopy' => true],
            'sql'  => "varchar(23) NOT NULL default ''",
        ];

        $arrDca['fields']['shareTokenTime'] = [
            'eval' => ['doNotCopy' => true],
            'sql'  => "int(10) unsigned NOT NULL default '0'",
        ];
    }

    public static function shareTokenExpiredOrEmpty($objEntity, $intNow)
    {
        $strShareToken         = $objEntity->shareToken;
        $arrExpirationInterval = deserialize(\Config::get('shareExpirationInterval'), true);
        $intInterval           = 604800; // default: 7 days

        if (isset($arrExpirationInterval['unit']) && isset($arrExpirationInterval['value']) && $arrExpirationInterval['value'] > 0) {
            $intInterval = DateUtil::getTimePeriodInSeconds($arrExpirationInterval);
        }

        return !$strShareToken || !$objEntity->shareTokenTime || ($objEntity->shareTokenTime > $intNow + $intInterval);
    }

    public static function addProximitySearchFields($strDca)
    {
        \Controller::loadDataContainer($strDca);
        \System::loadLanguageFile('tl_module');

        $arrDca = &$GLOBALS['TL_DCA'][$strDca];

        $arrDca['fields'][FormHybridList::PROXIMITY_SEARCH_RADIUS] = [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchRadius'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => static::PROXIMITY_SEARCH_RADIUS_STEPS,
            'default'   => '5km',
            'eval'      => ['mandatory' => true],
            'sql'       => "varchar(16) NOT NULL default ''"
        ];

        $arrDca['fields'][FormHybridList::PROXIMITY_SEARCH_USE_LOCATION] = [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchUseLocation'],
            'exclude'   => true,
            'inputType' => 'checkbox',
            'eval'      => ['skipTransformToSelect' => true],
            'sql'       => "char(1) NOT NULL default ''"
        ];

        $arrDca['fields'][FormHybridList::PROXIMITY_SEARCH_LOCATION] = [
            'label'     => &$GLOBALS['TL_LANG']['tl_module']['proximitySearchLocation'],
            'exclude'   => true,
            'inputType' => 'text',
            'sql'       => "varchar(16) NOT NULL default ''"
        ];
    }

    public static function addInsertTags($strTag)
    {
        $arrTag = explode('::', $strTag);

        switch ($arrTag[0]) {
            // {{fhl_filter_url::<pageId>::<moduleId>::<filterQuery>}}
            case 'fhl_filter_url':
                if (($objModule = \ModuleModel::findByPk($arrTag[2])) === null) {
                    return '';
                }

                return Url::addQueryString(
                    sprintf('FORM_SUBMIT=%s_%s&%s', $objModule->formHybridDataContainer, $arrTag[2], $arrTag[3]),
                    Url::generateFrontendUrl($arrTag[1])
                );
                break;
        }

        return false;
    }

}
<?php

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\Haste\DateUtil;

class FormHybridList
{
    const ACT_SHARE    = 'share';
    const PARAM_RANDOM = 'random';

    public static function addShareFields($strDca)
    {
        \Controller::loadDataContainer($strDca);

        $arrDca = &$GLOBALS['TL_DCA'][$strDca];

        $arrDca['fields']['shareToken'] = array(
            'eval' => array('doNotCopy' => true),
            'sql'  => "varchar(23) NOT NULL default ''",
        );

        $arrDca['fields']['shareTokenTime'] = array(
            'eval' => array('doNotCopy' => true),
            'sql'  => "int(10) unsigned NOT NULL default '0'",
        );
    }

    public static function shareTokenExpiredOrEmpty($objEntity, $intNow)
    {
        $strShareToken         = $objEntity->shareToken;
        $arrExpirationInterval = deserialize(\Config::get('shareExpirationInterval'), true);
        $intInterval           = 604800; // default: 7 days

        if (isset($arrExpirationInterval['unit']) && isset($arrExpirationInterval['value']) && $arrExpirationInterval['value'] > 0)
        {
            $intInterval = DateUtil::getTimePeriodInSeconds($arrExpirationInterval);
        }

        return !$strShareToken || !$objEntity->shareTokenTime || ($objEntity->shareTokenTime > $intNow + $intInterval);
    }
}
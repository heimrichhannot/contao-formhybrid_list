<?php

namespace HeimrichHannot\FormHybridList\Dca;

use Contao\Controller;
use Contao\Database;
use Contao\DataContainer;
use HeimrichHannot\Haste\Dca\General;

class SessionField
{
    const FIELD_NAME = 'sessionID';

    /**
     * Add the session id field to your dca
     *
     * @param $strTable
     * @return void
     */
    public static function addSessionIdFieldAndCallback($strTable)
    {
        Controller::loadDataContainer($strTable);

        // callback
        $GLOBALS['TL_DCA'][$strTable]['config']['oncreate_callback']['setSessionID'] = [self::class, 'onSessionIdCreateCallback'];

        // field
        $GLOBALS['TL_DCA'][$strTable]['fields'][static::FIELD_NAME] = [
            'label' => &$GLOBALS['TL_LANG']['MSC']['haste_plus'][static::FIELD_NAME],
            'sql'   => "varchar(128) NOT NULL default ''",
        ];
    }

    /**
     * Set the session id when the form entry is created
     *
     * @param $strTable
     * @param $intId
     * @param $arrRow
     * @param DataContainer $dc
     * @return false|void
     */
    public static function onSessionIdCreateCallback($strTable, $intId, $arrRow, DataContainer $dc)
    {
        $objModel = General::getModelInstance($strTable, $intId);

        if ($objModel === null || !Database::getInstance()->fieldExists(static::FIELD_NAME, $strTable)) {
            return false;
        }

        $objModel->sessionID = session_id();
        $objModel->save();
    }
}
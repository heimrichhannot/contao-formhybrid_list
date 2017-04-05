<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) Heimrich & Hannot GmbH
 *
 * @package formhybrid_list
 * @author  Dennis Patzer
 * @license http://www.gnu.org/licences/lgpl-3.0.html LGPL
 */

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\FormHybrid\Form;
use HeimrichHannot\Haste\Util\Url;

class ListFilterForm extends Form
{
    protected $isFilterForm = true;
    protected $objListModule;

    public function __construct($objModule)
    {
        $this->strMethod                   = FORMHYBRID_METHOD_GET;
        $this->isFilterForm                = true;
        $this->objListModule               = $objModule;
        $objModule->formHybridTemplate     = $objModule->formHybridTemplate ?: 'formhybrid_list_filter';
        $objModule->formHybridEditable     = $objModule->customFilterFields;
        $objModule->formHybridCustomSubmit = true;
        $objModule->formHybridSubmitLabel  = 'filter';

        $arrHeadline                         = deserialize($objModule->filterHeadline);
        $objModule->filterHeadline           = is_array($arrHeadline) ? $arrHeadline['value'] : $arrHeadline;
        $objModule->filterHl                 = is_array($arrHeadline) ? $arrHeadline['unit'] : 'h1';
        $objModule->formHybridSkipValidation = true;

        parent::__construct($objModule);
    }

    protected function onSubmitCallback(\DataContainer $dc)
    {
        $this->submission = $dc;

        if (is_array($this->arrSubmitCallbacks) && !empty($this->arrSubmitCallbacks))
        {
            foreach ($this->arrSubmitCallbacks as $arrCallback)
            {
                if (is_array($arrCallback) && !empty($arrCallback))
                {
                    $arrCallback[0]::$arrCallback[1]($dc);
                }
            }
        }
    }

    protected function compile() { }

    protected function generateResetFilterField()
    {
        $arrData = [
            'inputType' => 'explanation',
            'eval'      => [
                'text' => '<div class="form-group reset-filter"><a class="btn btn-default" href="' . Url::getCurrentUrlWithoutParameters() . '"><span>'
                          . $GLOBALS['TL_LANG']['formhybrid_list'][FORMHYBRID_LIST_BUTTON_RESET_FILTER][0] . '</span></a></div>',
            ],
        ];

        $this->arrFields[FORMHYBRID_LIST_BUTTON_RESET_FILTER] = $this->generateField(FORMHYBRID_LIST_BUTTON_RESET_FILTER, $arrData);
    }

    protected function generateSubmitField()
    {
        $this->generateResetFilterField();

        $strLabel = &$GLOBALS['TL_LANG']['MSC']['formhybrid']['submitLabels']['default'];
        $strClass = 'btn btn-primary';

        if ($this->strSubmit != '' && isset($this->arrFields[$this->strSubmit]))
        {
            return false;
        }

        if ($this->customSubmit)
        {
            if ($this->submitLabel != '')
            {
                $strLabel = $GLOBALS['TL_LANG']['MSC']['formhybrid']['submitLabels'][$this->submitLabel];
            }

            $strClass = $this->submitClass;
        }

        $arrData = [
            'inputType' => 'submit',
            'label'     => is_array($strLabel) ? $strLabel : [$strLabel],
            'eval'      => ['class' => $strClass],
        ];

        $this->arrFields[FORMHYBRID_NAME_SUBMIT] = $this->generateField(FORMHYBRID_NAME_SUBMIT, $arrData);
    }

    public function modifyDC(&$arrDca = null)
    {
        foreach ($arrDca['fields'] as $strField => $arrData)
        {
            switch ($arrData['inputType'])
            {
                case 'textarea':
                    $arrDca['fields'][$strField]['inputType'] = 'text';
                    break;
                case 'checkbox':
                    // Replace boolean checkbox value with "yes" and "no"
                    if (!$arrData['eval']['multiple'] && !$arrData['eval']['skipTransformToSelect'])
                    {
                        $arrDca['fields'][$strField]['eval']['includeBlankOption'] = true;
                        $arrDca['fields'][$strField]['eval']['isBoolean']          =
                            true; // required to be set within Modulelist::applyFilters() cause checkbox is select there
                        $arrDca['fields'][$strField]['inputType']                  = 'select';
                        $arrDca['fields'][$strField]['options']                    = ['1', '0'];
                        $arrDca['fields'][$strField]['reference']                  = &$GLOBALS['TL_LANG']['formhybrid_list']['reference']['yes_no'];
                    }
                    break;
            }
        }

        $this->objListModule->modifyDC($arrDca);
    }
}

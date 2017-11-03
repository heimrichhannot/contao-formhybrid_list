<?php
/**
 * Created by PhpStorm.
 * User: mkunitzsch
 * Date: 27.09.17
 * Time: 09:48
 */

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\Request\Request;

class ModuleFilter extends \Module
{
    protected $strTemplate = 'mod_formhybrid_filter';

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate           = new \BackendTemplate('be_wildcard');
            $objTemplate->wildcard = '### ' . utf8_strtoupper($GLOBALS['TL_LANG']['FMD'][$this->type][0] ?: $this->type) . ' ###';
            $objTemplate->title    = $this->headline;
            $objTemplate->id       = $this->id;
            $objTemplate->link     = $this->name;
            $objTemplate->href     = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        \DataContainer::loadDataContainer($this->formHybridDataContainer);
        \System::loadLanguageFile($this->formHybridDataContainer);

        $this->dca = $GLOBALS['TL_DCA'][$this->formHybridDataContainer];

        $this->strWrapperId .= $this->id;


        return parent::generate();

    }

    public function compile()
    {
        // reset id to serve correct id for target formhybrid_list
        $this->id = $this->formHybridLinkedList;

        $this->objFilterForm        = new ListFilterForm($this);
        $this->Template->filterForm = $this->objFilterForm->generate();
    }

    public function modifyDC(&$arrDca = null)
    {
        foreach ($arrDca['fields'] as $strField => $arrData) {
            if ($arrData['inputType'] == 'select' && $strField != FormHybridList::PROXIMITY_SEARCH_RADIUS) {
                $arrDca['fields'][$strField]['eval']['includeBlankOption'] = true;
            }
        }

        if ($this->addProximitySearch) {
            $arrDca['fields'][FormHybridList::PROXIMITY_SEARCH_LOCATION] = [
                'inputType' => 'hidden'
            ];
        }
    }
}
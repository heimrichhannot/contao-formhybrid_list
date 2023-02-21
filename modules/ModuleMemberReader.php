<?php

namespace HeimrichHannot\FormHybridList;

use HeimrichHannot\Haste\Util\Files;

class ModuleMemberReader extends ModuleReader
{
    public const TYPE = 'formhybrid_member_reader';

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

        return parent::generate();
    }

    protected function parseItem($objItem, $strClass = '', $intCount = 0)
    {
        if (in_array('member_content_archives', \ModuleLoader::getActive())) {
            $arrFilterTags          = deserialize($this->memberContentArchiveTags, true);
            $objItem->memberContent = '';

            if (($objMemberContentArchives =
                    \HeimrichHannot\MemberContentArchives\MemberContentArchiveModel::findPublishedByMid($objItem->memberId ?: $objItem->id)) !== null
            ) {
                while ($objMemberContentArchives->next()) {
                    if (in_array($objMemberContentArchives->tag, $arrFilterTags)) {
                        $objItem->memberContentId = $objMemberContentArchives->id;
                        $objElement               =
                            \ContentModel::findPublishedByPidAndTable($objMemberContentArchives->id, 'tl_member_content_archive');

                        if ($objElement !== null) {
                            while ($objElement->next()) {
                                $objItem->memberContent .= \Controller::getContentElement($objElement->current());
                            }
                        }
                    }
                }

                if ($objMemberContentArchives->tag == $this->memberContentArchiveTeaserTag) {
                    $objItem->memberContentTitle  = $objMemberContentArchives->title;
                    $objItem->memberContentTeaser = $objMemberContentArchives->teaser;
                }

                // override member fields
                $arrOverridableMemberFields = deserialize(\Config::get('overridableMemberFields'));

                if (!empty($arrOverridableMemberFields)) {
                    foreach ($arrOverridableMemberFields as $strField) {
                        $strFieldOverride = 'member' . ucfirst($strField);
                        if ($objMemberContentArchives->{$strFieldOverride}) {
                            if (\Validator::isUuid($objMemberContentArchives->{$strFieldOverride})) {
                                $objMemberContentArchives->{$strFieldOverride} =
                                    Files::getPathFromUuid($objMemberContentArchives->{$strFieldOverride});
                            }

                            $objItem->{$strField} = $objMemberContentArchives->{$strFieldOverride};
                        }
                    }
                }
            }
        }

        return parent::parseItem($objItem, $strClass, $intCount);
    }


}

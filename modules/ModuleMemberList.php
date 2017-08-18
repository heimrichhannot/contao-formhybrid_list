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

use HeimrichHannot\Haste\Util\Files;

class ModuleMemberList extends ModuleList
{
    protected function generateFields($objItem)
    {
        $arrItem = parent::generateFields($objItem);

        if (in_array('member_content_archives', \ModuleLoader::getActive()))
        {
            $arrFilterTags                      = deserialize($this->memberContentArchiveTags, true);
            $arrItem['fields']['memberContent'] = '';

            if (($objMemberContentArchives = \HeimrichHannot\MemberContentArchives\MemberContentArchiveModel::findBy(
                    ['mid=?', 'published=?'],
                    [$objItem->memberId ?: $objItem->id, true]
                )) !== null
            )
            {
                while ($objMemberContentArchives->next())
                {
                    if (in_array($objMemberContentArchives->tag, $arrFilterTags))
                    {
                        $arrItem['fields']['memberContentId'] = $objMemberContentArchives->id;
                        $objElement                           =
                            \ContentModel::findPublishedByPidAndTable($objMemberContentArchives->id, 'tl_member_content_archive');

                        if ($objElement !== null)
                        {
                            while ($objElement->next())
                            {
                                $arrItem['fields']['memberContent'] .= \Controller::getContentElement($objElement->current());
                            }
                        }
                    }
                }

                if ($objMemberContentArchives->tag == $this->memberContentArchiveTeaserTag)
                {
                    $arrItem['fields']['memberContentTitle']  = $objMemberContentArchives->title;
                    $arrItem['fields']['memberContentTeaser'] = $objMemberContentArchives->teaser;
                }

                // override member fields
                $arrOverridableMemberFields = deserialize(\Config::get('overridableMemberFields'));

                if (!empty($arrOverridableMemberFields))
                {
                    foreach ($arrOverridableMemberFields as $strField)
                    {
                        $strFieldOverride = 'member' . ucfirst($strField);
                        if ($objMemberContentArchives->{$strFieldOverride})
                        {
                            if (\Validator::isUuid($objMemberContentArchives->{$strFieldOverride}))
                            {
                                $objMemberContentArchives->{$strFieldOverride} =
                                    Files::getPathFromUuid($objMemberContentArchives->{$strFieldOverride});
                            }

                            $arrItem['fields'][$strField] = $objMemberContentArchives->{$strFieldOverride};
                        }
                    }
                }
            }
        }

        return $arrItem;
    }
}
<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2015 Heimrich & Hannot GmbH
 * @package formhybrid_list
 * @author Dennis Patzer <d.patzer@heimrich-hannot.de>
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
			$arrFilterTags = deserialize($this->memberContentArchiveTags, true);
			$arrItem['fields']['memberContent'] = '';

			if (($objMemberContentArchives = \HeimrichHannot\MemberContentArchives\MemberContentArchiveModel::findBy('mid', $objItem->memberId ?: $objItem->id)) !== null)
			{
				while ($objMemberContentArchives->next())
				{
					if (in_array($objMemberContentArchives->tag, $arrFilterTags))
					{
						$arrItem['fields']['memberContentId'] = $objMemberContentArchives->id;
						$objElement = \ContentModel::findPublishedByPidAndTable($objMemberContentArchives->id, 'tl_member_content_archive');

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
					$arrItem['fields']['memberContentTitle'] = $objMemberContentArchives->title;
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
								$objMemberContentArchives->{$strFieldOverride} =
										Files::getPathFromUuid($objMemberContentArchives->{$strFieldOverride});

							$arrItem['fields'][$strField] = $objMemberContentArchives->{$strFieldOverride};
						}
					}
				}
			}
		}

		return $arrItem;
	}

	protected function runBeforeTemplateParsing($objTemplate, $arrItem)
	{
		if (!$this->useDummyImage)
			return;

		if ($arrItem['fields']['addImage'] && $arrItem['fields']['singleSRC'] != '')
		{
			$this->addImage($arrItem['raw'], 'singleSRC', $arrItem);
			if (is_file(TL_ROOT . '/' . $arrItem['fields']['singleSRC'])) {
				$this->addImageToTemplate($objTemplate, $arrItem['fields']);
			}
		}
		elseif ($this->useDummyImage && $this->dummyImage)
		{
			$arrItem['fields']['addImage'] = true;
			$arrItem['fields']['singleSRC'] = Files::getPathFromUuid($this->dummyImage);

			$this->addImage($arrItem['raw'], 'singleSRC', $arrItem);

			if (is_file(TL_ROOT . '/' . $arrItem['fields']['singleSRC'])) {
				$this->addImageToTemplate($objTemplate, $arrItem['fields']);
			}
		}
	}
}

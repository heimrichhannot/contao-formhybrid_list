<?php

/**
 * Front end modules
 */

use HeimrichHannot\FormHybridList\ModuleList;
use HeimrichHannot\FormHybridList\ModuleListFilter;
use HeimrichHannot\FormHybridList\ModuleMemberList;
use HeimrichHannot\FormHybridList\ModuleMemberReader;
use HeimrichHannot\FormHybridList\ModuleNewsList;
use HeimrichHannot\FormHybridList\ModuleReader;

$GLOBALS['TL_LANG']['FMD'][ModuleReader::TYPE]       = ['FormHybrid-Leser', ''];
$GLOBALS['TL_LANG']['FMD'][ModuleMemberReader::TYPE] = ['FormHybrid-Mitgliederleser', ''];
$GLOBALS['TL_LANG']['FMD'][MODULE_FORMHYBRID_LISTS]  = ['FormHybrid-Listen', ''];
$GLOBALS['TL_LANG']['FMD'][ModuleList::TYPE]         = ['FormHybrid-Liste', ''];
$GLOBALS['TL_LANG']['FMD'][ModuleMemberList::TYPE]   = ['FormHybrid-Mitgliederliste', ''];
$GLOBALS['TL_LANG']['FMD'][ModuleNewsList::TYPE]     = ['FormHybrid-Nachrichtenliste', ''];
$GLOBALS['TL_LANG']['FMD'][ModuleListFilter::TYPE]   = ['FormHybrid-Listenfilter', ''];

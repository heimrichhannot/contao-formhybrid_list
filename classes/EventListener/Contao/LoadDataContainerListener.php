<?php

namespace HeimrichHannot\FormHybridList\EventListener\Contao;

use HeimrichHannot\Modal\Modal;

class LoadDataContainerListener
{
    public function __invoke(string $table): void
    {
        if ('tl_module' === $table && class_exists(Modal::class)) {
            $arrDca = &$GLOBALS['TL_DCA']['tl_module'];
            $arrDca['subpalettes']['addDetailsCol'] = 'useModalExplanation,useModal,'($arrDca['subpalettes']['addDetailsCol'] ?? '');
        }
    }
}
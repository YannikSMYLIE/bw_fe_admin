<?php

// Hook für List-View einfügen
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['typo3/class.db_list_extra.inc']['actions'][] = \BoergenerWebdesign\BwFeAdmin\Hooks\FeUserRecordListHook::class;
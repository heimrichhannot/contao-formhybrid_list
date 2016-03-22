<?php


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_module']['hideFilter']							= array('Filter verstecken', 'Klicken Sie hier, um den Filter zu verstecken.');
$GLOBALS['TL_LANG']['tl_module']['filterTitle']							= array('Filterüberschrift', 'Geben Sie hier eine Überschrift für den Filter ein.');
$GLOBALS['TL_LANG']['tl_module']['showItemCount']						= array('Ergebnisanzahl anzeigen', 'Klicken Sie hier, um die Anzahl der gefundenen Objekte anzuzeigen.');
$GLOBALS['TL_LANG']['tl_module']['showInitialResults']					= array('Initial Ergebnisse anzeigen', 'Wählen Sie diese Option, wenn initial eine Ergebnisliste angezeigt werden soll (dafür werden die initialen Filter verwendet sofern gesetzt).');
$GLOBALS['TL_LANG']['tl_module']['sortingMode']							= array('Sortiermodus', 'Wählen Sie hier aus, ob Sie zur Sortierung ein Feld auswählen oder über eine Freitexteingabe der Form "<fieldname>_<asc|desc>" sortieren möchten.');
$GLOBALS['TL_LANG']['tl_module']['sortingMode']['field']				= 'Feld';
$GLOBALS['TL_LANG']['tl_module']['sortingMode']['text']					= 'Freitext';
$GLOBALS['TL_LANG']['tl_module']['itemSorting']							= array('Initiale Sortierung', 'Wählen Sie hier eine initiale Sortierung aus.');
$GLOBALS['TL_LANG']['tl_module']['itemSorting']['asc']					= ' (aufsteigend)';
$GLOBALS['TL_LANG']['tl_module']['itemSorting']['desc']					= ' (absteigend)';
$GLOBALS['TL_LANG']['tl_module']['itemSorting']['random']				= 'Zufällige Reihenfolge';
$GLOBALS['TL_LANG']['tl_module']['formHybridAddDefaultFilterValues']	= array('Initiale Filter hinzufügen', 'Wählen Sie diese Option, um initiale Filter für das Modul hinzuzufügen.');
$GLOBALS['TL_LANG']['tl_module']['formHybridDefaultFilterValues']		= array(' ', 'Definieren Sie hier initiale Filter für das Modul.');
$GLOBALS['TL_LANG']['tl_module']['formHybridFilterTemplate']			= array('Filterformular-Template', 'Hier können Sie das Formular-Template überschreiben.');
$GLOBALS['TL_LANG']['tl_module']['itemTemplate']						= array('Instanz-Template', 'Wählen Sie hier das Template aus, mit dem die einzelnen Instanzen gerendert werden sollen.');
$GLOBALS['TL_LANG']['tl_module']['customFilterFields']					= array('Felder zur Filterung', 'Wählen Sie hier die Felder, die im Filter erscheinen sollen.');
$GLOBALS['TL_LANG']['tl_module']['setPageTitle']						= array('Instanzfeld als Seitentitel setzen', 'Wählen Sie diese Option, wenn nach dem Anlegen einer Instanz ein Feld als Seitentitel gesetzt werden soll (bspw. der Titel).');
$GLOBALS['TL_LANG']['tl_module']['pageTitleField']						= array('Seitentitelfeld', 'Wählen Sie das Feld aus, dass dem Seitentitel zugewiesen werden soll.');
$GLOBALS['TL_LANG']['tl_module']['additionalWhereSql']					= array('Zusätzliches WHERE-SQL', 'Geben Sie hier SQL ein, welches dem WHERE-Statement hinzugefügt wird.');
$GLOBALS['TL_LANG']['tl_module']['additionalSelectSql']					= array('Zusätzliches SELECT-SQL', 'Geben Sie hier SQL ein, welches vor dem FROM-Statement eingefügt wird (bspw. , IF(field1 = \'\', field2, field1) as somename).');
$GLOBALS['TL_LANG']['tl_module']['additionalSql']						= array('Zusätzliches SQL', 'Geben Sie hier SQL ein, welches nach dem SELECT-Statement eingefügt wird (bspw. INNER JOIN tl_tag ON tl_calendar_events.id = tl_tag.tid).');
$GLOBALS['TL_LANG']['tl_module']['hideUnpublishedItems']				= array('Unveröffentlichte Instanzen verstecken', 'Wählen Sie diese Option, um unveröffentlichte Instanzen zu verstecken.');
$GLOBALS['TL_LANG']['tl_module']['publishedField']						= array('Veröffentlicht-Feld', 'Wählen Sie hier das Feld aus, in dem der Öffentlichkeitszustand gespeichert ist (z. B. published).');
$GLOBALS['TL_LANG']['tl_module']['invertPublishedField']				= array('Veröffentlicht-Feld negieren', 'Wählen Sie diese Option, wenn ein "true" im Veröffentlicht-Feld einem nichtöffentlichen Zustand entspricht.');
$GLOBALS['TL_LANG']['tl_module']['emptyText']							= array('Meldung bei leerer Ergebnismenge', 'Geben Sie hier die Meldung ein, die erscheinen soll, wenn keine Ergebnisse gefunden wurden (mit ##<Feldname>## können Filtereingaben eingefügt werden).');
$GLOBALS['TL_LANG']['tl_module']['addDetailsCol']						= array('Details-Spalte hinzufügen', 'Klicken Sie hier, um jeder Zeile einen Button zum Anzeigen von Details hinzuzufügen.');
$GLOBALS['TL_LANG']['tl_module']['jumpToDetails']						= array('Weiterleitungsseite (Details)', 'Wählen Sie hier die Seite aus, zu der weitergeleitet wird, wenn es eine Detailseite gibt.');
$GLOBALS['TL_LANG']['tl_module']['useDummyImage']						= array('Platzhalterbild nutzen', 'Wählen Sie diese Option, um immer dann ein Platzhalterbild zu nutzen, wenn es kein der Nachricht kein reguläres Bild zugewiesen wurde.');
$GLOBALS['TL_LANG']['tl_module']['dummyImage']							= array('Platzhalterbild', 'Wählen Sie hier das Platzhalterbild aus.');
$GLOBALS['TL_LANG']['tl_module']['isTableList']							= array('Als Tabelle ausgeben', 'Wählen Sie diese Option, die Liste in Form einer Tabelle ausgegeben werden soll.');
$GLOBALS['TL_LANG']['tl_module']['hasHeader']							= array('Kopfzeile ausgeben', 'Wählen Sie diese Option, wenn die Tabelle eine Kopfzeile haben soll.');
$GLOBALS['TL_LANG']['tl_module']['tableFields']							= array('Tabellenfelder', 'Wählen Sie die Felder aus, die in der Tabelle ausgegeben werden sollen.');


// events
$GLOBALS['TL_LANG']['tl_module']['filterArchives']						= array('Archive', 'Wählen Sie hier die Archive aus, deren Elemente in der Liste sichtbar sein sollen.');

// members
$GLOBALS['TL_LANG']['tl_module']['filterGroups']						= array('Mitgliedergruppen', 'Wählen Sie hier die Mitgliedergruppen aus, deren Mitglieder in der Liste sichtbar sein sollen.');

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_module']['entity_legend']						= 'Entität';
$GLOBALS['TL_LANG']['tl_module']['list_legend']							= 'Liste';
$GLOBALS['TL_LANG']['tl_module']['filter_legend']						= 'Filter & Sortierung';
$GLOBALS['TL_LANG']['tl_module']['misc_legend']							= 'Verschiedenes';
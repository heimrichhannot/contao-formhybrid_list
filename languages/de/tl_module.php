<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_module'];

/**
 * Fields
 */
$arrLang['hideFilter']							= array('Filter verstecken', 'Klicken Sie hier, um den Filter zu verstecken.');
$arrLang['filterHeadline']						= array('Filterüberschrift', 'Geben Sie hier eine Überschrift für den Filter ein.');
$arrLang['showItemCount']						= array('Ergebnisanzahl anzeigen', 'Klicken Sie hier, um die Anzahl der gefundenen Objekte anzuzeigen.');
$arrLang['showInitialResults']					= array('Initial Ergebnisse anzeigen', 'Wählen Sie diese Option, wenn initial eine Ergebnisliste angezeigt werden soll (dafür werden die initialen Filter verwendet sofern gesetzt).');
$arrLang['sortingMode']							= array('Sortiermodus', 'Wählen Sie hier aus, ob Sie zur Sortierung ein Feld auswählen oder über eine Freitexteingabe der Form "<fieldname>_<asc|desc>" sortieren möchten.');
$arrLang['sortingMode']['field']				= 'Feld';
$arrLang['sortingMode']['text']					= 'Freitext';
$arrLang['itemSorting']							= array('Initiale Sortierung', 'Wählen Sie hier eine initiale Sortierung aus.');
$arrLang['itemSorting']['asc']					= ' (aufsteigend)';
$arrLang['itemSorting']['desc']					= ' (absteigend)';
$arrLang['itemSorting']['random']				= 'Zufällige Reihenfolge';
$arrLang['formHybridAddDefaultFilterValues']	= array('Initiale Filter hinzufügen', 'Wählen Sie diese Option, um initiale Filter für das Modul hinzuzufügen.');
$arrLang['formHybridDefaultFilterValues']		= array(' ', 'Definieren Sie hier initiale Filter für das Modul.');
$arrLang['formHybridFilterTemplate']			= array('Filterformular-Template', 'Hier können Sie das Formular-Template überschreiben.');
$arrLang['itemTemplate']						= array('Instanz-Template', 'Wählen Sie hier das Template aus, mit dem die einzelnen Instanzen gerendert werden sollen.');
$arrLang['customFilterFields']					= array('Felder zur Filterung', 'Wählen Sie hier die Felder, die im Filter erscheinen sollen.');
$arrLang['setPageTitle']						= array('Instanzfeld als Seitentitel setzen', 'Wählen Sie diese Option, wenn nach dem Anlegen einer Instanz ein Feld als Seitentitel gesetzt werden soll (bspw. der Titel).');
$arrLang['pageTitleField']						= array('Seitentitelfeld', 'Wählen Sie das Feld aus, dass dem Seitentitel zugewiesen werden soll.');
$arrLang['pageTitlePattern']					= array('Seitentitelmuster', 'Geben Sie hier ein Muster für aus mehreren Feldern zusammengesetzte Seitentitel in der Form "%firstname% %lastname%" ein.');
$arrLang['addBackButton']						= array('"Zurück"-Button hinzufügen', 'Wählen Sie diese Option, um dem Modul einen "Zurück"-Button hinzuzufügen.');
$arrLang['additionalWhereSql']					= array('Zusätzliches WHERE-SQL', 'Geben Sie hier SQL ein, welches dem WHERE-Statement hinzugefügt wird.');
$arrLang['additionalSelectSql']					= array('Zusätzliches SELECT-SQL', 'Geben Sie hier SQL ein, welches vor dem FROM-Statement eingefügt wird (bspw. , IF(field1 = \'\', field2, field1) as somename).');
$arrLang['additionalSql']						= array('Zusätzliches SQL', 'Geben Sie hier SQL ein, welches nach dem SELECT-Statement eingefügt wird (bspw. INNER JOIN tl_tag ON tl_calendar_events.id = tl_tag.tid).');
$arrLang['hideUnpublishedItems']				= array('Unveröffentlichte Instanzen verstecken', 'Wählen Sie diese Option, um unveröffentlichte Instanzen zu verstecken.');
$arrLang['publishedField']						= array('Veröffentlicht-Feld', 'Wählen Sie hier das Feld aus, in dem der Öffentlichkeitszustand gespeichert ist (z. B. published).');
$arrLang['invertPublishedField']				= array('Veröffentlicht-Feld negieren', 'Wählen Sie diese Option, wenn ein "true" im Veröffentlicht-Feld einem nichtöffentlichen Zustand entspricht.');
$arrLang['emptyText']							= array('Meldung bei leerer Ergebnismenge', 'Geben Sie hier die Meldung ein, die erscheinen soll, wenn keine Ergebnisse gefunden wurden (mit ##<Feldname>## können Filtereingaben eingefügt werden).');
$arrLang['addDetailsCol']						= array('Details-Spalte hinzufügen', 'Klicken Sie hier, um jeder Zeile einen Button zum Anzeigen von Details hinzuzufügen.');
$arrLang['jumpToDetails']						= array('Weiterleitungsseite (Details)', 'Wählen Sie hier die Seite aus, zu der weitergeleitet wird, wenn es eine Detailseite gibt.');
$arrLang['useDummyImage']						= array('Platzhalterbild nutzen', 'Wählen Sie diese Option, um immer dann ein Platzhalterbild zu nutzen, wenn es kein der Nachricht kein reguläres Bild zugewiesen wurde.');
$arrLang['dummyImage']							= array('Platzhalterbild', 'Wählen Sie hier das Platzhalterbild aus.');
$arrLang['isTableList']							= array('Als Tabelle ausgeben', 'Wählen Sie diese Option, die Liste in Form einer Tabelle ausgegeben werden soll.');
$arrLang['hasHeader']							= array('Kopfzeile ausgeben', 'Wählen Sie diese Option, wenn die Tabelle eine Kopfzeile haben soll.');
$arrLang['tableFields']							= array('Tabellenfelder', 'Wählen Sie die Felder aus, die in der Tabelle ausgegeben werden sollen.');
$arrLang['conjunctiveMultipleFields']			= array('Konjunktiv auszuwertende Mehrfachfelder', 'Wählen Sie die Felder aus, die konjunktiv, also UND-verknüpft ausgewertet werden sollen.');
$arrLang['addDisjunctiveFieldGroups']			= array('Disjunktiv auszuwertende Feldergruppen hinzufügen', 'Wählen Sie diese Option, wenn Sie Felder zu disjunktiven, also ODER-verknüpften, Gruppen zusammenfassen möchten.');
$arrLang['disjunctiveFieldGroups']				= array('Disjunktiv auszuwertende Feldergruppen', 'Wählen Sie die Felder aus, die zusammen disjunktiv, also ODER-verknüpft, ausgewertet werden sollen.');
$arrLang['disjunctiveFieldGroups']['fields']	= 'Felder';
$arrLang['addShowConditions']  					= array('Bedingungen für das Anzeigen hinzufügen', 'Wählen Sie hier aus, ob es Bedingungen für das Anzeigen von Datensätzen geben soll.');
$arrLang['showConditions']  					= array(' ', 'Wählen Sie hier aus, unter welchen Bedingungen das Anzeigen von Datensätzen möglich sein soll.');
$arrLang['useModal']  							= array('Instanzen in Modalfenstern ausgeben', 'Wählen Sie diese Option, wenn die Instanzen in Modalfenstern ausgegeben werden sollen.');
$arrLang['modalWrapperTpl']  					= array('Modal-Template (Wrapper)', 'Wählen Sie hier das Template für den Modal-Wrapper aus.');
$arrLang['modalTpl']  							= array('Modal-Template', 'Wählen Sie hier das Template für das Modalfenster aus.');
$arrLang['modalClass']  						= array('Modal-CSS-Klasse', 'Geben Sie hier bei Bedarf CSS-Klassen für das Modalfenster ein (bspw. "fade").');
$arrLang['modalInnerClass']  					= array('Innere Modal-CSS-Klasse', 'Geben Sie hier bei Bedarf CSS-Klassen für das innere DIV im Modalfenster ein (bspw. "modal-lg").');

// events
$arrLang['filterArchives']						= array('Archive', 'Wählen Sie hier die Archive aus, deren Elemente in der Liste sichtbar sein sollen.');

// members
$arrLang['filterGroups']						= array('Mitgliedergruppen', 'Wählen Sie hier die Mitgliedergruppen aus, deren Mitglieder in der Liste sichtbar sein sollen.');

/**
 * Legends
 */
$arrLang['entity_legend']						= 'Entität';
$arrLang['list_legend']							= 'Liste';
$arrLang['filter_legend']						= 'Filter & Sortierung';
$arrLang['misc_legend']							= 'Verschiedenes';
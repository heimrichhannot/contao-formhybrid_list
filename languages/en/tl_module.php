<?php

$arrLang = &$GLOBALS['TL_LANG']['tl_module'];

/**
 * Fields
 */
$arrLang['hideFilter']                       = ['Filter verstecken', 'Klicken Sie hier, um den Filter zu verstecken.'];
$arrLang['filterHeadline']                   = ['Filterüberschrift', 'Geben Sie hier eine Überschrift für den Filter ein.'];
$arrLang['showItemCount']                    = ['Ergebnisanzahl anzeigen', 'Klicken Sie hier, um die Anzahl der gefundenen Objekte anzuzeigen.'];
$arrLang['showInitialResults']               = ['Initial Ergebnisse anzeigen', 'Wählen Sie diese Option, wenn initial eine Ergebnisliste angezeigt werden soll (dafür werden die initialen Filter verwendet sofern gesetzt).'];
$arrLang['addAjaxPagination']                = ['Ajax-Paginierung hinzufügen', 'Wählen Sie diese Option, wenn eine Ajax-Paginierung genutzt werden soll. Dafür muss ein Wert > 0 in "Elemente pro Seite" gesetzt sein. Die Seitenzahlen werden durch einen einzelnen "Weiter"-Button ersetzt.'];
$arrLang['addInfiniteScroll']                = ['Infinite Scroll hinzufügen', 'Wählen Sie diese Option, um die Ajax-Paginierung im UI-Muster "Infinite Scroll" umzusetzen.'];
$arrLang['sortingMode']                      = ['Sortiermodus', 'Wählen Sie hier aus, ob Sie zur Sortierung ein Feld auswählen oder über eine Freitexteingabe der Form "<fieldname>_<asc|desc>" sortieren möchten.'];
$arrLang['sortingMode']['field']             = 'Feld';
$arrLang['sortingMode']['text']              = 'Freitext';
$arrLang['itemSorting']                      = ['Initiale Sortierung', 'Wählen Sie hier eine initiale Sortierung aus.'];
$arrLang['itemSorting']['asc']               = ' (aufsteigend)';
$arrLang['itemSorting']['desc']              = ' (absteigend)';
$arrLang['itemSorting']['random']            = 'Zufällige Reihenfolge';
$arrLang['formHybridAddDefaultFilterValues'] = ['Initiale Filter hinzufügen', 'Wählen Sie diese Option, um initiale Filter für das Modul hinzuzufügen.'];
$arrLang['formHybridDefaultFilterValues']    = [' ', 'Definieren Sie hier initiale Filter für das Modul.'];
$arrLang['formHybridFilterTemplate']         = ['Filterformular-Template', 'Hier können Sie das Formular-Template überschreiben.'];
$arrLang['itemTemplate']                     = ['Instanz-Template', 'Wählen Sie hier das Template aus, mit dem die einzelnen Instanzen gerendert werden sollen.'];
$arrLang['customFilterFields']               = ['Felder zur Filterung', 'Wählen Sie hier die Felder, die im Filter erscheinen sollen.'];
$arrLang['setPageTitle']                     = ['Instanzfeld als Seitentitel setzen', 'Wählen Sie diese Option, wenn nach dem Anlegen einer Instanz ein Feld als Seitentitel gesetzt werden soll (bspw. der Titel).'];
$arrLang['pageTitleField']                   = ['Seitentitelfeld', 'Wählen Sie das Feld aus, dass dem Seitentitel zugewiesen werden soll.'];
$arrLang['pageTitlePattern']                 = ['Seitentitelmuster', 'Geben Sie hier ein Muster für aus mehreren Feldern zusammengesetzte Seitentitel in der Form "%firstname% %lastname%" ein.'];
$arrLang['additionalWhereSql']               = ['Zusätzliches WHERE-SQL', 'Geben Sie hier SQL ein, welches dem WHERE-Statement hinzugefügt wird.'];
$arrLang['additionalSelectSql']              = ['Zusätzliches SELECT-SQL', 'Geben Sie hier SQL ein, welches vor dem FROM-Statement eingefügt wird (bspw. , IF(field1 = \'\', field2, field1) as somename).'];
$arrLang['additionalSql']                    = ['Zusätzliches SQL', 'Geben Sie hier SQL ein, welches nach dem SELECT-Statement eingefügt wird (bspw. INNER JOIN tl_tag ON tl_calendar_events.id = tl_tag.tid).'];
$arrLang['additionalHavingSql']              = ['Zusätzliches HAVING-SQL', 'Geben Sie hier SQL ein, welches nach dem WHERE-Statement als HAVING-Statement eingefügt wird.'];
$arrLang['hideUnpublishedItems']             = ['Unveröffentlichte Instanzen verstecken', 'Wählen Sie diese Option, um unveröffentlichte Instanzen zu verstecken.'];
$arrLang['publishedField']                   = ['Veröffentlicht-Feld', 'Wählen Sie hier das Feld aus, in dem der Öffentlichkeitszustand gespeichert ist (z. B. published).'];
$arrLang['invertPublishedField']             = ['Veröffentlicht-Feld negieren', 'Wählen Sie diese Option, wenn ein "true" im Veröffentlicht-Feld einem nichtöffentlichen Zustand entspricht.'];
$arrLang['emptyText']                        = ['Meldung bei leerer Ergebnismenge', 'Geben Sie hier die Meldung ein, die erscheinen soll, wenn keine Ergebnisse gefunden wurden (mit ##<Feldname>## können Filtereingaben eingefügt werden).'];
$arrLang['addDetailsCol']                    = ['Details-Spalte hinzufügen', 'Klicken Sie hier, um jeder Zeile einen Button zum Anzeigen von Details hinzuzufügen.'];
$arrLang['jumpToDetails']                    = ['Weiterleitungsseite (Details)', 'Wählen Sie hier die Seite aus, zu der weitergeleitet wird, wenn es eine Detailseite gibt.'];
$arrLang['addShareCol']                      = ['Teilen-Spalte hinzufügen', 'Klicken Sie hier, um jeder Zeile einen Button zum Teilen des aktuellen Listeneintrags hinzuzufügen.'];
$arrLang['jumpToShare']                      = ['Weiterleitungsseite (Teilen)', 'Wählen Sie hier die Seite aus, zu der weitergeleitet wird, wenn ein Inhalt geteilt wurde.'];
$arrLang['shareAutoItem']                    = ['auto_item für den Teilen-Link verwenden', 'Wählen Sie diese Option aus, um das Share Token als auto_item auszugeben.'];
$arrLang['addTokenToShareUrl']               = ['Request-Token für den Teilen-Link verwenden', 'Wählen Sie diese Option aus, um dem Teilen-Link ein Request Token hinzuzufügen.'];
$arrLang['useDummyImage']                    = ['Platzhalterbild nutzen', 'Wählen Sie diese Option, um immer dann ein Platzhalterbild zu nutzen, wenn es kein der Nachricht kein reguläres Bild zugewiesen wurde.'];
$arrLang['dummyImage']                       = ['Platzhalterbild', 'Wählen Sie hier das Platzhalterbild aus.'];
$arrLang['isTableList']                      = ['Als Tabelle ausgeben', 'Wählen Sie diese Option, die Liste in Form einer Tabelle ausgegeben werden soll.'];
$arrLang['hasHeader']                        = ['Kopfzeile ausgeben', 'Wählen Sie diese Option, wenn die Tabelle eine Kopfzeile haben soll.'];
$arrLang['sortingHeader']                    = ['Sortierende Kopfzeile', 'Wählen Sie diese Option, wenn die Tabelle eine Kopfzeile haben soll, die Links zum Sortieren enthält.'];
$arrLang['tableFields']                      = ['Tabellenfelder', 'Wählen Sie die Felder aus, die in der Tabelle ausgegeben werden sollen.'];
$arrLang['conjunctiveMultipleFields']        = ['Konjunktiv auszuwertende Mehrfachfelder', 'Wählen Sie die Felder aus, die konjunktiv, also UND-verknüpft ausgewertet werden sollen.'];
$arrLang['addDisjunctiveFieldGroups']        = ['Disjunktiv auszuwertende Feldergruppen hinzufügen', 'Wählen Sie diese Option, wenn Sie Felder zu disjunktiven, also ODER-verknüpften, Gruppen zusammenfassen möchten.'];
$arrLang['disjunctiveFieldGroups']           = ['Disjunktiv auszuwertende Feldergruppen', 'Wählen Sie die Felder aus, die zusammen disjunktiv, also ODER-verknüpft, ausgewertet werden sollen.'];
$arrLang['disjunctiveFieldGroups']['fields'] = 'Felder';
$arrLang['addShowConditions']                = ['Bedingungen für das Anzeigen hinzufügen', 'Wählen Sie hier aus, ob es Bedingungen für das Anzeigen von Datensätzen geben soll.'];
$arrLang['showConditions']                   = [' ', 'Wählen Sie hier aus, unter welchen Bedingungen das Anzeigen von Datensätzen möglich sein soll.'];
$arrLang['addExistanceConditions']           = ['Instanz über eine bestimmte Bedingung finden', 'Wählen Sie diese Option, wenn die Instanz nicht durch das auto_item gefunden werden soll, sondern durch eine bestimmte Bedingung der Datenbankabfrage.'];
$arrLang['existanceConditions']              = ['Bedingungen für das Auffinden bestehender Instanzen', 'Geben Sie hier Bedingungen ein, die für das Auffinden bestehender Instanzen gelten müssen.'];
$arrLang['aliasField']                       = ['Alias-Feld', 'Wählen Sie hier das Alias-Feld aus, welches als auto_item abgefragt wird. Wenn Sie kein Feld auswählen, wird das Feld "id" verwendet.'];
$arrLang['deactivateTokens']                 = ['Token-Handling deaktivieren', 'Wählen Sie diese Option, wenn die Module nicht prüfen sollen, ob der GET-Parameter "token" ein korrektes Token enthält.'];
$arrLang['limitFormattedFields'][0]          = 'Formatierte Felder einschränken (Geschwindigkeit verbessern)';
$arrLang['limitFormattedFields'][1]          = 'Wählen Sie diese Option, wenn nur bestimmte Felder auf Basis der Data-Containers-Konfiguration formatiert werden sollen möchten.';
$arrLang['formattedFields'][0]               = 'Formatierte Felder';
$arrLang['formattedFields'][1]               = 'Wählen Sie hier die zu formatierenden Felder aus.';


// events
$arrLang['filterArchives'] = ['Archive', 'Wählen Sie hier die Archive aus, deren Elemente in der Liste sichtbar sein sollen.'];

// members
$arrLang['filterGroups'] = ['Mitgliedergruppen', 'Wählen Sie hier die Mitgliedergruppen aus, deren Mitglieder in der Liste sichtbar sein sollen.'];

/**
 * Legends
 */
$arrLang['entity_legend'] = 'Entität';
$arrLang['list_legend']   = 'Liste';
$arrLang['filter_legend'] = 'Filter & Sortierung';
$arrLang['misc_legend']   = 'Verschiedenes';
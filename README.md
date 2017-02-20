# Formhybrid List

Contains a list and a reader module in a generic flavor. The modules can display and process all kinds of contao entities containing filtering, pagination, modal handling, ...

If additional functionality is needed, one simply has to inherit from ModuleList or ModuleReader.

-> Click [here](docs/formhybrid.png) for a diagram visualizing the interaction between the modules [formhybrid](https://github.com/heimrichhannot/contao-formhybrid), [formhybrid_list](https://github.com/heimrichhannot/contao-formhybrid_list), [frontendedit](https://github.com/heimrichhannot/contao-frontendedit) and [submissions](https://github.com/heimrichhannot/contao-submissions).

## Features

### List module

- display any contao entity
- sorting by field or free text
- pagination (ajax or synchronous)
- infinite scroll
- advanced filtering using heimrichhannot/contao-formhybrid
- defining of default filters
- detail links with alias support
- opening instances in modal windows
- display as table with sortable headers
- share entities in a list (add the necessary fields to your dca beforehands by using ```FormHybridList::addShareFields()```)

### Reader module

- display any contao entity
- support for id or alias
- security handling

### Modules

Name | Description
---- | -----------
ModuleList | A generic list module able to display all kinds of contao entities containing filtering, pagination, ...
ModuleMemberList | Encapsulates member specific changes overriding ModuleList
ModuleNewsList | Encapsulates news specific changes overriding ModuleList
ModuleReader | A generic reader module able to display all kinds of contao entities
ModuleMemberReader | Encapsulates news specific changes overriding ModuleList

### Fields

tl_module:

Name | Description
---- | -----------
addDetailsCol | Determines whether a list item should contain a details column
jumpToDetails | The jump to page opened when clicking a details link
itemSorting | Determines the sorting of the items
hideFilter | Determines whether the filter is hidden
showItemCount | Determines whether the item count is shown
addCustomFilterFields | Determines whether custom filter fields should be used
customFilterFields | The custom filter fields
filterArchives | The archives in which an item must be in order to be displayed (pid)
filterGroups | The groups in which a member must be in order to be displayed (groups)
pageTitleField | Specifies the page's title field
additionalWhereSql | Here additional sql added in the WHERE part of the SQL query can be specified
additionalSelectSql | Here additional sql added before the FROM part of the SQL query can be specified
additionalSql | Here additional sql added after the FROM part of the SQL query can be specified
hideUnpublishedItems | Determines whether unpublished items should be hidden
publishedField | Determines the field responsible for the publish state
invertPublishedField | Determines whether publishedField should be taken into account inverted
emptyText | The text shown when no items match the query
itemTemplate | The template each item is rendered with

### Hooks

Name | Arguments | Description
---- | --------- | -----------
parseItems | $objTemplate, $arrItem, $objModule | Triggered just before FrontendTemplate::parse() is called

## Technical instructions

### Example CSS for the masonry

```
.formhybrid-list {
    > div {
        width: 100%;
    }

    .items {
        width: 100%;
        .make-row();

        .item, .stamp-item {
            .make-xs-column(12);
            .make-sm-column(6);
            .make-md-column(4);
            .make-lg-column(4);
            margin-bottom: 40px;
        }
    }
}
```

### Customize list filter processing behavior

Just create a submodule of ModuleList, add the following function and adjust it to your needs

```
protected function customizeFilters(&$strField, &$strColumn, &$varValue, &$blnSkipValue = false)
{
    $arrData = $this->objFilterForm->getDca();

    switch ($strField)
    {
        case 'startDate':
            $strColumn = 'startDate >= ?';
            $varValue = strtotime(str_replace('%', '', $varValue));
            break;
        case 'endDate':
            $strColumn = 'startDate <= ?';
            $varValue = strtotime(str_replace('%', '', $varValue));
            break;
        case 'city':
            $strColumn = 'city = ?';
            $varValue = $arrData['fields'][$strField]['options'][$varValue];
            break;
        case 'fsonly':
            if (\Input::get('fsonly') === '0')
            {
                $strColumn = 'pid != 1';
            }
            else if (\Input::get('fsonly') === '1')
            {
                $strColumn = 'pid = 1';
            }
            break;
    }
}
```
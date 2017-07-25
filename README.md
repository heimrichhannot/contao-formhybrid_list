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
- opening instances in modal windows (install heimrichhannot/contao-modal for this)
- display as table with sortable headers
- share entities in a list (add the necessary fields to your dca beforehands by using ```FormHybridList::addShareFields()```)

### Reader module

- display any contao entity
- support for id or alias
- security handling

## Technical instructions

###

### Example CSS for the masonry

```
.formhybrid-list {
    .items {
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

Just create a submodule of ModuleList, add the following function and adjust it to your needs (same is possible for customizeDefaultFilters() which are the initial filters).

Hint: You can also return multiple values for one field as array.

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

### Modules

Name | Description
---- | -----------
ModuleList | A generic list module able to display all kinds of contao entities containing filtering, pagination, ...
ModuleMemberList | Encapsulates member specific changes overriding ModuleList
ModuleNewsList | Encapsulates news specific changes overriding ModuleList
ModuleReader | A generic reader module able to display all kinds of contao entities
ModuleMemberReader | Encapsulates news specific changes overriding ModuleList

### Hooks

Name | Arguments | Description
---- | --------- | -----------
parseItems | $objTemplate, $arrItem, $objModule | Triggered just before FrontendTemplate::parse() is called

### Insert Tags

Name | Arguments | Example
---- | --------- | -------
fhl_filter_url | page id – the page that contains the list module,<br>module id – the formhybrid_list list module -> ModuleList or inheriting class,<br>filter query – url encoded | {{fhl_filter_url::1::5::city=Dresden&company=Heimrich+%26+Hannot}}
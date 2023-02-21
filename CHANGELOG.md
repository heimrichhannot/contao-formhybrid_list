# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased] - 2023-02-21
- Changed: replaced all frontend module global constants with class constants
- Changed: requires contao >= 4.4
- Deprecated: global module constants
- Fixed: some namespaces

## [5.10.1] - 2022-09-08
- Fixed: load dca syntax

## [5.10.0] - 2022-08-23
- Changed: make modal dependency optional
- Changed: minimum supported php version is now 7.1

## [5.9.0] - 2022-07-21
- Changed: introduced `ModuleReader::TYPE` to replace MODULE_FORMHYBRID_READER
- Deprecated: MODULE_FORMHYBRID_READER constant

## [5.8.1] - 2022-02-14

- Fixed: array index issues in php 8+

## [5.8.0] - 2022-01-20
- Added: SessionField class to replace HastePlus session methods
- Changed: minimum php version is now 5.6

## [5.7.0] - 2021-09-01

- Added: php8 support

## [5.6.0] - 2020-11-03
- removed robloach/component-installer from dependencies

## [5.5.2] - 2019-05-17

#### Fixed
- masonry error on some lists in combination with ajax pagination

## [5.5.1] - 2019-04-01

#### Fixed
- restored `tl_module.formHybridLinkedList` in filter module


## [5.5.0] - 2019-04-01

#### Added
- `tl_module.limitFormattedFields` added to improve list and reader performance by minimizing `FormSubmission::prepareSpecialValueForPrint` calls

## [5.4.1] - 2018-12-13

#### Fixed
- error due using non existing method in backend Module class

## [5.4.0] - 2018-11-28

#### Added
- TL_COMPONENT support in order to disable js, css assets on demand

## [5.3.7] - 2018-11-26

### Fixed
- Search in blob fields must ignore case-sensitivity (#1)

## [5.3.6] - 2018-11-20

### Fixed
- coordinates issue introduced in 5.3.5

### Added
- fallback if no coordinates are found in proximity search

## [5.3.5] - 2018-11-07

### Fixed
- coordinates filter error due string changes in php7

## [5.3.4] - 2018-07-16

### Fixed
- missing default value for linkedList and linkedFilter

## [5.3.3] - 2018-03-09

### Fixed
- missed to check in `ModuleList` on last tag

## [5.3.2] - 2018-03-09

### Added
- optional fuzzy search in `ModuleList`

### Fixed
- `$blnSkipColumn` and `$blnSkipValue` fields `city` and `postal` in `ModuleList::applyDefaultValues()` only when `FormHybridList::PROXIMITY_SEARCH_RADIUS` is in `formHybriddefaultValues` 

## [5.3.1] - 2018-03-06

### Fixed
- restored minified version of js

## [5.3.0] - 2018-03-06

### Added
- optional freetext search field
- save the filter parameter to session in `ListFilterForm::onSubmitCallback()` and set them as defaultValues when building the form in `ListFilterForm::__construct()`
- freetext and proximity search in `ModuleList::applyDefaultValues()`

## [5.2.1] - 2018-03-05

### Changed

added minified version of js

## [5.2.0] - 2018-02-09

#### Changed
- "outsourced" jscroll

## [5.1.0] - 2018-02-06

#### Changed
- changed masonry and imagesloaded library dependencies to our new repositories

## [5.0.0] - 2018-02-06

#### Changed
- `heimrichhannot/contao-formhybrid` 3.x dependency
- licence `LGPL-3.0+`to `LGPL-3.0-or-later`

## [4.1.3] - 2018-01-24

### Fixed
- tl_module changed *JumpTo load to lazy

## [4.1.2] - 2017-12-08

### Fixed
- shareExpirationInterval is not mandatory anymore

## [4.1.1] - 2017-12-08

### Fixed
- generation of special dca field values (id was missing for data container); switched to DC_HastePlus for this generation

## [4.1.0] - 2017-12-05

### Added
- entity id filter

## [4.0.2] - 2017-12-01

### Fixed
- pid filter not respected in some situations

## [4.0.1] - 2017-12-01

### Fixed
- issues for filter module

### Added
- support for start and stop field (published sub palette)

## [4.0.0] - 2017-11-21

### Added
- sessionID/author check to list module

### Fixed
- sortingHeader
- filter palette issues
- replace echo by <?=

## [3.4.6] - 2017-11-14

### Fixed
- wrong array key at `runBeforeTemplateParsing` in ModuleNewsList

## [3.4.5] - 2017-11-06

### Fixed
- text sorting

## [3.4.4] - 2017-11-06

### Fixed
- module callbacks

## [3.4.3] - 2017-11-06

### Fixed
- translations

## [3.4.2] - 2017-11-06

### Added
- support for inherited list filters

### Changed
- name of ModuleFilter to ModuleListFilter for future changes

## [3.4.1] - 2017-11-03

### Changed
- added table to all fields while filtering in order to avoid ambiguous field names in SQL query
- palettes: split filtering and sorting for list module

## [3.4.0] - 2017-11-03

### Changed
- added table to all fields while filtering in order to avoid ambiguous field names in SQL query
- palettes: split filtering and sorting for list module

## [3.3.3] - 2017-11-02

### Fixed
- replaceInsertTags to non caching

## [3.3.2] - 2017-10-30

### Fixed
- DcaExtractor instantiation for Contao 4

## [3.3.1] - 2017-10-30

### Fixed
- field for linked list in filter

## [3.3.0] - 2017-10-30

### Added
- detached filter
- dropdown sorting for lists

## [3.2.1] - 2017-09-08

### Fixed
- order handling

## [3.2.1] - 2017-09-08

### Fixed
- order handling

## [3.2.0] - 2017-08-18

### Removed
- addImage -> refactor a better in future

## [3.1.0] - 2017-08-17

### Added
- property passing to item template offering access by $this->fieldName

## [3.0.4] - 2017-08-09

### Added
- state for proximity search

## [3.0.3] - 2017-07-25

### Fixed
- modal notes, autoload.ini

## [3.0.2] - 2017-07-25

### Fixed
- fixed classes in templates

## [3.0.1] - 2017-07-25

### Fixed
- fixed deps

## [3.0.0] - 2017-07-25

### Changed
- outsourced modal handling to heimrichhannot/contao-modal

## [2.5.4] - 2017-06-27

### Fixed
- fixed deps

## [2.5.3] - 2017-06-26

### Fixed
- fixed deps

## [2.5.2] - 2017-06-21

### Fixed
- fixed masonry dep

## [2.5.1] - 2017-06-21

### Fixed
- fixed masonry dep

## [2.5.0] - 2017-06-20

### Fixed
- fixed deps for contao 4

## [2.4.6] - 2017-05-09

### Fixed
- php 7 support
- field lengths

## [2.4.5] - 2017-04-12
- created new tag

## [2.4.4] - 2017-04-06

### Changed
- added php7 support. fixed contao-core dependency

## [2.4.3] - 2017-04-06

### Fixed
- geo location bug

## [2.4.2] - 2017-04-06

### Added
- more description in formhybrid_reader_default

### Fixed
- sql issues

## [2.4.1] - 2017-04-05

### Fixed
- Codestyle
- ajax pagination naming

### Added
- more description in formhybrid_reader_default

## [2.4.0] - 2017-04-05

### Added
- proximity search

### Changed
- template -> wrapper data attributes are now calculated in the module

## [2.3.32] - 2017-03-24

### Added
- appendIdToUrlOnFound

## [2.3.31] - 2017-03-15

### Fixed
- field dependend redirect

## [2.3.30] - 2017-03-10

### Fixed
- ajax pagination and fiter bugs

## [2.3.29] - 2017-02-24

### Fixed
- optimized loading div

### Added
- option for deactivating news comment notification

## [2.3.28] - 2017-02-22

### Added
- frontendedit support for masonry

## [2.3.27] - 2017-02-21

### Fixed
- masonry bug

## [2.3.26] - 2017-02-21

### Fixed
- masonry class name bug

## [2.3.25] - 2017-02-21

### Fixed
- imagesLoaded at initMasonry

## [2.3.24] - 2017-02-20

### Fixed

- removed masonryCols since this is done by using css

### Added
- additional sql now supports insert tags
- chapter for customizing filters in readme

## [2.3.23] - 2017-02-17

### Fixed
- masonry stamps

## [2.3.22] - 2017-02-16

### Added
- support for masonry

## [2.3.21] - 2017-02-03

### Fixed
- changed array() to []
- fixed Where calculation

## [2.3.20] - 2016-12-20

### Added
- CHANGELOG.md

### Fixed
- call findPublishedByMid instead of findBy to get only published MemberContentArchiveModels

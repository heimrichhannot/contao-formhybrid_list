# Changelog
All notable changes to this project will be documented in this file.

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

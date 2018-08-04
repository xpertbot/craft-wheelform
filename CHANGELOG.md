# Wheel Form Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 1.10.2 - 2018-08-04
### Fixed
- Fixed new column count on multiple forms giving wrong value.

## 1.10.1 - 2018-08-04
### Added
- Added new column to form index to display which form has new messages.

### Fixed
- Fixed translations needed for new items.

## 1.10.0 - 2018-08-01
### Added
- Added Capability to turn off database saving.
- Added Dutch Translation (@timbertens)

## 1.9.0 - 2018-07-10
### Added
- Added Custom HTML Email template.

## 1.8.0 - 2018-06-24
### Added
- Added CSV Exporter under Admin > Utilities > Form Export.

## 1.7.5 - 2018-06-24
### Added
- Added Italian translations.

## 1.7.4 - 2018-06-18
### Added
- Added German translations.

### Fixed
- Fixed reCaptcha submission value on PHP 7.2.

## 1.7.3 - 2018-06-09
### Fixed
- Fixed wrong fields being emailed on form submittion introduced on previous patch.

## 1.7.2 - 2018-06-05
### Notes
- Forms need to be re-saved in order for new ordering to take effect.

### Fixed
- Fixed bug of inactive fields being displayed on form builder.

## 1.7.1 - 2018-06-04
### Added
- Added minified version of application.

## 1.7.0 - 2018-06-02
### Added
- Added order of field in database. Forms have to be resaved in order to save field ordering to take effect.
- Added better view of fields

## 1.6.9 - 2018-05-10
### Fixed
- Fixed error overwritting secret key in settings

## 1.6.7 - 2018-05-10
### Fixed
- Fixed optional checkboxes not displaying correctly.
- Fixed Control Panel as the title of the Forms Index Page.

## 1.6.6 - 2018-05-06
### Fixed
- Fixed message list view columns when a value is empty.
- Fixed performance for values inside the column list.

## 1.6.5 - 2018-05-06
### Fixed
- Fixed bug not finding correct Message Entry

## 1.6.4 - 2018-05-06
### Fixed
- Fixed migration column type

## 1.6.3 - 2018-05-06
### Added
- Added Ability to mark messages as unread
- Added ability to delete message entries

### Fixed
- Fixed namespace to be all lowercase to better use Yii module routes.

## 1.6.2 - 2018-05-03
### Fixed
- Fixed PostgreSQL form Field id on new forms error
- Fixed bug on admin not allowing checkbox to update correctly.

## 1.6.1 - 2018-05-02
### Fixed
- Fixed PostgreSQL installation column type errors

## 1.6.0 - 2018-04-30
### Added
- Added ability to mark messages are unread.
- Added new badge count of unread messages.

### Fixed
- Fixed some Date issues for entries view
- Fixed class names inside Models

## 1.5.1 - 2018-04-30
### Fixed
- Fixed Mailer message error

## 1.5.0 - 2018-04-25
### Added
- Added Spanish translations

## 1.4.4 - 2018-04-07
### Fixed
- Fixed install error introduced last patch

## 1.4.3 - 2018-04-07
### Fixed
- Fixed migration changes to Install.php for future installs

## 1.4.2 - 2018-04-07
### Improved
- Improved handling of ids in fields introcued on previous patch

### Added
- Added new active column to fields so we don't delete data related to those fields.
- Added Delete warning on Form Field.

## 1.4.1 - 2018-04-07
### Fixed
- Fixed critical bug that delete fields entries when updating form fields

## 1.4.0 - 2018-04-07
### Added
- Added ability to check fields on entries views

## 1.3.0 - 2018-04-06
### Added
- Added more field types, they all validate to strings

## 1.2.4 - 2018-04-04
### Improvements
- Improved icon hover instead of solid color for form fields

## 1.2.3 - 2018-04-04
### Fixed
- Fixed icon

## 1.2.2 - 2018-04-04
### Improvement
- Improved icon with custom one

## 1.2.1 - 2018-04-03
### Fixed
- Fixed checkbox change event on new row added

## 1.2.0 - 2018-03-29
### Improvement
- Improvement on entries list

## 1.1.1 - 2018-03-29
### Fixed
- Fixed instructions for To Emails

## 1.1.0 - 2018-03-29
### Added
- Added ability to send to multiple emails

## 1.0.4 - 2018-03-29
### Fixed
- Fixed bug handling checkboxes on emails

## 1.0.3 - 2018-03-29
### Fixed
- Fixed Javascript Ajax response on errors

## 1.0.2 - 2018-03-28
### Bugs
- Fixed Recaptcha values on form

## 1.0.1 - 2018-03-28
### Added
- Recaptcha Ability to individual forms

## 0.1 - 2018-02-07
### Added
- Initial release

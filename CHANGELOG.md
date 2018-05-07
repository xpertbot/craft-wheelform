# Wheel Form Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

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

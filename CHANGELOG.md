# Wheel Form Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/) and this project adheres to [Semantic Versioning](http://semver.org/).

## 2.2.3 - 2019-01-08
### Fixed
- Fixed bug where files were being uploaded after form errors.

## 2.2.2 - 2019-25-07
### Fixed
- Ability to delete HTML fields

## 2.2.1 - 2019-15-07
### Fixed
- Bug related to deleted form not displaying correctly on form fields.

## 2.2.0 - 2019-13-07
### Added
- Added ability to delete forms.
### Fixed
- Fixed Pagination on entries list
- Fixed Items not deleting correctly in Multi options fields.

## 2.1.1 - 2019-07-06
### Added
- Added ability to restrict type of files uploaded.

## 2.1.0 - 2019-06-19
### Added
- Added ability to use Aliases inside Recaptcha fields.
- Added `name` variable for individual forms.
### Fixed
- Fixed typo in README
- Fixed error on new forms with no fields present

## 2.0.2 - 2019-05-05
### Fixed
- Fixed placeholder for Email and Textarea

## 2.0.1 - 2019-05-03
### Fixed
- Fixed Form Attributes not displaying correctly

## 2.0.0 - 2019-04-29
### Improved
- Improved Form Field configuration page.
- Improved Field Configuration options
- Faster Form Submission

### Added
- Added new HTMl Field
- Added French Translation (@timbertens)

## 1.25.3 - 2019-04-07
### Fixed
- Fixed Bug on Form Service

## 1.25.2 - 2019-04-04
### Fixed
- Fixed Bug related to Cron Job Command to Purge Messages (@luke-nehemedia).

## 1.25.1 - 2019-04-01
### Fixed
- Fixed Bug related to Deprecation error on form Attributes.

## 1.25.0 - 2019-03-30
### Added
- Added ability to create Cron Jobs to delete Messages after a determined amount of days.
- Added new form configuration to allow assets to be registered before getting cached.
### Fixed
- Fixed Bug related to Form Field trying to render itself when already instantiated.

## 1.24.1 - 2019-03-13
### Improved
- Improved the way the Twig Services render the form, Now using Yii2 Html helpers to display the forms and the CSRF fields

## 1.24.0 - 2019-03-13
### Added
- Custom Subject to Admin Email.
- Reply-to Field lightswitch to Forms.
- Portuguese translations.
### Improved
- Improved Translations setup.
- Improved Spanish Translations.

## 1.23.0 - 2019-02-25
### Improved
- Improved UI for Form Settings so Advanced Options are collapsed by default.
### Added
- Added Form Field Type, Where users can select a form and display on the templates.

## 1.22.0 - 2019-02-18
### Added
- Added Notification to form submissions.
### Fixed
- Fixed Bug that would duplicate forms after initial save

## 1.21.1 - 2019-02-14
### Fixed
- Fixed error being thrown on group permissions for bad namespace.

## 1.21.0 - 2019-02-12
### Added
- Added Permissions for different aspects of the forms.

## 1.20.4 - 2019-02-11
### Fixed
- RecpatchaV2 not working on some server setups.
## Improved
- Improved Attributes passed on the template to overwritte form default attribute values.

## 1.20.3 - 2019-02-08
### Fixed
- Multiple field values not updating correctly.

## 1.20.2 - 2019-02-08
### Fixed
- Order not saving correctly introduced on Last Patch.

## 1.20.1 - 2019-02-08
### Fixed
- Critical update to fix new options not loading correctly on Edit Form View.

## 1.20.0 - 2019-01-26
### Added
- Added New Actions to Entries view.
###Improved
- Improved Form Settings Layout, 2 column layout for form field configuration

## 1.19.0 - 2019-01-19
### Added
- Added Placeholders to field options.
- Added email_html to beforeSend Event that allows overwrite of the email template completely.
- Added ability to add specific tempaltes based on form ID.
- Added ability to Labels to be translatable.

## 1.18.0 - 2019-01-13
### Added
- Added RecaptchaV3 Integration.
- Added submitButton Template Variable for more customization options.
- Added BeforeSave Event and AfterSend Events for more options to manipulate data at various stages

## 1.17.1 - 2018-12-12
### Fixed
- Fixed problem with flash messages being generic. Notice: Old flash message are unaffected but will be deprecated eventually.

## 1.17.0 - 2018-12-09
### Added
- Added new List field to handle multiple values.

## 1.16.4 - 2018-11-18
### Fixed
- Fixed better volume retrieval when uploading files.

## 1.16.3 - 2018-11-16
### Fixed
- Fixed error when trying to create multiple forms on a single template.

## 1.16.2 - 2018-11-14
### Fixed
- Fixed error on undefined variable.

## 1.16.1 - 2018-11-14
### Fixed
- Fixed better handling of Asset management and reporting.
- Fixed better handling of Timezone handling for CSV exporter.

## 1.16.0 - 2018-11-10
### Added
- Added improved Event listener to modify From Email, To Email, ReplyTo Email, add extra fields to message.

## 1.15.1 - 2018-11-09
### Fixed
- Fixed CSV exporter not displaying all the headers.

## 1.15.0 - 2018-10-29
### Added
- Added New template function to display last submission. (@svale)
- Added New template function to display all entries of an specific form. (@svale)

## 1.14.3 - 2018-10-22
### Fixed
- Fixed Timezone on Message View

## 1.14.2 - 2018-10-16
### Fixed
- Fixed Dutch Translations (@cbovers)

## 1.14.1 - 2018-10-04
### Fixed
- Fixed missing Select on variable tempaltes
- Fixed Documentation examples to be more accurate.

## 1.14.0 - 2018-10-03
### Added
- Added wheelform variable for templates
- Added ability to add classes to fields and their containers for easy template render

## 1.13.1 - 2018-09-16
### Fixed
- Fixed File field being empty and not saving correctly

## 1.13.0 - 2018-09-16
### Added
- Added new ability to save Uploaded files to specific Asset folder.
- Added new ability to Import / Export Files from one form to another.

## 1.12.0 - 2018-08-14
### Added
- Added new Honeypot Field
- Added new ability to add Custom Label to Fields.
- Added Bulgarian translations (@ipetrov87)

## 1.11.0 - 2018-08-04
### Added
- Added ability to add options to checkboxes, radios and selects, for validation purposes

### Improved
- Improved has_new function for form column.

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

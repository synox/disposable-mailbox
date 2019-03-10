# Change Log
All notable changes to this project will be documented in this file. The format is based on [Keep a Changelog](http://keepachangelog.com/).


## [Unreleased]

### Changed
- find config.php automatically in current and parent directories. Show error message if not found. 
- use local CSS/JS instead of CDN
- detect missing imap extension and config error
- refactoring to simplify routing

### Removed
- JSON API (json-api.php), this feature would better fit in a separate project. 

## [0.2.1] - 2018-07-01

### Breaking Changes
- added $config['locale'].  See config.sample.php - you have to set it.

### Changed
- new layout & design with more whitespace and more explanations.  
- Show dates in local and relative format. 

## [0.2.0] - 2018-06-16

### Changed
- Show list of mails and show them only on click. 
- Removed Turbolinks to allow for simpler code in new features. Add new mail alert. 
- Rewrote to use mostly pure php. Uses Javascript only where it’s necessary. 
- fixed problem where only one domain is defined
- fix: restore focus on reload
- #33 improve button style
- fixed bug where html in plaintext emails are interpreted as html. 
- changed footer style
- refactored code into multiple php files.
- Requires PHP version  >=7.2
- make all addresses lowercase  #30
- fixed error when downloading email 

### Added 
- better horizontal spacing for header (from @Spegeli) and style
- improved validation of user input
- Added $config['prefer_plaintext'] = true; Prefer HTML or Text and removed toggle buttons.
- Added multiple domain support #21
- Blacklist some usernames, configurable  #27
- copyToClipboard button #30
- mail counter in title
- rest api option

## [0.1.4] - 2017-04-15

### Changed
- Improved styling using card layout
- Changed license to GPL-3.0 in order to allow commercial use and advertisement.

## [0.1.3] - 2017-03-24
### Changed
- new nicer login form
- layout optimized (show html now on the right)
- tell user that mails will be deleted

### Added
- set delete period in config

## [<=0.1.2]
See https://github.com/synox/disposable-mailbox/releases

# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/).

## [Unreleased]

### Changed
- Rewrote to use mostly pure php. Uses Javascript only where it’s necessary. 
- make blocked_usernames configurable
- improved validation of user input
- fixed problem where only one domain is defined
- horizontal spacing for header (from @Spegeli) and style
- fix: restore focus on reload
- Added $config['prefer_plaintext'] = true; Prefer HTML or Text and removed toggle buttons. 
- #33 improve button style
- fixed bug where html in plaintext emails are interpreted as html. 

### Added 
- Added multiple domain support (https://github.com/synox/disposable-mailbox/issues/21)
- Blacklist some usernames (https://github.com/synox/disposable-mailbox/issues/27)
- copyToClipboard button (https://github.com/synox/disposable-mailbox/issues/30)
- make all addresses lowercase  (https://github.com/synox/disposable-mailbox/issues/30)
- mail counter in title

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

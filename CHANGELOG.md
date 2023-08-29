# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

## 0.3.0 - 2023-08-29

## Added
- Add option to save users.
- Add option to save assignees for a merge request.
- Add option to save assignees for an issue.

## Changed
- Make the HookStoredListener queueable and add the config options for the prefered queue. 

## 0.2.0 - 2023-03-04

### Fixed
- Fixed the clearing of data for a build while handling a pipeline event.

## 0.1.1 - 2023-01-26

### Added
- Add Laravel 10 support.

### Fixed
- Fixed the deployment_id from the event data.

## 0.1.0 - 2023-01-23
- Initial release

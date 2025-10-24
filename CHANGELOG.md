# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased


## 0.8.0 - 2025-10-24
### Added
- Add Laravel 12 support.

### Fixed
- Make the deployable_url nullable on the deployments table because it can be null


## 0.7.0 - 2025-08-11

### Added
- Add indexes to the fields in the tables that are used in the repository to find the records if they exist.


## 0.6.0 - 2024-11-21

### Added
- Add PHP 8.4 support.


## 0.5.0 - 2024-03-13

### Added
- Add Laravel 11 support.


## 0.4.1 - 2024-02-12

### Fixed
- Revert the final on the models and update the ECS config to support it.


## 0.4.0 - 2024-02-09

### Added
- Add PHP 8.3 support.

### Fixed
- Fix using the queue for the GitLabHookStored event.


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

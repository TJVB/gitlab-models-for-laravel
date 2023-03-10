# Contributing
Contributions are always very welcome. This file is a guideline about how to contribute.

## How to contribute
All the contributions need to be done with a merge request. It is possible to create a merge request prefixed with DRAFT: to ask for feedback or if you didn't know how to match all requirements.  
Please be sure to check all the [requirements](#requirements) before sending your merge request (except a draft merge request)

## Requirements
* All the code need to confirm to the [PSR-12](https://www.php-fig.org/psr/psr-12/). You can check this locally with `vendor/bin/phpcs`
* We use [PHPMD](https://phpmd.org) to validate the quality of the code. You can check it locally with `vendor/bin/phpmd src text phpmd.xml.dist`
* Add tests for code changes, we use [PHPUnit](https://phpunit.de/). You can run the test with `vendor/bin/phpunit` this wil also generate some reports in the build directory.
* We also use [PHPStan](https://phpstan.org/) to find possible bugs in the code. You can run it with `vendor/bin/phpstan`.
* Document the changes, any functional change or bug fix need to be written in [CHANGELOG.md](CHANGELOG.md). Depending on your change you need to add some documentation to the [README.md](README.md)
* Respect [SemVer](http://semver.org/), we use Semantic Versioning so please respect it with the changes you want to add.
* A merge request for a change. Please don't mix multiple changes in one merge request.
* Ask questions if you are not sure about something ask it. 


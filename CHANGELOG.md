# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Releases

### [0.1.20] - 2023-09-05

* Add more IBAN countries and tests
* Add account-number-prefix

### [0.1.19] - 2023-09-04

* Add more IBAN countries and tests

### [0.1.18] - 2023-09-04

* Add more IBAN countries and tests
* Add national-identification-number

### [0.1.17] - 2023-09-04

* Add more IBAN countries and tests
* Add bic-bank-code

### [0.1.16] - 2023-09-04

* Add more IBAN countries and tests
* Add currency-code

### [0.1.15] - 2023-09-04

* Add more IBAN countries and tests
* Add owner-account-number and account-type

### [0.1.14] - 2023-09-03

* README.md updates

### [0.1.13] - 2023-09-03

* README.md updates

### [0.1.12] - 2023-09-03

* Add IBAN validator for country Turkey
* IBAN number with fixed numbers

### [0.1.11] - 2023-09-03

* Add IBAN validator for country Andorra and Aserbaidschan

### [0.1.10] - 2023-09-03

* Add IBAN validator for country albania
* Refactoring

### [0.1.9] - 2023-09-03

* Add IBAN validator for country spain
* Refactoring

### [0.1.8] - 2023-09-02

* Add IBAN validator spain

### [0.1.7] - 2023-09-02

* README.md updates
* iban:validate output changes

### [0.1.6] - 2023-09-02

* Refactoring

### [0.1.5] - 2023-09-02

* Add php-timezone composer package
* Refactoring

### [0.1.4] - 2023-09-02

* Add IBAN codes from France

### [0.1.3] - 2023-09-02

* Add Liechtenstein bank
* Add formatted IBAN tests

### [0.1.2] - 2023-09-02

* Add more IBAN countries and tests
* Add getIbanFormatted method

### [0.1.1] - 2023-09-02

* Add README.md updates

### [0.1.0] - 2023-09-01

* Initial release with first Coordinate parser and converter
* Add src
* Add tests
  * PHP Coding Standards Fixer
  * PHPMND - PHP Magic Number Detector
  * PHPStan - PHP Static Analysis Tool
  * PHPUnit - The PHP Testing Framework
  * Rector - Instant Upgrades and Automated Refactoring
* Add README.md
* Add LICENSE.md

## Add new version

```bash
# Checkout master branch
$ git checkout main && git pull

# Check current version
$ vendor/bin/version-manager --current

# Increase patch version
$ vendor/bin/version-manager --patch

# Change changelog
$ vi CHANGELOG.md

# Push new version
$ git add CHANGELOG.md VERSION && git commit -m "Add version $(cat VERSION)" && git push

# Tag and push new version
$ git tag -a "$(cat VERSION)" -m "Version $(cat VERSION)" && git push origin "$(cat VERSION)"
```

# Changelog
All notable changes to this project will be documented in this file.

## [Unreleased]

## [1.3.0] - 2021-10-21
### Added
- New config variables `$dirMinJs` and `$dirMinCss` that allow to specify separate directories for minified assets - by [@daycry](https://github.com/daycry)

## [1.2.0] - 2021-01-01
### Added
- New config variable `$autoDeployOnChange` that specifies if we want to make a deploy automatically whenever any change happens to our assets files. Thanks to [@paul45](https://github.com/paul45) for inspiration.

## [1.1.0] - 2020-04-23
### Added
- New config variable `$returnType` that specifies if we want to get `html` (default), `json` or php `array` as a result when we load a file to display.
- New optional config variables `$baseJsUrl` and `$baseCssUrl` to handle serving assets from separate domains.

### Changed
- Use spaces instead of tabs.
- Updates in README file.

### Fixed
- Fix wrong message name for `noVersioningFile` by [@paul45](https://github.com/paul45)
- Fix the way `versions.json` file is set.

## [1.0.0] - 2020-03-27
- Initial realease

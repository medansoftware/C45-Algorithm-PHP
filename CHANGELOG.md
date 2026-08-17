# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0] - 2026-08-17

### Breaking Changes

- **Minimum PHP version raised from `>=5.5` to `^8.1`.** PHP 5.x, 7.x, and 8.0
  are no longer supported. Upgrade your PHP runtime before updating this
  package.
- **`phpoffice/phpspreadsheet` upgraded from `^1.9` to `^2.0 || ^3.0`.**
  If you depend on PhpSpreadsheet directly elsewhere in your project, review
  its own changelog for breaking changes between 1.x and 2.x/3.x.

### Added

- `DataInput::loadCsv()` — load data directly from a CSV file using
  PhpSpreadsheet's dedicated CSV reader, avoiding unreliable format
  auto-detection.
- `TreeNode::toDot()` — export the decision tree to Graphviz DOT format for
  visualization (e.g. via `dot -Tpng tree.dot -o tree.png` or
  [Graphviz Online](https://dreampuf.github.io/GraphvizOnline/)).
- `C45::evaluate()` — measure a built tree's accuracy against a labeled test
  set, returning accuracy, correct/total counts, and a list of misclassified
  rows.
- `LICENSE` file (MIT), formalizing the license already declared in
  `composer.json`.
- PHPUnit test suite (`tests/`) covering the calculators (Gain, Split Info,
  Gain Ratio), tree building, classification, evaluation, and serialization
  (`toArray`, `toJson`).
- GitHub Actions workflow (`.github/workflows/tests.yml`) running the test
  suite on PHP 8.1, 8.2, and 8.3 on every push and pull request.
- `.gitattributes`, marking development-only paths (`tests/`, `.github/`,
  `examples/`, etc.) as `export-ignore` for cleaner Composer package
  archives.
- Sample Graphviz output (`examples/tree.dot`, `examples/tree.png`) generated
  from the Play Tennis dataset, referenced in the README.
- `example.php`, a standalone script demonstrating all output formats
  (string, JSON, array, DOT).
- `composer test` script for running PHPUnit locally.

### Changed

- Moved `example.xlsx` into `examples/example.xlsx` for a cleaner project
  structure.
- Restructured `README.md`: added a table of contents, grouped sections
  (Requirements, Quick Start, Output Formats, Evaluating Accuracy, etc.),
  status badges (Packagist, Tests, PHP version, License), and a rendered
  sample tree image.
- Removed the donation links / social media section from `README.md`.

### Fixed

- `C45::evaluate()` now returns `accuracy` as a proper `float` in all cases
  (previously, a perfect `int/int` division such as `14/14` returned PHP's
  native `int(1)` instead of `float(1.0)`).

## [1.0.2] - 2022-04-25

### Added

- README example demonstrating how to initialize data directly from a PHP
  array (`DataInput::setData()` + `setAttributes()`), instead of only from
  an Excel file.

### Changed

- Updated `example.xlsx` sample file.

## [1.0.1] - 2019-12-09

### Changed

- Made `C45` class properties (`$c45`, `$target_attribute`, `$gainCalculator`,
  etc.) `public` instead of `protected`/`private`, allowing direct
  configuration (e.g. `$c45->c45 = $input;`) as shown in the README examples.

### Fixed

- `DataInput::setData()` now calls `populateClasses()` internally, so
  attribute value classes are populated correctly when data is set directly
  from an array instead of loaded from a file.
- `DataInput::isMatch()` now correctly handles both array and object rows
  when matching criteria, instead of assuming array access only.

## [1.0.0] - 2019-11-20

### Added

- Initial release of the C4.5 decision tree algorithm implementation in PHP.
- `Algorithm\C45` — main class to build a decision tree from an Excel file
  or a `DataInput` instance, and classify new data.
- `Algorithm\C45\DataInput` — load and query row data from `.xlsx` files
  (via PhpSpreadsheet) or plain PHP arrays.
- `Algorithm\C45\TreeNode` — represents a node in the decision tree, with
  `toArray()`, `toJson()`, and `toString()` output formats, and `classify()`
  for predicting new data.
- `Algorithm\C45\Calculator\GainCalculator`, `SplitInfoCalculator`, and
  `GainRatioCalculator` — implement the entropy-based Gain, Split Info, and
  Gain Ratio calculations used to select the best splitting attribute at
  each node.
- Example spreadsheet (`example.xlsx`) and README usage examples.
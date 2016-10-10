# php5to7
Convert PHP5 code to PHP7 (scalar type hinted parameters, return types) by reading the PHPdoc blocks of your source file. For example, in PHP5 code if you had `@param int $index`, the function signature will be modified as `public function myFunc(int $index) {`


## Usage

(Not in packagist yet) Clone/download this repository and install the dependencies using composer.

```
composer install
```

```
Usage:
php php5to7.php [OPTIONS] <file/directory>

--overwrite     Instead of outputting the changed source, writes
                the php7 changes into the file.
--backup        For every file it modifies, copies the original
                to filename.php.bak.
-h  --help      This help message.

<file/directory> The path to the individual file, or a directory
                 to process every php file recursively.
```

## Limitations & Risks

This should not be used for large projects, mission critical code, etc. This is currently an on-going experiment.

* No typehinting will be done on:
 * Parameters that are not documented with PHPdoc blocks
 * Parameters that are more than one type, e.g. `string|array`
 * Parameters other than the basic scalars: `int`, `float`, `string`, and `bool`
* Code with scalar typehints **may** operate slower than code without them (might be negligible)

## Contributing

* Pull requests welcome
* Please use the PSR2 standard with CodeSniffer


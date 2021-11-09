<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use League\Flysystem\Filesystem;
use League\Flysystem\Adapter\Local;
use Flyfinder\Finder;
use Flyfinder\Path;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use Flyfinder\Specification\AndSpecification;

// (03-sample-files based on some phpDocumentor2 src files)
$filesystem = new Filesystem(new Local(__DIR__ . '/03-sample-files'));
$filesystem->addPlugin(new Finder());

/*
 * "phpdoc -d src -i src/phpDocumentor/DomainModel"
 * should result in src/Cilex and src/phpDocumentor/. files being found,
 * but src/phpDocumentor/DomainModel files being left out
 */
$dashDirectoryPath = new InPath(new Path('src'));
$dashIgnorePath = new InPath(new Path('src/phpDocumentor/DomainModel'));
$isHidden = new IsHidden();
$isPhpFile = new HasExtension(['php']);
$spec = new AndSpecification($dashDirectoryPath, $dashIgnorePath->notSpecification());
$spec->andSpecification($isHidden->notSpecification());
$spec->andSpecification($isPhpFile);

$generator = $filesystem->find($spec);
$result = [];
foreach($generator as $value) {
    $result[] = $value;
}

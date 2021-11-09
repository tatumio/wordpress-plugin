<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter as Adapter;
use Flyfinder\Finder;
use Flyfinder\Path;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;

/*
 * First create a new Filesystem and add the FlySystem plugin
 * In this example we are using a filesystem with the memory adapter
 */
$filesystem = new Filesystem(new Adapter());
$filesystem->addPlugin(new Finder());

// Create some demo files
$filesystem->write('test.txt', 'test');
$filesystem->write('.hiddendir/.test.txt', 'test');
$filesystem->write('.hiddendir/found.txt', 'test');
$filesystem->write('.hiddendir/normaldir/example.txt', 'test');

/*
 * In order to tell FlyFinder what to find, you need to give it a specification
 * In this example the specification will be satisfied by *.txt files
 * within the .hidden directory and its subdirectories that are not hidden
 */
$isHidden = new IsHidden();
$hasExtension = new HasExtension(['txt']);
$inPath = new InPath(new Path('.hiddendir'));
$specification = $inPath->andSpecification($hasExtension)->andSpecification($isHidden->notSpecification());

//FlyFinder will yield a generator object with the files that are found
$generator = $filesystem->find($specification);

$result = [];

foreach ($generator as $value) {
    $result[] = $value;
}

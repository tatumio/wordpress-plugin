<?php
require_once(__DIR__ . '/../vendor/autoload.php');

use League\Flysystem\Filesystem;
use League\Flysystem\Memory\MemoryAdapter as Adapter;
use Flyfinder\Finder;
use Flyfinder\Specification\IsHidden;

/*
 * First create a new Filesystem and add the FlySystem plugin
 * In this example we are using a filesystem with the memory adapter
 */
$filesystem = new Filesystem(new Adapter());
$filesystem->addPlugin(new Finder());

// Create some demo files
$filesystem->write('test.txt', 'test');
$filesystem->write('.hiddendir/.test.txt', 'test');

//In order to tell FlyFinder what to find, you need to give it a specification
//In this example the specification will be satisfied by files and directories that are hidden
$specification = new IsHidden();

//FlyFinder will yield a generator object with the files that are found
$generator = $filesystem->find($specification);

$result = [];

foreach ($generator as $value) {
    $result[] = $value;
}

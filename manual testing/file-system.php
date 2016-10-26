<?php

use FileSystem\Drivers\LocalFileSystem;
use FileSystem\Disk;
use FileSystem\Disks\Manager as Disks;
use FileSystem\FileSystem;
use FileSystem\FileTree;
use FileSystem\Manager;
use FileSystem\Root;

use function FileSystem\inside;

require __DIR__ . '/../vendor/autoload.php';


$disks = new  Disks;
$disks->add ( new Disk ( 'local', __DIR__, new LocalFileSystem ) );

$manager = new Manager ( $disks );
$tree = new FileTree;

return new FileSystem ( $tree, $manager );

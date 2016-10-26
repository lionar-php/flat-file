<?php

use FileSystem\File;
use FileSystem\Root;
use FlatFile\Stack;

require __DIR__ . '/../vendor/autoload.php';



$fileSystem = require __DIR__ . '/file-system.php';

$root = new Root;
$file = new File ( 'stack.data', $root );
$file->write ( file_get_contents ( __DIR__ . '/stack.data' ) ); // mock file tree persistence


$stack = new Stack ( $fileSystem, $file );

// $stack->set ( 'id', 'my value' );
// $stack->set ( 'blah', 'baaah' );
// $stack->set ( 'my id', 'uniqid' );


var_dump ( $stack->get ( 'id' ) );
var_dump ( $stack->get ( 'blah' ) );
var_dump ( $stack->get ( 'my id' ) );

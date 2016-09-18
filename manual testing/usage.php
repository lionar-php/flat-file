<?php

use FileSystem\Directory;
use FileSystem\File;
use FileSystem\FileTree;
use FileSystem\FileSystems\LocalFileSystem;
use FlatFile\Stack;


require __DIR__ . '/../vendor/autoload.php';



$fileTree = new FileTree ( array (

	$root = new Directory ( __DIR__ ),
	$file = new File ( 'stack.data', $root ),
) );

$fileSystem = new LocalFileSystem ( $fileTree );
$stack = new Stack ( array ( $fileSystem ), $file );

$stack->set ( 'id', 'my value' );
$stack->set ( 'blah', 'baaah' );
$stack->set ( 'my id', 'uniqid' );

var_dump ( $stack->get ( 'id' ) );
var_dump ( $stack->get ( 'blah' ) );
var_dump ( $stack->get ( 'my id' ) );
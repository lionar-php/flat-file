<?php

use Agreed\Storage\Store;
use FileSystem\File;
use FileSystem\FileTree;
use FileSystem\FileSystems\Dropbox;
use FileSystem\FileSystems\LocalFileSystem;
use FlatFile\Stack;

class Exercise
{
	public $name = '';

	public function __construct ( $name )
	{
		$this->name = $name;
	}
}

$fileTree = new FileTree ( array ( ) );
$dropboxFileTree = new FileTree ( array ( ) );


// file will be placed inside the root
$file = new File ( 'exercises.data' );


$fileSystem = new LocalFileSystem ( $fileTree );
$dropbox = new Dropbox ( $dropboxFileTree, 'http://dropbox/eyesports' );


$stack = new Stack ( array ( $fileSystem, $dropbox ), $file );
$store = new Store ( $stack );

$benchPress = new Exercise ( 'bench press' );
$store->save ( $benchPress->name, $benchPress );
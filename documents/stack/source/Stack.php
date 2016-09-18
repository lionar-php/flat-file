<?php

namespace FlatFile;

use FileSystem\File;
use FileSystem\FileSystem;

class Stack implements \Agreed\Storage\Stack
{
	private $fileSystems = array ( );
	private $file = null;

	// file will be auto created on the file system...
	public function __construct ( array $fileSystems = array ( ), File $file )
	{
		foreach ( $fileSystems as $fileSystem )
			$this->add ( $fileSystem );
		$this->file = $file;
	}

	public function append ( $identifier, $entity )
	{
		$data = unserialize ( $this->file->contents );
		$data [ $identifier ] = $entity;
		$this->file->write ( serialize ( $data ) );
		$this->write ( $this->file );
	}

	public function has ( $identifier )
	{
		foreach ( $this->fileSystems as $fileSystem )
			$this->checkFor ( $identifier, in ( $fileSystem ) );
	}

	private function write ( File $file )
	{
		foreach ( $this->fileSystems as $fileSystem )
			$fileSystem->write ( $file ); // adds / updates file on file systems file tree
	}

	private function add ( FileSystem $fileSystem )
	{
		$this->fileSystems [ ] = $fileSystem;
	}

	private function checkFor ( $identifier, FileSystem $fileSystem )
	{
		// ... can we do this?
		// yess we will lock the files that are put in here...
		array_key_exists ( $identifier, unserialize ( $this->file->contents ) );
	}
}
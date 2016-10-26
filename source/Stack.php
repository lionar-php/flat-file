<?php

namespace FlatFile;

use FileSystem\File;
use FileSystem\FileSystem;
use InvalidArgumentException;

class Stack implements \Agreed\Technical\Storage\Stack
{
	private $entries = array ( );
	private $fileSystem, $file = null;

	public function __construct ( FileSystem $fileSystem, File $file )
	{
		$this->entries = $this->read ( $file );
		$this->fileSystem = $fileSystem;
		$this->file = $file;
	}

	public function set ( $identifier, $value )
	{
		$this->entries [ $identifier ] = $value;
		$this->file->write ( serialize ( $this->entries ) );
		$this->fileSystem->write ( $this->file );
	}

	public function has ( $identifier ) : bool
	{
		return ( bool ) isset ( $this->entries [ $identifier ] );
	}

	public function all ( ) : array
	{
		return $this->entries;
	}

	public function get ( $identifier )
	{
		if ( $this->has ( $identifier ) )
			return $this->entries [ $identifier ];
	}

	private function read ( File $file )
	{
		if ( $file->isEmpty ( ) )
			return array ( );
		if ( ! is_array ( ( $content = unserialize ( $file->content ) ) ) )
			throw new InvalidArgumentException ( "The file contents of $file->path must be a serialized array." );
		return $content;
	}
}


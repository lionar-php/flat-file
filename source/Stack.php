<?php

namespace FlatFile;

use FileSystem\File;
use FileSystem\FileSystem;
use InvalidArgumentException;

class Stack implements \Agreed\Storage\Stack
{
	private $file = null;
	private $fileSystems, $entries = array ( );

	public function __construct ( array $fileSystems, File $file )
	{
		$this->entries = $this->prepare ( $file );
		$this->file = $file;
		foreach ( $fileSystems as $fileSystem )
			$this->add ( $fileSystem );
	}
	
	public function set ( $identifier, $value )
	{
		$this->entries [ $identifier ] = $value;
		$this->file->write ( serialize ( $this->entries ) );
		$this->write ( $this->file );
	}

	public function all ( ) : array
	{
		return $this->entries;
	}

	public function get ( $identifier )
	{
		if ( ! $this->has ( $identifier ) )
			return null;

		return $this->entries [ $identifier ];
	}

	public function has ( $identifier ) : bool
	{
		return array_key_exists ( $identifier, $this->entries );
	}

	private function add ( FileSystem $fileSystem )
	{
		$this->fileSystems [ get_class ( $fileSystem ) ] = $fileSystem;
	}

	private function write ( File $file )
	{
		foreach ( $this->fileSystems as $fileSystem )
			$fileSystem->write ( $file );
	}

	private function prepare ( File $file )
	{
		if ( $file->isEmpty ( ) )
			return array ( );
		if ( ! is_array ( ( $content = unserialize ( $file->content ) ) ) )
			throw new InvalidArgumentException ( "The file contents of $file->path must be a serialized array." );
		return $content;
	}
}


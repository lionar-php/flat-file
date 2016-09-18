<?php

namespace FlatFile;

use FileSystem\File;
use FileSystem\FileSystem;
use InvalidArgumentException;

class Stack implements \Agreed\Storage\Stack
{
	private $file = null;
	private $fileSystems = array ( );

	public function __construct ( array $fileSystems, File $file )
	{
		$this->file = $file;
		foreach ( $fileSystems as $fileSystem )
			$this->add ( $fileSystem );
	}
	
	public function set ( $identifier, $value )
	{
		$data = unserialize ( $this->file->content );
		$data [ $identifier ] = $value;
		$this->file->write ( serialize ( $data ) );
		$this->write ( $this->file );
	}

	public function get ( $identifier )
	{
		if ( ! $this->has ( $identifier ) )
			return null;

		$entries = unserialize ( $this->file->content );
		return $entries [ $identifier ];
	}

	public function has ( $identifier ) : bool
	{
		return array_key_exists ( $identifier, unserialize ( $this->file->content ) );
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
}
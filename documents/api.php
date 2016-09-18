<?php

use Agreed\Storage\Store;
use Agreed\Storage\Stack;

class Trainer
{
	public function __construct ( Store $store )
	{
		$this->store = $store;
	}

	public function remember ( Exercise $exercise )
	{
		if ( ! $this->store->has ( $exercise->name ) )
			$this->store->save ( $exercise->name, $exercise );
	}

	public function change ( Exercise $exercise )
	{
		$this->store->overwrite ( $exercise->name, with ( $exercise ) );
	}
}

use Agreed\Storage\Store;


class SportsServiceProvider
{
	private $store = null;

	public function __construct ( Application $application )
	{
		parent::__construct ( $application );
		$file = new File ( 'exercises.data' );
		$stack = new Stack ( array ( new LocalFileSystem ( $fileTree, __DIR__ . '/cache' ) ), $file );
		$this->store = new Store ( $stack );
	}

	// how to ensure we always have the pointer to the correct stack location?
	public function register ( )
	{
		$this->application->share ( 'sports\\trainer', function ( ) : Store
		{
			$trainer 
		} );

		$this->application->share ( 'Sports\\Stack', function ( )
		{

		} );
	}
}

// the store is the overall manager of how things get stored
// what happens if an identifier already exists?
// what happens if the identifier you try to get does not exist?
class Store
{
	public function __construct ( Stack $stack )
	{
		$this->stack = $stack;
	}

	public function save ( $identifier, $entity )
	{
		if ( ! $this->stack->has ( $identifier ) )
			$this->stack->put ( $identifier, $entity ); // could do all sorts of fun with $entity here
	}

	public function get ( $identifier )
	{
		if ( ! $this->stack->has ( $identifier ) )
			throw new EntityNotFoundException ( );
		return $this->stack->get ( $identifier );
	}
}


// the stack manages the storage implementation
// what becomes the literal stored form of the stored object
class FileSystemStack implements Stack
{
	private $pointer = array ( );
	private $file = null;

	public function __construct ( array $pointers = array ( ), File $file )
	{
		foreach ( $pointers as $pointer )
			$this->add ( $pointer );
		$this->file = $file;
	}

	public function put ( $identifier, $entity )
	{
		$this->file->write ( serialize ( array ( $identifier => $entity ) ) );
		foreach ( $this->pointers as $pointer )
			$pointer->write ( $file );
	}

	public function has ( $identifier )
	{
		foreach ( $this->pointers as $pointer )
			$this->checkFor ( $identifier, in ( $pointer ) );
	}

	private function add ( Pointer $pointer )
	{
		$this->pointers [ ] = $pointer;
	}

	private function checkFor ( $identifier, Pointer $pointer )
	{
		array_key_exists ( $identifier, $pointer->read ( $this->file ) );
	}
}
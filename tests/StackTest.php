<?php

namespace FlatFile\Tests;

use FlatFile\Stack;
use Mockery;
use ReflectionClass;
use Testing\TestCase;

class StackTest extends TestCase
{
	private $stack, $file = null;

	public function setUp ( )
	{
		$this->fileSystem = $fileSystem = Mockery::mock ( 'FileSystem\\FileSystem' )->shouldIgnoreMissing ( );
		$this->file = $file = Mockery::mock ( 'FileSystem\\File' )->shouldIgnoreMissing ( );
		$this->file->content = serialize ( array ( ) );
		$this->stack = new Stack ( $fileSystem, $file );
	}

	/*
	|--------------------------------------------------------------------------
	| Construction testing.
	|--------------------------------------------------------------------------
	|
	| The constructor should initialize the entries 
	| inside the stack. Here we test the entries are correctly
	| initialized.
	*/

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 * @dataProvider nonArrayValues
	 */
	public function __construct_withFileThatIsNotEmptyAndDoesNotContainASerializedArray_throwsException ( $value )
	{
		$this->file->shouldReceive ( 'isEmpty' )->andReturn ( false );
		$this->file->content = serialize ( $value );

		$stack = new Stack ( $this->fileSystem, $this->file );
	}

	/**
	 * @test
	 */
	public function __construct_withEmptyFile_setsEntriesAsEmptyArray ( )
	{
		$this->file->shouldReceive ( 'isEmpty' )->andReturn ( true );

		$stack = new Stack ( $this->fileSystem, $this->file );
		assertThat ( $this->property ( $stack, 'entries' ), is ( emptyArray ( ) ) );
	}

	/**
	 * @test
	 */
	public function __construct_withFileWithSerializedArray_unserializesArrayAndSetsThatAsEntries ( )
	{
		$entry = array ( 'name' => 'Aron Wouters' );

		$this->file->content = serialize ( $entry );
		$this->file->shouldReceive ( 'isEmpty' )->andReturn ( false );

		$stack = new Stack ( $this->fileSystem, $this->file );
		assertThat ( $this->property ( $stack, 'entries' ), is ( identicalTo ( $entry ) ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Setting values inside the stack.
	|--------------------------------------------------------------------------
	|
	| Set allows to add values to the flat file stack. Here we
	| test that entries are correctly stored in memory and that
	| the correct file system methods are called.
	*/

	/**
	 * @test
	 */
	public function set_withEntry_addsEntryToStackEntries ( )
	{
		$identifier = 'name';
		$value = 'Aron Wouters';
		$this->stack->set ( $identifier, $value );
		assertThat ( $this->property ( $this->stack, 'entries' ), hasEntry ( $identifier, $value ) );
	}

	/**
	 * @test
	 */
	public function set_withEntry_callsFileWriteWithSerializedEntry ( )
	{
		$identifier = 'name';
		$value = 'Aron Wouters';

		$this->file->shouldReceive ( 'write' )->with ( serialize ( array ( $identifier => $value) ) )->once ( );
		
		$this->stack->set ( $identifier, $value );
	}

	/**
	 * @test
	 */
	public function set_withEntry_callsFileSystemWriteWithFile ( )
	{
		$identifier = 'name';
		$value = 'Aron Wouters';

		$this->fileSystem->shouldReceive ( 'write' )->with ( $this->file )->once ( );
		
		$this->stack->set ( $identifier, $value );
	}

	/*
	|--------------------------------------------------------------------------
	| Check if the stack has values.
	|--------------------------------------------------------------------------
	|
	| Has provides the functionality to check whether the stack
	| has a certain identifier registered. Here we test if the correct
	| boolean value is returned.
	*/

	/**
	 * @test
	 */
	public function has_withIdentifierThatDoesNotExistInsideStack_returnsFalse ( )
	{
		$identifier = 'non existent';
		assertThat ( $this->stack->has ( $identifier ), is ( identicalTo ( false ) ) );
	}

	/**
	 * @test
	 */
	public function has_withIdentifierThatDoesExistInsideStack_returnsTrue ( )
	{
		$identifier = 'uniqid';
		$this->stack->set ( $identifier, 'value' );

		assertThat ( $this->stack->has ( $identifier ), is ( identicalTo ( true ) ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Getting all values from the stack.
	|--------------------------------------------------------------------------
	|
	| All provides functionality to get all values from the stack as
	| an array. Here we test that all returns all the values set
	| in the stack.
	*/

	/**
	 * @test
	 */
	public function all_withMultipleEntriesInStack_returnsAllEntries ( )
	{
		$entries = array (

			'name'	=> 'Aron Wouters',
			'street' => 'Klaphekstraat'
		);

		foreach ( $entries as $identifier => $value )
			$this->stack->set ( $identifier, $value );

		assertThat ( $this->stack->all ( ), is ( identicalTo ( $entries ) ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Getting single values from the stack.
	|--------------------------------------------------------------------------
	|
	| Get provides the functionality to retrieve singular values
	| by identifier. Here we test the correct value is returned.
	*/

	/**
	 * @test
	 */
	public function get_withIdentifierThatDoesNotExistInStack_returnsNull ( )
	{
		assertThat ( $this->stack->get ( 'non existent id' ), is ( identicalTo ( null ) ) );
	}

	/**
	 * @test
	 */
	public function get_withIdentifierThatDoesExistInStack_returnsValueFoundWithIdentifier ( )
	{
		$identifier = 'uniqid';
		$value = 'value';

		$this->stack->set ( $identifier, $value );

		assertThat ( $this->stack->get ( $identifier ), is ( identicalTo ( $value ) ) );
	}
}
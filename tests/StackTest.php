<?php

namespace FlatFile\Tests;

use FlatFile\Stack;
use Mockery;
use ReflectionClass;
use Testing\TestCase;

class StackTest extends TestCase
{
	private $stack, $file, $fileSystems = null;
	private $preContent = '';

	public function setUp ( )
	{
		$fileSystems = array (

			$fileSystem = Mockery::mock ( 'FileSystem\\FileSystems\\LocalFileSystem' ),
		);

		$preContent = serialize ( array ( 'uniqid' => 'unique id value', 'some value' => 'yeah value' ) );
		$file = Mockery::mock ( 'FileSystem\\File' );
		$file->content = $preContent;

		$this->stack = new Stack ( $fileSystems, $file );
		$this->file = $file;
		$this->fileSystems = $fileSystems;
		$this->preContent = $preContent;
	}

	/*
	|--------------------------------------------------------------------------
	| Constructor testing.
	|--------------------------------------------------------------------------
	*/

	/**
	 * @test
	 * @expectedException  TypeError
	 */
	public function __construct_withArrayWithNonFileSystemObject_throwsException ( )
	{
		$fileSystems = array ( 'non file system' );
		$stack = new Stack ( $fileSystems, $this->file );
	}

	/**
	 * @test
	 */
	public function __construct_withArrayOffFileSystemsWithDuplicateFileSystemType_addsItOnlyOnce ( )
	{
		$fileSystems = array (

			$fileSystem1 = Mockery::mock ( 'FileSystem\\FileSystems\\LocalFileSystem' ),
			$fileSystem2 = Mockery::mock ( 'FileSystem\\FileSystems\\LocalFileSystem' ),
		);

		$stack = new Stack ( $fileSystems, $this->file );
		assertThat ( $this->property ( $stack, 'fileSystems' ), is ( arrayWithSize ( 1 ) ) );
	}

	/**
	 * @test
	 * @expectedException InvalidArgumentException
	 */
	public function __construct_withFileThatDoesNotContainASerializedArray_throwsException ( )
	{
		$this->file->content = serialize ( 'some value' );
		$stack = new Stack ( $this->fileSystems, $this->file );
	}

	/*
	|--------------------------------------------------------------------------
	| Set method testing.
	|--------------------------------------------------------------------------
	*/

	/**
	 * @test
	 */
	public function set_withIdentifierAndEntity_callsFileWriteMethodAndAllRegisteredFileSystemsWriteMethods ( )
	{
		$identifier = 'id';
		$value = 'value';

		$content = serialize ( array_merge ( unserialize ( $this->preContent ), array ( $identifier => $value ) ) );

		$this->file->shouldReceive ( 'write' )->with ( $content )->once ( );

		foreach ( $this->fileSystems as $fileSystem )
			$fileSystem->shouldReceive ( 'write' )->with ( $this->file )->once ( );

		$this->stack->set ( $identifier, $value );
	}

	/*
	|--------------------------------------------------------------------------
	| All method testing.
	|--------------------------------------------------------------------------
	*/

	/**
	 * @test
	 */
	public function all_whenFileHasElements_returnsElementsAsArray ( )
	{
		assertThat ( $this->stack->all ( ), is ( identicalTo ( unserialize ( $this->file->content ) ) ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Get method testing.
	|--------------------------------------------------------------------------
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
		assertThat ( $this->stack->get ( 'uniqid' ), is ( identicalTo ( 'unique id value' ) ) );
	}

	/*
	|--------------------------------------------------------------------------
	| Has method testing.
	|--------------------------------------------------------------------------
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
		assertThat ( $this->stack->has ( $identifier ), is ( identicalTo ( true ) ) );
	}
}
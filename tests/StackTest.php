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

		$preContent = serialize ( array ( 'uniqid' => 'unique id entity' ) );
		$file = Mockery::mock ( 'FileSystem\\File' );
		$file->content = $preContent;

		$this->stack = new Stack ( $fileSystems, $file );
		$this->file = $file;
		$this->fileSystems = $fileSystems;
		$this->preContent = $preContent;
	}

	/*
	|--------------------------------------------------------------------------
	| Constructor testing
	|--------------------------------------------------------------------------
	*/

	/**
	 * @test
	 * @expectedException  TypeError
	 */
	public function __construct_withArrayWithNonFileSystemObject_throwsException ( )
	{
		$fileSystems = array ( 'non file system' );
		$stack = new Stack ( $fileSystems );
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

	/*
	|--------------------------------------------------------------------------
	| Append method testing.
	|--------------------------------------------------------------------------
	*/

	/**
	 * @test
	 */
	public function append_withIdentifierAndEntity_callsFileWriteMethodAndAllRegisteredFileSystemsWriteMethods ( )
	{
		$identifier = 'id';
		$entity = 'entity';

		$content = serialize ( array_merge ( unserialize ( $this->preContent ), array ( $identifier => $entity ) ) );

		$this->file->shouldReceive ( 'write' )->with ( $content )->once ( );

		foreach ( $this->fileSystems as $fileSystem )
			$fileSystem->shouldReceive ( 'write' )->with ( $this->file )->once ( );

		$this->stack->append ( $identifier, $entity );
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
<?php

// rules are made inside file system,
// decides what driver can store the persons.data file.
// at the file system level we also decide what happens with the duplicate
// files spreaded over different drivers, which one is the primary source, what
// happens if another application modifies this file? ( other drivers get updated or will they become backups )

$root = new Root;
$storage = new Directory ( 'storage', $root );
$file = new File ( 'persons.data', $storage );

$stack = new Stack ( $fileSystem, $file );
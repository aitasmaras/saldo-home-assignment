<?php

require __DIR__ . '/vendor/autoload.php';

use VirtualFileSystem\FileSystem\VirtualFileSystem;
use VirtualFileSystem\Persistence\FilePersistence;
use VirtualFileSystem\CLI\CommandHandler;

// Create dependencies
$persistence = new FilePersistence(__DIR__ . '/storage/filesystem.json');
$fileSystem = new VirtualFileSystem($persistence);
$commandHandler = new CommandHandler($fileSystem);

// Start the CLI
echo "Virtual File System CLI. Type 'help' for a list of commands.\n";

while (true) {
    $command = readline('Enter command: ');
    $commandHandler->handleCommand($command);
}

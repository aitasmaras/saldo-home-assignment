<?php

namespace VirtualFileSystem\Tests;

use PHPUnit\Framework\TestCase;
use VirtualFileSystem\FileSystem\VirtualFileSystem;
use VirtualFileSystem\Persistence\PersistenceInterface;
use PHPUnit\Framework\MockObject\MockObject;

class VirtualFileSystemTest extends TestCase
{
    private VirtualFileSystem $fileSystem;
    private MockObject & PersistenceInterface $persistence;

    protected function setUp(): void
    {
        $this->persistence = $this->createMock(PersistenceInterface::class);
        $this->fileSystem = new VirtualFileSystem($this->persistence);
    }

    public function testCreateAndDeleteFolder(): void
    {
        // Mock the persistence layer to return an empty array on load
        $this->persistence->method('load')->willReturn([]);

        // Create a folder
        $this->fileSystem->createFolder('/root/folder1');
        $this->assertStringContainsString('folder1', $this->fileSystem->displayTree());

        // Delete the folder
        $this->fileSystem->deleteFolder('/root/folder1');
        $this->assertStringNotContainsString('folder1', $this->fileSystem->displayTree());
    }

    public function testAddAndRemoveFile(): void
    {
        // Mock the persistence layer to return an empty array on load
        $this->persistence->method('load')->willReturn([]);

        // Create a folder
        $this->fileSystem->createFolder('/root/folder1');

        // Add a file
        $this->fileSystem->addFile('/root/folder1', __FILE__);
        $files = $this->fileSystem->listFiles('/root/folder1');
        $this->assertContains(basename(__FILE__), $files);

        // Remove the file
        $this->fileSystem->removeFile('/root/folder1', basename(__FILE__));
        $files = $this->fileSystem->listFiles('/root/folder1');
        $this->assertNotContains(basename(__FILE__), $files);
    }

    public function testDisplayTree(): void
    {
        // Mock the persistence layer to return an empty array on load
        $this->persistence->method('load')->willReturn([]);

        // Create folders and files
        $this->fileSystem->createFolder('/root/folder1');
        $this->fileSystem->createFolder('/root/folder2');
        $this->fileSystem->addFile('/root', __FILE__);

        // Verify the tree output
        $tree = $this->fileSystem->displayTree();
        $this->assertStringContainsString('root', $tree);
        $this->assertStringContainsString('folder1', $tree);
        $this->assertStringContainsString('folder2', $tree);
        $this->assertStringContainsString(basename(__FILE__), $tree);
    }
}

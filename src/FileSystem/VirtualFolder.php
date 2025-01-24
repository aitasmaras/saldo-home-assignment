<?php

namespace VirtualFileSystem\FileSystem;

class VirtualFolder
{
    private $name;
    private $subfolders = [];
    private $files = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function addSubfolder(VirtualFolder $folder): void
    {
        $this->subfolders[] = $folder;
    }

    public function removeSubfolder(string $name): void
    {
        $this->subfolders = array_filter($this->subfolders, fn($folder) => $folder->getName() !== $name);
    }

    public function addFile(VirtualFile $file): void
    {
        $this->files[] = $file;
    }

    public function removeFile(string $name): void
    {
        $this->files = array_filter($this->files, fn($file) => $file->getName() !== $name);
    }

    public function getSubfolders(): array
    {
        return $this->subfolders;
    }

    public function getFiles(): array
    {
        return $this->files;
    }
}

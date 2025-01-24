<?php

namespace VirtualFileSystem\FileSystem;

class VirtualFile
{
    private $name;
    private $localPath;

    public function __construct(string $name, string $localPath)
    {
        $this->name = $name;
        $this->localPath = $localPath;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLocalPath(): string
    {
        return $this->localPath;
    }
}

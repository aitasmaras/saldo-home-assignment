<?php

namespace VirtualFileSystem\Persistence;

class FilePersistence implements PersistenceInterface
{
    private $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
    }

    public function save(array $data): void
    {
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents($this->filePath, $json);
    }

    public function load(): array
    {
        if (file_exists($this->filePath)) {
            return json_decode(file_get_contents($this->filePath), true);
        }
        return [];
    }
}

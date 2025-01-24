<?php

namespace VirtualFileSystem\Persistence;

interface PersistenceInterface
{
    public function save(array $data): void;
    public function load(): array;
}

<?php

namespace VirtualFileSystem\FileSystem;

use VirtualFileSystem\Persistence\PersistenceInterface;

class VirtualFileSystem
{
    private $root;
    private $persistence;

    public function __construct(PersistenceInterface $persistence)
    {
        $this->root = new VirtualFolder('');
        $this->persistence = $persistence;
        $this->load();
    }

    public function getRoot(): VirtualFolder
    {
        return $this->root;
    }

    public function createFolder(string $path): void
    {
        $parts = explode('/', trim($path, '/'));
        $current = $this->root;

        foreach ($parts as $part) {
            $found = false;
            foreach ($current->getSubfolders() as $subfolder) {
                if ($subfolder->getName() === $part) {
                    $current = $subfolder;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $newFolder = new VirtualFolder($part);
                $current->addSubfolder($newFolder);
                $current = $newFolder;
            }
        }
        $this->save();
    }

    public function deleteFolder(string $path): void
    {
        $parts = explode('/', trim($path, '/'));
        $current = $this->root;

        for ($i = 0; $i < count($parts) - 1; $i++) {
            $found = false;
            foreach ($current->getSubfolders() as $subfolder) {
                if ($subfolder->getName() === $parts[$i]) {
                    $current = $subfolder;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new \Exception("Folder not found: {$parts[$i]}");
            }
        }

        $folderName = end($parts);
        $current->removeSubfolder($folderName);
        $this->save();
    }

    public function addFile(string $folderPath, string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Local file does not exist: {$filePath}");
        }

        $folder = $this->getFolderByPath($folderPath);
        $fileName = basename($filePath);
        $file = new VirtualFile($fileName, $filePath);
        $folder->addFile($file);
        $this->save();
    }

    public function removeFile(string $folderPath, string $fileName): void
    {
        $folder = $this->getFolderByPath($folderPath);
        $folder->removeFile($fileName);
        $this->save();
    }

    public function listFiles(string $folderPath): array
    {
        $folder = $this->getFolderByPath($folderPath);
        $files = array_map(fn($file) => $file->getName(), $folder->getFiles());
        $folders = array_map(fn($folder) => $folder->getName() . '/', $folder->getSubfolders());
        return array_merge($folders, $files);
    }

    public function displayTree(): string
    {
        return $this->renderTree($this->root);
    }

    private function renderTree(VirtualFolder $folder, $indent = ''): string
    {
        $tree = $indent . $folder->getName() . PHP_EOL;
        foreach ($folder->getSubfolders() as $subfolder) {
            $tree .= $this->renderTree($subfolder, $indent . '  ');
        }
        foreach ($folder->getFiles() as $file) {
            $tree .= $indent . '  ' . $file->getName() . PHP_EOL;
        }
        return $tree;
    }

    private function getFolderByPath(string $path): VirtualFolder
    {
        $parts = explode('/', trim($path, '/'));
        $current = $this->root;

        foreach ($parts as $part) {
            $found = false;
            foreach ($current->getSubfolders() as $subfolder) {
                if ($subfolder->getName() === $part) {
                    $current = $subfolder;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                throw new \Exception("Folder not found: {$part}");
            }
        }
        return $current;
    }

    private function save(): void
    {
        $data = $this->serializeFolder($this->root);
        $this->persistence->save($data);
    }

    private function load(): void
    {
        $data = $this->persistence->load();
        if (!empty($data)) {
            $this->root = $this->deserializeFolder($data);
        }
    }

    private function serializeFolder(VirtualFolder $folder): array
    {
        return [
            'name' => $folder->getName(),
            'subfolders' => array_map([$this, 'serializeFolder'], $folder->getSubfolders()),
            'files' => array_map(fn($file) => [
                'name' => $file->getName(),
                'localPath' => $file->getLocalPath(),
            ], $folder->getFiles()),
        ];
    }

    private function deserializeFolder(array $data): VirtualFolder
    {
        $folder = new VirtualFolder($data['name']);
        foreach ($data['subfolders'] as $subfolderData) {
            $folder->addSubfolder($this->deserializeFolder($subfolderData));
        }
        foreach ($data['files'] as $fileData) {
            $folder->addFile(new VirtualFile($fileData['name'], $fileData['localPath']));
        }
        return $folder;
    }
}

<?php

namespace VirtualFileSystem\CLI;

use VirtualFileSystem\FileSystem\VirtualFileSystem;

class CommandHandler
{
    private $fileSystem;
    private $currentPath;

    public function __construct(VirtualFileSystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
        $this->currentPath = '/root';
    }

    public function handleCommand(string $command): void
    {
        $args = explode(' ', $command);
        $action = $args[0] ?? '';

        try {
            switch ($action) {
                case 'mkdir':
                    $path = $this->resolvePath($args[1]);
                    $this->fileSystem->createFolder($path);
                    echo "Folder created: {$path}\n";
                    break;
                case 'rmdir':
                    $path = $this->resolvePath($args[1]);
                    $this->fileSystem->deleteFolder($path);
                    echo "Folder deleted: {$path}\n";
                    break;
                case 'cp':
                    $localPath = $args[1];
                    $virtualPath = $this->resolvePath($args[2] ?? $this->currentPath);
                    $this->fileSystem->addFile($virtualPath, $localPath);
                    echo "File copied: {$localPath} to {$virtualPath}\n";
                    break;
                case 'rm':
                    $fileName = $args[1];
                    $virtualPath = $this->resolvePath($args[1] ?? $this->currentPath);
                    $this->fileSystem->removeFile($virtualPath, $fileName);
                    echo "File removed: {$fileName} from {$virtualPath}\n";
                    break;
                case 'ls':
                    $virtualPath = $this->resolvePath($args[1] ?? $this->currentPath);
                    $files = $this->fileSystem->listFiles($virtualPath);
                    echo implode("\n", $files) . "\n";
                    break;
                case 'tree':
                    echo $this->fileSystem->displayTree();
                    break;
                case 'help':
                    $this->displayHelp();
                    break;
                default:
                    echo "Unknown command: {$action}\n";
                    $this->displayHelp();
            }
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }

    private function resolvePath(string $path): string
    {
        if (strpos($path, '/') === 0) {
            // Absolute path
            return $path;
        } else {
            // Relative path
            return rtrim($this->currentPath, '/') . '/' . ltrim($path, '/');
        }
    }

    private function displayHelp(): void
    {
        echo <<<HELP
Virtual File System CLI Commands:
  mkdir <path>          Create a virtual folder at the specified path.
  rmdir <path>          Delete a virtual folder at the specified path.
  cp <local_path> <virtual_path>  Copy a local file to a virtual folder.
  rm <file_name> <virtual_path>   Remove a file from a virtual folder.
  ls [path]             List files in the current or specified folder.
  tree                  Display the virtual folder tree.
  help                  Display this help message.

Examples:
  mkdir /root/folder1
  cp /path/to/local/file.txt /root/folder1
  ls /path/to
  rm file.txt /path/to
  tree
  help

HELP;
    }

    public function getCurrentPath(): string
    {
        return $this->currentPath;
    }
}

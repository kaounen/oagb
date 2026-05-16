<?php
$dir = 'c:\xampp\htdocs\oagb\css';
$search = 'gestao/assets/uploads/files/';
$replace = 'uploads/';

$count = 0;
$files = scandir($dir);

foreach ($files as $file) {
    if ($file === '.' || $file === '..') continue;
    
    $path = $dir . DIRECTORY_SEPARATOR . $file;
    
    if (!is_dir($path)) {
        if (pathinfo($path, PATHINFO_EXTENSION) === 'css') {
            $content = file_get_contents($path);
            
            if (strpos($content, $search) !== false) {
                $newContent = str_replace($search, $replace, $content);
                file_put_contents($path, $newContent);
                echo "Atualizado CSS: " . $file . "\n";
                $count++;
            }
        }
    }
}
echo "Terminado! $count ficheiros CSS foram atualizados com sucesso.\n";

<?php
$dir = 'c:\xampp\htdocs\oagb';
$search = 'uploads/';
$replace = 'uploads/';

function processDirectory($dir, $search, $replace) {
    global $count;
    $files = scandir($dir);
    
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        
        // Exclude the old CodeIgniter folders so we don't mess with them or waste time
        if ($file === 'gestao' || $file === 'gestaoCODIGNITER') continue;
        
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        
        if (is_dir($path)) {
            processDirectory($path, $search, $replace);
        } else {
            if (pathinfo($path, PATHINFO_EXTENSION) === 'php') {
                $content = file_get_contents($path);
                
                if (strpos($content, $search) !== false) {
                    $newContent = str_replace($search, $replace, $content);
                    file_put_contents($path, $newContent);
                    echo "Atualizado: " . str_replace('c:\xampp\htdocs\oagb\\', '', $path) . "\n";
                    $count++;
                }
            }
        }
    }
}

$count = 0;
echo "Iniciando atualizacao...\n";
processDirectory($dir, $search, $replace);
echo "Terminado! $count ficheiros foram atualizados com sucesso.\n";

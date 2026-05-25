<?php
$dir = new RecursiveDirectoryIterator(__DIR__);
$iterator = new RecursiveIteratorIterator($dir);

foreach ($iterator as $file) {
    if ($file->isDir()) continue;
    $ext = pathinfo($file->getFilename(), PATHINFO_EXTENSION);
    $path = $file->getPathname();
    
    if ($ext === 'php' && strpos($path, 'database.php') === false && strpos($path, 'fix_paths.php') === false) {
        $content = file_get_contents($path);
        
        // HTML links and scripts
        $content = str_replace('href="/pararbazar/', 'href="<?= BASE_URL ?>', $content);
        $content = str_replace('src="/pararbazar/', 'src="<?= BASE_URL ?>', $content);
        
        // JS strings
        $content = str_replace("'/pararbazar/", "'<?= BASE_URL ?>", $content);
        $content = str_replace('"/pararbazar/', '"<?= BASE_URL ?>', $content);
        
        file_put_contents($path, $content);
    }
}

// Fix config/functions.php specifically
$func_file = __DIR__ . '/config/functions.php';
$func = file_get_contents($func_file);
$func = str_replace('header("Location: "<?= BASE_URL ?>"', 'header("Location: " . BASE_URL . "', $func); // fix incorrect replace
$func = str_replace('header("Location: /pararbazar/" . ltrim($path, \'/\'));', 'header("Location: " . BASE_URL . ltrim($path, \'/\'));', $func);
file_put_contents($func_file, $func);

echo "Paths fixed!\n";

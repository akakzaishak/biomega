<?php
$root = realpath(__DIR__ . '/../') . DIRECTORY_SEPARATOR;
$exclude = ['.git' . DIRECTORY_SEPARATOR, 'vendor' . DIRECTORY_SEPARATOR];
$scriptRel = 'scripts' . DIRECTORY_SEPARATOR . 'append_trailing_space.php';

$it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$count = 0;
foreach ($it as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    $rel = substr($path, strlen($root));
    // skip the script itself
    if (str_replace('\\','/', $rel) === str_replace('\\','/',$scriptRel)) continue;
    // skip excluded directories
    $skip = false;
    foreach ($exclude as $e) {
        if (strpos($rel, $e) === 0) { $skip = true; break; }
    }
    if ($skip) continue;

    // read file
    $contents = @file_get_contents($path);
    if ($contents === false) continue;
    // skip binary files (contain NUL)
    if (strpos($contents, "\0") !== false) continue;

    // append single space
    $new = $contents . ' ';
    if ($new !== $contents) {
        $w = @file_put_contents($path, $new);
        if ($w !== false) {
            echo "Appended: $rel\n";
            $count++;
        }
    }
}

echo "Done. Files modified: $count\n";

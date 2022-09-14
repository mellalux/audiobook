<?php

// Reads files and directories from the given directory.
function sdir( $path='.', $mask='*', $nocache=0 ){
    static $dir = array(); // cache result in memory
    if ( !isset($dir[$path]) || $nocache) {
        $dir[$path] = scandir($path);
    }
    foreach ($dir[$path] as $i=>$entry) {
        if ($entry!='.' && $entry!='..' ) {
            if (is_file($path . $entry) && fnmatch($mask, $entry)) {
                $sdir['file'][] = $entry;
            } else {
                if (is_dir($path . $entry)) {
                    $sdir['dir'][] = $entry;
                }
            }
        } 
    }
    return ($sdir);
}

$root = '/var/www/space/audiobooks/data/';
setlocale(LC_ALL, 'et_EE.UTF8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $root = ($_POST['root']) ? $_POST['root'] : './data/';
    $folder = ($_POST['folder']) ? $root . $_POST['folder'] : $root;
}

echo json_encode(sdir($folder, '*.mp3'));

?>
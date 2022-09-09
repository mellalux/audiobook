<?php

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

$root = '/var/www/space/audiobooks/';
setlocale(LC_ALL, 'et_EE.UTF8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') :
    $folder = $root . $_POST['folder'];
else:
    $folder = $root;
endif;

$logfile = file_put_contents('logs.txt', 'Sent folder: '. $folder . PHP_EOL, FILE_APPEND | LOCK_EX);

echo json_encode(sdir($folder, '*.mp3'));

?>
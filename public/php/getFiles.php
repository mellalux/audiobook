<?php

// Reads files and directories from the given directory.
function sdir( $path='.', $mask='*', $nocache=0 ){
    static $dir = []; // cache result in memory
    $sdir = [];
    $ftypes = preg_split("/\,/",$mask);
    $dir_exists = false;
    $file_exists = false;

    if (is_dir($path)) {

        if ( !isset($dir[$path]) || $nocache) {
            if (scandir($path)) {
                $dir[$path] = scandir($path);
            }
        }
        
        foreach ($dir[$path] as $i=>$entry) {
            if ($entry!='.' && $entry!='..' ) {
                if (is_file($path . $entry)) {
                    foreach ($ftypes as $j=>$type) {
                        if (fnmatch('*.'.$type, $entry)) {
                            $sdir['file'][] = $entry;
                        }
                    }
                } else {
                    if (is_dir($path . $entry)) {
                        $sdir['dir'][] = $entry;
                    }
                }
            } 
        }
        
        if (array_key_exists('dir', $sdir)) {
            $dir_exists = (count($sdir['dir']) > 0) ? true : false;
        }
        if (array_key_exists('file', $sdir)) {
            $file_exists = (count($sdir['file']) > 0) ? true : false;
        }

        if (!$dir_exists && !$file_exists) {
            $sdir['result'] = 0;
        } elseif ($dir_exists && $file_exists) {
            $sdir['result'] = 1;
        } elseif ($dir_exists) {
            $sdir['result'] = 2;
        } elseif ($file_exists)  {
            $sdir['result'] = 3;
        }

    } else {
        $sdir['result'] = -1;
    }

    return ($sdir);
}

setlocale(LC_ALL, 'et_EE.UTF8');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $root = ($_POST['root']) ? $_POST['root'] : './data/';
    $folder = ($_POST['folder']) ? $root . $_POST['folder'] : $root;
    $types = ($_POST['types']) ? $_POST['types'] : 'mp3';
}

echo json_encode(sdir($folder, $types));

?>
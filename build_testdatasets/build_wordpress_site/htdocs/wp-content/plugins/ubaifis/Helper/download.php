<?php

function download_attachment()
{
    $file_path = $_POST['filename'];
    $file_mime = $_POST['mime'];
    $data['file_path'] = file_exists($file_path);

    try{
        header('Pragma: public');   // required
        header('Expires: 0');       // no cache
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file_path)).' GMT');
        header('Cache-Control: private',false);
        header('Content-Type: '.$file_mime);
        header('Content-Disposition: attachment; filename="'.basename($file_path).'"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: '.filesize($file_path));    // provide file size
        header('Connection: close');
        set_time_limit(0);
        @readfile("$file_path") or die("File not found.");

    }catch(Exception $e)
    {
        $data['error'] = $e->getMessage() ." @ ". $e->getFile() .' - '. $e->getLine();
    }
    }
    echo json_encode($data);
    die();
}

 ?>

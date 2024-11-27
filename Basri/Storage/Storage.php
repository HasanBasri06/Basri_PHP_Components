<?php 

namespace Basri\Storage;

use Config;

class Storage {
    public static function putFile($file) {
        $name = Config::storage()['page'];

        if (!file_exists($name)) {
            mkdir($name);
        }

        $fileName = $name . '/' . $file['name'];
        move_uploaded_file($file['tmp_name'], $fileName);            
    } 

    
    public static function putFileAs($file, $storageName, $folder = '') {
        $name = Config::storage()['page'].'/'.$folder;

        if (!file_exists($name)) {
            mkdir($name, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = $name . '/' . $storageName.".".$extension;
        move_uploaded_file($file['tmp_name'], $fileName); 
    } 
}
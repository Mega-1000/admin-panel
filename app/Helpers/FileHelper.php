<?php

namespace App\Helpers;

use Illuminate\Http\File;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class FileHelper {
    
    public static function createUploadedFileFromBase64(string $base64) {
        $fileData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
        $tmpFilePath = sys_get_temp_dir() . '/' . Str::uuid()->toString();
        file_put_contents($tmpFilePath, $fileData);
        $tmpFile = new File($tmpFilePath);
        $ext = $tmpFile->extension();
        $file = new UploadedFile($tmpFile->getPathname(), $tmpFile->getFilename().'.'.$ext, $tmpFile->getMimeType());
        return $file;
    }
}
<?php

namespace App\Traits;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

trait UploadFile
{

    public function uploadFile($file,$directory)
    {
        $attachment = [];
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
//        $file->getSize()
        $fileNameGenerator = md5($filename . csrf_token() . Carbon::now()->toDateTimeString() . rand(1, 100)) . '.' . $extension;

        Storage::disk('public')->put('uploads/'.$directory.'/'.$fileNameGenerator, file_get_contents($file));
        $attachment['path'] = 'uploads/'.$directory.'/'.$fileNameGenerator;
        $attachment['file_name'] = $filename;
        return $attachment;
    }

    public function deleteFile($path)
    {
        Storage::disk('public')->delete($path);
    }

    public function uploadBase64File($file , $base64File,$directory )
    {
        $attachment = [];
//        $allowedfileExtension=['pdf','jpg','png','docx'];
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
//        $file->getSize()
        $fileNameGenerator = md5($filename . csrf_token() . Carbon::now()->toDateTimeString() . rand(1, 100)) . '.' . $extension;
//        $extension = $file->getClientOriginalExtension();
//        $check=in_array($extension,$allowedfileExtension);
        $base64File = str_replace(' ', '+', $base64File);
        Storage::disk('public')->put('uploads/'.$directory.'/'.$fileNameGenerator, base64_decode($base64File));
        $attachment [] = 'uploads/'.$directory.'/'.$fileNameGenerator;
        $attachment [] = $filename;
        return $attachment;
    }

    public function convertFileToBase64($file)
    {
        $img = Image::make($file)->resize(320, 240)->encode('data-url');
        $uploadingFile = file_get_contents($img);
        return base64_encode($uploadingFile);
    }

    public function createBase64File($base64File , $path)
    {
        $base64File = str_replace(' ', '+', $base64File);
        Storage::disk('public')->put($path, base64_decode($base64File));
        $attachment [] = $path;
        return $attachment;
    }

}

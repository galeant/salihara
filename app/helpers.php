<?php

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Intervention\Image\ImageManagerStatic as ImageManipulation;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;


function imageUpload($folder_upload, $image, $resize = null, $name = null)
{
    if (str_contains($image, url('/'))) {
        return str_replace(url('/') . '/', '', $image);
    }
    $img = ImageManipulation::make($image);
    $type = str_replace('image/', '.', $img->mime());
    $image_name = $folder_upload . (string) Str::uuid() . $type;
    if ($name !== null) {
        $image_name = $folder_upload . $name . $type;
    }

    if (Storage::exists($folder_upload)) {
        Storage::makeDirectory($folder_upload);
    }
    if ($resize !== null) {
        if ($resize['width'] !== null) {
            $img->resize($resize['width'], null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else if ($resize['height'] !== null) {
            $img->resize(null, $resize['height'], function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $img->resize($resize['width'], $resize['height']);
        }
    }

    Storage::put($image_name, $img->encode());
    $image_name = str_replace('public/', '', $image_name);
    return 'storage/' . $image_name;
}

function tokenize($payload)
{
    $key = ENV('JWT_SECRET');
    return JWT::encode($payload, $key, 'HS256');
}


function parseTokenize($token)
{
    $key = ENV('JWT_SECRET');
    return (array)JWT::decode($token, new Key($key, 'HS256'));
}

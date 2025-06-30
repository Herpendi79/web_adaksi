<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    public function uploadFile($file, $path, $oldFile = null)
    {
        if ($oldFile) {
            $this->deleteFile($oldFile);
        }

        $fileName = time() . '.' . $file->getClientOriginalExtension();
        $file->move($path, $fileName);

        return $fileName;
    }

    public function deleteFile($filePath)
    {
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    public function getCreds()
    {
        $user = Auth::user();
        if ($user) {
            return $user;
        }
        return null;
    }
}

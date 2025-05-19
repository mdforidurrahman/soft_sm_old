<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = Storage::disk('public')->put('project_files', $file);

            return response()->json(['success' => true, 'name' => $path]);
        }

        return response()->json(['success' => false]);
    }
}

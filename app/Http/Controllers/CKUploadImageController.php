<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CKUploadImageController extends Controller
{
     public function uploadImage(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|max:2048',
        ]);

        $path = $request->file('upload')->store('uploads', 'public');
        $url = asset('storage/' . $path);

        return response()->json([
            'url' => $url
        ]);
    }
}

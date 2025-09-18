<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CKEditorController extends Controller
{
    // CKEditor 4 upload (enhanced for all upload types)
    public function upload(Request $request)
    {
        try {
            // Handle different upload methods
            $file = null;
            
            if ($request->hasFile('upload')) {
                $file = $request->file('upload');
            } elseif ($request->hasFile('file')) {
                $file = $request->file('file');
            } else {
                $files = $request->allFiles();
                if (!empty($files)) {
                    $file = reset($files);
                }
            }

            if (!$file) {
                return response()->json(['uploaded' => false, 'error' => ['message' => 'No file uploaded']]);
            }

            $request->validate([
                'upload' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
                'file'   => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:5120',
            ]);

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $directory = 'storage/ckeditor';

            if (!is_dir(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }

            $file->move(public_path($directory), $filename);
            $url = url('public/'. $directory . '/' . $filename);
            
            if ($request->has('CKEditorFuncNum')) {
                $funcNum = $request->input('CKEditorFuncNum');
                return response("<script>window.parent.CKEDITOR.tools.callFunction($funcNum, '$url', '');</script>")
                    ->header('Content-Type', 'text/html; charset=utf-8');
            }
            
            return response()->json([
                'uploaded' => true,
                'fileName' => $filename,
                'url'      => $url
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => $e->getMessage()
                ]
            ]);
        }
    }

    // TinyMCE upload
    public function tinymceUpload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_tinymce_' . $file->getClientOriginalName();
            $directory = 'tinymce';
            
            if (!is_dir(public_path($directory))) {
                mkdir(public_path($directory), 0755, true);
            }
            
            $file->move(public_path($directory), $filename);
            $url = url($directory . '/' . $filename);
            
            return response()->json([
                'location' => $url
            ]);
        }
        
        return response()->json([
            'error' => 'File upload failed.'
        ], 400);
    }
}

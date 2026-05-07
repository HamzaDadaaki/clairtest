<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MediaUploadController extends Controller
{
    public function productImage(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:10240'],
        ]);

        return response()->json([
            'path' => $this->storeUploadedFile($data['file'], 'assets/images/products/uploads', 'product-image'),
        ]);
    }

    public function partnerLogo(Request $request): JsonResponse
    {
        $data = $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,svg', 'max:10240'],
        ]);

        return response()->json([
            'path' => $this->storeUploadedFile($data['file'], 'assets/images/partners/uploads', 'partner-logo'),
        ]);
    }

    protected function storeUploadedFile($file, string $relativeDirectory, string $fallbackName): string
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'webp');
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $safeName = $safeName !== '' ? $safeName : $fallbackName;
        $filename = now()->format('YmdHis').'-'.Str::random(8).'-'.$safeName.'.'.$extension;

        $destination = public_path($relativeDirectory);

        if (! is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $file->move($destination, $filename);

        return trim($relativeDirectory, '/').'/'.$filename;
    }
}

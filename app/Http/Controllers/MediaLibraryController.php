<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Support\MediaLibraryCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MediaLibraryController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $user = $request->user();
        abort_unless($user, Response::HTTP_UNAUTHORIZED);

        $search = trim((string) $request->query('search', ''));
        $items = MediaLibraryCatalog::toListItems(MediaLibraryCatalog::visibleFor($user));
        if ($search !== '') {
            $items = MediaLibraryCatalog::filterItemsByQuery($items, $search);
        }

        return response()->json([
            'success' => true,
            'media' => $items,
        ]);
    }

    public function upload(Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        abort_unless($user, Response::HTTP_UNAUTHORIZED);

        $single = $request->file('file');
        $many = $request->file('files', []);
        if ($single !== null && $many === []) {
            $many = [$single];
        }
        if (! is_array($many) || count($many) === 0) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please select at least one image.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            return back()->withErrors(['files' => 'Please select at least one image.']);
        }

        foreach ($many as $f) {
            if (! $f || ! str_starts_with((string) $f->getMimeType(), 'image/')) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Only images are allowed.',
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                return back()->withErrors(['files' => 'Only images are allowed.']);
            }
            if ((int) $f->getSize() > 5 * 1024 * 1024) {
                if ($request->expectsJson() || $request->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Each image must be 5MB or smaller.',
                    ], Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                return back()->withErrors(['files' => 'Each image must be 5MB or smaller.']);
            }
        }

        $dir = MediaLibraryCatalog::mediaDir();
        if (! is_dir($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        $uploadedCount = 0;

        foreach ($many as $file) {
            $name = Str::slug(pathinfo((string) $file->getClientOriginalName(), PATHINFO_FILENAME));
            $ext = strtolower((string) $file->getClientOriginalExtension());
            $size = (int) $file->getSize();
            $filename = ($name !== '' ? $name : 'media').'-'.Str::random(6).'-'.time().'.'.$ext;

            $file->move($dir, $filename);

            Media::query()->create([
                'filename' => $filename,
                'original_name' => (string) $file->getClientOriginalName(),
                'file_path' => MediaLibraryCatalog::mediaPathPrefix().$filename,
                'file_type' => $ext,
                'file_size' => $size,
                'uploaded_by' => $user->id,
            ]);
            $uploadedCount++;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $uploadedCount.' media file(s) uploaded successfully.',
            ], Response::HTTP_CREATED);
        }

        return back()->with('status', $uploadedCount.' media file(s) uploaded successfully.');
    }
}

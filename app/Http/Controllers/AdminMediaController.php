<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Support\MediaLibraryCatalog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class AdminMediaController extends Controller
{
    /**
     * @return array<int, array{id: int, name: string, path: string, url: string, size: int, modified_at: int}>
     */
    protected function mediaItems(Request $request): array
    {
        $user = $request->user();
        abort_unless($user?->can('media.manage'), Response::HTTP_FORBIDDEN);

        return MediaLibraryCatalog::toListItems(MediaLibraryCatalog::visibleFor($user));
    }

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $items = $this->mediaItems($request);
        if ($query !== '') {
            $items = MediaLibraryCatalog::filterItemsByQuery($items, $query);
        }

        return view('admin.media.index', [
            'title' => __('Media library'),
            'items' => $items,
            'query' => $query,
        ]);
    }

    public function destroy(Request $request, Media $media): RedirectResponse|JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->can('media.manage'), Response::HTTP_FORBIDDEN);

        if (! MediaLibraryCatalog::visibleFor($user)->whereKey($media->id)->exists()) {
            abort(Response::HTTP_FORBIDDEN);
        }

        $fullPath = public_path($media->file_path);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
        $media->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Media deleted successfully.',
            ]);
        }

        return redirect()->route('admin.media.index')->with('status', 'Media deleted successfully.');
    }

    public function bulkDestroy(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user?->can('media.manage'), Response::HTTP_FORBIDDEN);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer'],
        ]);

        $allowedIds = MediaLibraryCatalog::visibleFor($user)->whereIn('id', $data['ids'])->pluck('id')->all();
        $items = Media::query()->whereIn('id', $allowedIds)->get();
        foreach ($items as $item) {
            $fullPath = public_path($item->file_path);
            if (is_file($fullPath)) {
                @unlink($fullPath);
            }
            $item->delete();
        }

        return redirect()->route('admin.media.index')->with('status', 'Selected media deleted successfully.');
    }
}

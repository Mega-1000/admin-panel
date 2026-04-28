<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileManagerController extends Controller
{
    private string $basePath;
    private string $baseUrl;

    public function __construct()
    {
        $this->basePath = public_path('uploads/file-manager');
        $this->baseUrl  = url('uploads/file-manager');
    }

    public function index(): View
    {
        return view('file-manager.index');
    }

    public function list(Request $request): JsonResponse
    {
        $rel  = $this->sanitizePath($request->input('path', ''));
        $full = $this->basePath . ($rel ? DIRECTORY_SEPARATOR . $rel : '');

        if (!is_dir($full)) {
            return response()->json(['error' => 'Folder nie istnieje.'], 404);
        }

        $items = [];
        foreach (scandir($full) as $entry) {
            if ($entry === '.' || $entry === '..') continue;

            $entryFull = $full . DIRECTORY_SEPARATOR . $entry;
            $entryRel  = $rel ? $rel . '/' . $entry : $entry;
            $isDir     = is_dir($entryFull);

            $items[] = [
                'name'     => $entry,
                'path'     => $entryRel,
                'is_dir'   => $isDir,
                'size'     => $isDir ? null : filesize($entryFull),
                'modified' => filemtime($entryFull),
                'url'      => $isDir ? null : $this->baseUrl . '/' . str_replace('\\', '/', $entryRel),
                'ext'      => $isDir ? null : strtolower(pathinfo($entry, PATHINFO_EXTENSION)),
            ];
        }

        usort($items, fn($a, $b) => $b['is_dir'] <=> $a['is_dir'] ?: strcmp($a['name'], $b['name']));

        $favorites = $this->getFavoritesList();

        return response()->json(['items' => $items, 'favorites' => $favorites]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate(['files.*' => 'required|file|max:20480']);

        $rel  = $this->sanitizePath($request->input('path', ''));
        $full = $this->basePath . ($rel ? DIRECTORY_SEPARATOR . $rel : '');

        if (!is_dir($full)) {
            @mkdir($full, 0775, true);
        }

        $uploaded = [];
        foreach ($request->file('files', []) as $file) {
            $name = $this->uniqueName($full, $file->getClientOriginalName());
            $file->move($full, $name);
            $fileRel  = $rel ? $rel . '/' . $name : $name;
            $uploaded[] = [
                'name' => $name,
                'path' => $fileRel,
                'url'  => $this->baseUrl . '/' . str_replace('\\', '/', $fileRel),
                'ext'  => strtolower(pathinfo($name, PATHINFO_EXTENSION)),
            ];
        }

        return response()->json(['uploaded' => $uploaded]);
    }

    public function createFolder(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:100|regex:/^[^\/\\\\<>:"|?*]+$/']);

        $rel  = $this->sanitizePath($request->input('path', ''));
        $name = trim($request->input('name'));
        $full = $this->basePath . ($rel ? DIRECTORY_SEPARATOR . $rel : '') . DIRECTORY_SEPARATOR . $name;

        if (is_dir($full)) {
            return response()->json(['error' => 'Folder już istnieje.'], 409);
        }

        if (!@mkdir($full, 0775, true)) {
            return response()->json(['error' => 'Nie można utworzyć folderu.'], 500);
        }

        return response()->json(['created' => true, 'name' => $name]);
    }

    public function delete(Request $request): JsonResponse
    {
        $rel  = $this->sanitizePath($request->input('path', ''));
        if (!$rel) {
            return response()->json(['error' => 'Nieprawidłowa ścieżka.'], 422);
        }

        $full = $this->basePath . DIRECTORY_SEPARATOR . $rel;

        if (!file_exists($full) && !is_dir($full)) {
            return response()->json(['error' => 'Nie znaleziono pliku/folderu.'], 404);
        }

        if (is_dir($full)) {
            $this->deleteDirectory($full);
        } else {
            unlink($full);
        }

        return response()->json(['deleted' => true]);
    }

    public function favorites(): JsonResponse
    {
        return response()->json($this->getFavoritesList());
    }

    public function toggleFavorite(Request $request): JsonResponse
    {
        $request->validate(['path' => 'required|string']);
        $path   = $this->sanitizePath($request->input('path'));
        $userId = Auth::id();

        $existing = \DB::table('file_manager_favorites')
            ->where('user_id', $userId)
            ->where('path', $path)
            ->first();

        if ($existing) {
            \DB::table('file_manager_favorites')
                ->where('id', $existing->id)
                ->delete();
            return response()->json(['favorited' => false]);
        }

        \DB::table('file_manager_favorites')->insert([
            'user_id'    => $userId,
            'path'       => $path,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['favorited' => true]);
    }

    // ── helpers ─────────────────────────────────────────────────────────────

    private function sanitizePath(string $path): string
    {
        // Strip leading slash, resolve .., keep only safe chars
        $path = str_replace('\\', '/', $path);
        $path = trim($path, '/');
        $parts = array_filter(explode('/', $path), fn($p) => $p !== '' && $p !== '..' && $p !== '.');
        return implode('/', $parts);
    }

    private function uniqueName(string $dir, string $original): string
    {
        $name = pathinfo($original, PATHINFO_FILENAME);
        $ext  = pathinfo($original, PATHINFO_EXTENSION);
        $candidate = $original;
        $i = 1;
        while (file_exists($dir . DIRECTORY_SEPARATOR . $candidate)) {
            $candidate = $name . '_' . $i . ($ext ? '.' . $ext : '');
            $i++;
        }
        return $candidate;
    }

    private function deleteDirectory(string $dir): void
    {
        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }

    private function getFavoritesList(): array
    {
        return \DB::table('file_manager_favorites')
            ->where('user_id', Auth::id())
            ->pluck('path')
            ->toArray();
    }
}

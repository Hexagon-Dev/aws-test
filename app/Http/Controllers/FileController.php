<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ArchiveServiceInterface;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    protected ArchiveServiceInterface $service;

    /**
     * @param ArchiveServiceInterface $archiveService
     */
    public function __construct(ArchiveServiceInterface $archiveService)
    {
        $this->service = $archiveService;
    }

    public function showAll(): string
    {
        return File::all()->toJson();
    }

    public function saveFile(Request $request, string $path = null): string
    {
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();
        $path = is_null($path) ? '' : $path;

        Storage::disk('minio')->putFileAs($path, $file, $filename);

        $data = [
            'name' => $filename,
            'size' => $file->getSize(),
            'path' => $path,
        ];

        if (File::query()->insert($data)) {
            return response()->json(['message' => 'file successfully loaded'], Response::HTTP_OK);
        }

        return response()->json(['error' => 'con not load file'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function downloadFile(string $filename)
    {
        $filepath = $filename;
        $filename = str_contains($filename, '/') ? last(explode('/', $filename)) : $filename;

        File::query()->where('name', $filename)->orWhere('path', $filepath)->firstOrFail();
        if (Storage::disk('minio')->exists($filepath)) {
            return $this->service->archive($filepath);
            //return Storage::disk('minio')->response($filename);
        }

        return false;
    }
}

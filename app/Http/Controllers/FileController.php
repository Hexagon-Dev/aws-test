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

    public function saveFile(Request $request): string
    {
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        Storage::disk('minio')->putFileAs('files', $file, $file->getClientOriginalName());

        $data = [
            'name' => $filename,
            'size' => $file->getSize(),
        ];

        if (File::query()->insert($data)) {
            return response()->json(['message' => 'file successfully loaded'], Response::HTTP_OK);
        }

        return response()->json(['error' => 'con not load file'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function downloadFile(String $filename)
    {
        File::query()->where('name', $filename)->firstOrFail();

        if (Storage::disk('minio')->exists('files/' . $filename)) {
            return $this->service->archive($filename);
            //return Storage::disk('minio')->response('files/' . $filename);
        }

        return false;
    }
}

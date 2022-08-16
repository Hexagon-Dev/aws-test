<?php

namespace App\Http\Controllers;

use App\Contracts\Services\ArchiveServiceInterface;
use App\Models\File;
use Facade\FlareClient\Http\Exceptions\NotFound;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

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

    public function showAll(): array
    {
        return Storage::cloud()->allFiles();
    }

    public function saveFile(Request $request, string $path = ''): JsonResponse
    {
        $file = $request->file('file');
        $filename = $file->getClientOriginalName();

        Storage::disk('minio')->putFileAs($path, $file, $filename);

        $data = [
            'name' => $filename,
            'size' => $file->getSize(),
            'path' => $path,
        ];

        if (File::query()->insert($data)) {
            return response()->json(['message' => 'File successfully uploaded,'], Response::HTTP_OK);
        }

        return response()->json(['error' => 'Can not upload file.'], Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws Throwable
     */
    public function downloadFile(string $filename)
    {
        if (Storage::cloud()->exists($filename)) {
            return $this->service->archive($filename);
        }

        throw new NotFound();
    }
}

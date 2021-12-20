<?php

namespace App\Services;

use App\Contracts\Services\ArchiveServiceInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream;

class ArchiveService implements ArchiveServiceInterface
{
    public function archive(string $filename): StreamedResponse
    {
        return response()->stream(function () use ($filename) {
            $minio_file = Storage::disk('minio')->response('files/' . $filename);

            $options = new ZipStream\Option\Archive();
            $options->setSendHttpHeaders(true);

            $zip = new ZipStream\ZipStream('result.zip', $options);

            $zip->addFile($filename, $minio_file);
            $zip->finish();
        });
    }
}

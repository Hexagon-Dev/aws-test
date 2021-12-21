<?php

namespace App\Services;

use App\Contracts\Services\ArchiveServiceInterface;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use ZipStream;
use ZipStream\Option\Archive;

class ArchiveService implements ArchiveServiceInterface
{

    public function archive(string $filename): StreamedResponse
    {
        $files = Storage::disk('minio')->allFiles($filename);

        if (empty($files)) {
            $files = [$filename];
        }

        return new StreamedResponse(function() use ($files)
        {
            $options = new Archive();
            $options->setSendHttpHeaders(true);

            $zip = new ZipStream\ZipStream('result_' . date('Y-m-d_H-i-s') . '.zip', $options);

            foreach ($files as $file) {
                $zip->addFile(basename($file), Storage::disk('minio')->get($file));
            }

            $zip->finish();
        });
    }
}

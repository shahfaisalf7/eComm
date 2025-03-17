<?php

namespace Modules\Media\Http\Controllers\Admin;

use Illuminate\Http\Response;
use Modules\Media\Entities\File;
use Illuminate\Support\Facades\Storage;
use Modules\Admin\Traits\HasCrudActions;
use Modules\Media\Http\Requests\UploadMediaRequest;

class MediaController
{
    use HasCrudActions;

    protected $model = File::class;
    protected $label = 'media::media.media';
    protected $viewPath = 'media::admin.media';

    public function store(UploadMediaRequest $request)
    {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName(); // e.g., "My Image!.jpg"
        $baseName = pathinfo($originalName, PATHINFO_FILENAME); // e.g., "My Image!"
        $extension = $file->guessClientExtension() ?? $file->getClientOriginalExtension(); // e.g., "jpg"

        // Clean the base name
        $cleanBaseName = preg_replace('/[^a-z0-9-]/', '', str_replace(' ', '-', strtolower($baseName))); // e.g., "my-image"
        $filename = "{$cleanBaseName}.{$extension}"; // e.g., "my-image.jpg"
        $disk = config('filesystems.default');
        $path = "media/{$filename}";

        // Check for duplicates
        $counter = 1;
        while (Storage::disk($disk)->exists($path)) {
            $filename = "{$cleanBaseName}-{$counter}.{$extension}"; // e.g., "my-image-1.jpg"
            $path = "media/{$filename}";
            $counter++;
        }

        // Save to storage with cleaned name
        Storage::disk($disk)->putFileAs('media', $file, $filename);

        // Database entry with cleaned name everywhere
        return File::create([
            'user_id' => auth()->id(),
            'disk' => $disk,
            'filename' => $filename, // Cleaned: "my-image.jpg" or "my-image-1.jpg"
            'path' => $path,        // Cleaned: "media/my-image.jpg" or "media/my-image-1.jpg"
            'extension' => $extension,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }

    public function destroy(string $ids)
    {
        File::find(explode(',', $ids))->each->delete();
    }
}

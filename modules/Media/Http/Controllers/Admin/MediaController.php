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

    /**
     * Model for the resource.
     *
     * @var string
     */
    protected $model = File::class;

    /**
     * Label of the resource.
     *
     * @var string
     */
    protected $label = 'media::media.media';

    /**
     * View path of the resource.
     *
     * @var string
     */
    protected $viewPath = 'media::admin.media';


    /**
     * Store a newly created media in storage.
     *
     * @param UploadMediaRequest $request
     *
     * @return Response
     */
    // PREVIOUS FUNCTION
//    public function store(UploadMediaRequest $request)
//    {
//        $file = $request->file('file');
//        $path = Storage::putFile('media', $file);
//
//        return File::create([
//            'user_id' => auth()->id(),
//            'disk' => config('filesystems.default'),
//            'filename' => substr($file->getClientOriginalName(), 0, 255),
//            'path' => $path,
//            'extension' => $file->guessClientExtension() ?? '',
//            'mime' => $file->getClientMimeType(),
//            'size' => $file->getSize(),
//        ]);
//    }

// WORKING NEW FUNCTION
//    public function store(UploadMediaRequest $request)
//    {
//        $file = $request->file('file');
//        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME); // e.g., "membership-facilities"
//        $extension = $file->guessClientExtension() ?? $file->getClientOriginalExtension(); // e.g., "jpg"
//        $uniqueName = $originalName . '-' . time() . '.' . $extension; // e.g., "membership-facilities-1677654321.jpg"
//        $path = Storage::putFileAs('media', $file, $uniqueName, 'public');
//
//        return File::create([
//            'user_id' => auth()->id(),
//            'disk' => config('filesystems.default'),
//            'filename' => substr($file->getClientOriginalName(), 0, 255),
//            'path' => $path,
//            'extension' => $extension,
//            'mime' => $file->getClientMimeType(),
//            'size' => $file->getSize(),
//        ]);
//    }




    public function store(UploadMediaRequest $request)
    {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName(); // e.g., "ddcLogo.jpg"
        $baseName = pathinfo($originalName, PATHINFO_FILENAME); // e.g., "ddcLogo"
        $extension = $file->guessClientExtension() ?? $file->getClientOriginalExtension(); // e.g., "jpg"
        $filename = $originalName;
        $disk = config('filesystems.default'); // e.g., "public_storage"
        $path = "media/{$filename}";

        // Check for duplicates and append a number if needed
        $counter = 1;
        while (Storage::disk($disk)->exists($path)) {
            $filename = "{$baseName}-{$counter}.{$extension}"; // e.g., "ddcLogo-1.jpg"
            $path = "media/{$filename}";
            $counter++;
        }

        // Save the file using the default disk
        Storage::disk($disk)->putFileAs('media', $file, $filename);

        // Create database entry
        return File::create([
            'user_id' => auth()->id(),
            'disk' => $disk,
            'filename' => substr($originalName, 0, 255),
            'path' => $path,
            'extension' => $extension,
            'mime' => $file->getClientMimeType(),
            'size' => $file->getSize(),
        ]);
    }


    /**
     * Remove the specified resources from storage.
     *
     * @param string $ids
     *
     * @return Response
     */
    public function destroy(string $ids)
    {
        File::find(explode(',', $ids))->each->delete();
    }
}

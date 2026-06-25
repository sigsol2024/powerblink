<?php

namespace App\Services;

use App\Models\Media;
use App\Models\PlayerDocument;
use App\Models\Registration;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class RegistrationDocumentService
{
    /**
     * @return array<string, int>
     */
    public function storeWizardUploads(array $files): array
    {
        $ids = [];
        $map = [
            'profile_photo' => 'passport_photo',
            'birth_certificate' => 'birth_certificate',
            'medical_clearance' => 'medical_clearance',
        ];

        foreach ($map as $input => $type) {
            $file = $files[$input] ?? null;
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $media = $this->persistUpload($file, $type);
            $ids[$type] = $media->id;
        }

        return $ids;
    }

    /**
     * @param  array<string, int>  $documentMediaIds
     */
    public function attachToRegistration(Registration $registration, array $documentMediaIds): void
    {
        if (isset($documentMediaIds['passport_photo'])) {
            $registration->update(['profile_photo_media_id' => $documentMediaIds['passport_photo']]);
        }

        foreach (['birth_certificate', 'medical_clearance'] as $type) {
            if (! isset($documentMediaIds[$type])) {
                continue;
            }

            PlayerDocument::query()->create([
                'registration_id' => $registration->id,
                'document_type' => $type,
                'media_id' => $documentMediaIds[$type],
                'status' => 'pending',
            ]);
        }
    }

    private function persistUpload(UploadedFile $file, string $type): Media
    {
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = Str::slug($type).'-'.Str::uuid().'.'.$extension;
        $relativeDir = 'asset/images/powerblink/documents';
        $absoluteDir = public_path($relativeDir);

        if (! is_dir($absoluteDir)) {
            File::makeDirectory($absoluteDir, 0755, true);
        }

        $file->move($absoluteDir, $filename);
        $relativePath = $relativeDir.'/'.$filename;
        $originalName = $file->getClientOriginalName();

        return Media::query()->create([
            'filename' => $filename,
            'original_name' => $originalName,
            'file_path' => $relativePath,
            'file_type' => $extension,
            'file_size' => (int) filesize(public_path($relativePath)),
            'category' => 'registration_'.$type,
        ]);
    }
}

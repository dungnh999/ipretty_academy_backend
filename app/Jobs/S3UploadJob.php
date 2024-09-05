<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;


class S3UploadJob implements ShouldQueue
{
    protected $filename;
    protected $folder;
    protected $s3;

    public function __construct($filename, $folder)
    {
      $this->filename = $filename;
      $this->folder = $folder;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      $disk = Storage::disk('s3');
      $key = $this->folder . $this->filename;
      $tempFilePath = storage_path('app/temp/' . $this->filename);
      try {
        // Thực hiện upload tệp tin lên S3
        $disk->put($key, file_get_contents($tempFilePath),  'public');

        // Xoá tệp tin tạm thời sau khi upload
        unlink($tempFilePath);

        // Log thông báo upload thành công hoặc trả về kết quả
        \Log::info('File uploaded successfully to S3.');
      } catch (\Exception $e) {
        \Log::error('Error uploading file to S3: ' . $e->getMessage());
      }
    }
}

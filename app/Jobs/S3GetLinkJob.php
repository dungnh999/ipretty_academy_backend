<?php

namespace App\Jobs;

use App\Http\Controllers\AppBaseController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Storage;

class S3GetLinkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $filename;
    protected $folder;
    protected $type;

    public function __construct($filename, $folder , $type)
    {
      $this->filename = $filename;
      $this->folder = $folder;
      $this->type = $type;
    }

    public function handle()
    {
      $folder = $this->handleFolder();
      dispatch(new S3UploadJob($this->filename, $folder));
    }

    public function handleFolder()
    {
      return $this->folder;
    }
}

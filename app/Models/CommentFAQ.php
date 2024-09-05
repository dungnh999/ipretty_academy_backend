<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
class CommentFAQ extends Model
{
    public $table = 'comments_faq';
    public $fillable = [
        'id',
        'commentator_id',
        'comment',
        'question_id',
        'comment_type',
        'file_name',
        'file_url',
        'parent_id',
        'created_at',
        'updated_at',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('file_url')
            ->singleFile(); // !!! ONLY use singleFile() for single file in media collection;
    }

    // remember to save before run this method
    public function handleMedia($request = null): void
    {
        if ($request == null) {
            return;
        }

        // Store Image
        if ($request->hasFile('file_url') && $request->file('file_url')->isValid()) {
            $this->addMediaFromRequest('file_url')->toMediaCollection('file_url');
            $this->file_url = $this->getFirstMediaUrl('file_url');
            $this->save(); //remember to save again
        } else {
            // TODO: throw exception
        }
    }
    
    public function child_comments()
    {
        return $this->hasMany('App\Models\CommentFAQ', 'parent_id')->with('comment_by', function ($q) {
            $q->select('id', 'name', 'email', 'avatar');
        });
    }

    public function comment_by()
    {
        return $this->belongsTo('App\Models\Users', 'commentator_id');
    }
}


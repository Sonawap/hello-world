<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    public function category(){
        return $this->belongsTo(BlogCategory::class, 'blog_categories_id', 'id');
    }

    public function getTagsAttribute($tags){
        return json_decode($tags);
    }
}

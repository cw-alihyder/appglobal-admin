<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\MediaCollections\Models\Media;



class BlogArticle extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'blog_articles'; // Prisma default is PascalCase

    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'title',
        'slug',
        'excerpt',
        'thumbnail',
        'content',
        'image',
        'date',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords'
    ];



    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_article_tags', 'article_id', 'tag_id');
    }
}

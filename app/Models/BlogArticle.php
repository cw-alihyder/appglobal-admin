<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogArticle extends Model
{
    use SoftDeletes;

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
        'createdAt',
        'updatedAt',
        'categoryId',
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'categoryId');
    }

    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'BlogArticleTag', 'articleId', 'tagId');
    }
}

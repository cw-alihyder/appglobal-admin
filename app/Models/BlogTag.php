<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class BlogTag extends Model
{
    protected $table = 'blog_tags'; // Prisma default is PascalCase

     protected $fillable = [
        'id',
        'name',
    ];

    public function articles()
    {
        return $this->belongsToMany(BlogArticle::class, 'blog_article_tags', 'tag_id', 'article_id');
    }
}

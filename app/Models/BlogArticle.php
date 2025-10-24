<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogArticle extends Model
{
    use SoftDeletes;

    protected $table = 'BlogArticle'; // Prisma default is PascalCase

    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BlogTag extends Model
{
    use SoftDeletes;

    protected $table = 'BlogTag'; // Prisma default is PascalCase

    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';

     protected $fillable = [
        'id',
        'name',
    ];

    public function articles()
    {
        return $this->belongsToMany(BlogArticle::class, 'BlogArticleTag', 'tagId', 'articleId');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BlogCategory extends Model
{
    protected $table = 'blog_categories'; // Prisma default is PascalCase


    protected $fillable = [
        'name',
        'slug'
    ];
}

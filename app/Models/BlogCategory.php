<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class BlogCategory extends Model
{
    use SoftDeletes;

    protected $table = 'BlogCategory'; // Prisma default is PascalCase

    protected $primaryKey = 'id';

    public $timestamps = true;

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = 'updatedAt';
    const DELETED_AT = 'deletedAt';


    protected $fillable = [
        'name',
        'slug'
    ];
}

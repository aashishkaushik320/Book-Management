<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'author',
        'cover_image',
        'price',
        'published_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'published_date' => 'date',
    ];
}

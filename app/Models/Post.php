<?php

namespace App\Models;

use App\Traits\BelongsToUser;
use App\Traits\HasComments;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use BelongsToUser, HasComments, HasFactory;

    protected $fillable = [
        'title',
        'content',
    ];
}

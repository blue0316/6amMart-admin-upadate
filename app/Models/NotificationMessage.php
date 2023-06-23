<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationMessage extends Model
{
    use HasFactory;

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translationable');
    }
}

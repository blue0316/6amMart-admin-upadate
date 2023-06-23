<?php

namespace App\Models;

use App\Scopes\StoreScope;
use Illuminate\Database\Eloquent\Model;

class MailConfig extends Model
{
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    protected static function booted()
    {
        if(auth('vendor')->check())
        {
            static::addGlobalScope(new StoreScope);
        } 
    }
}

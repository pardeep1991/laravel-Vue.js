<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = [
        'keyword_id',
        'search_engine',
        'title',
        'description',
        'url',
        'url_redirected',
        'url_category',
        'url_status',
        'ip',
        'ip_region'
    ];

    public function keyword() {
        return $this->belongsTo('App\Keyword');
    }

}

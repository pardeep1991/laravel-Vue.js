<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = [
        'keyword_id',
        'keyword_result_number',
        'search_engines',
        'status'
    ];

    protected $casts = [
        'search_engines' => 'array'
    ];

    public function keyword() {
        return $this->belongsTo('App\Keyword');
    }
}

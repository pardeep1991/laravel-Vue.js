<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    protected $fillable = [
        'keyword',
        'keyword_category',
        'baidu_crawler_exec_date',
        'bing_crawler_exec_date',
        '360_crawler_exec_date'
    ];
}

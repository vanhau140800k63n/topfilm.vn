<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $table = 'news';

    public static function rules()
    {
        return [
            'title' => 'required'
        ];
    }

    public static function rulesNoti()
    {
        return [
            'title.required' => 'Vui lòng nhập tiêu đề',
        ];
    }
}

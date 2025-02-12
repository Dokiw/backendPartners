<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    // Задаём имя таблицы, если оно не соответствует стандартному формату.
    protected $table = 'каталог';

    // Отключаем автоматическое ведение timestamps, если они не нужны.
    public $timestamps = false;

    // Разрешённые для массового заполнения поля.
    protected $fillable = [
        'name',
        'characters_valid',
        'size_visible',
        'sub_name'
    ];

    // Приведение типов: автоматически преобразуем JSON-поле в массив и обратно.
    protected $casts = [
        'characters_valid' => 'array',
        'size_visible' => 'boolean'
    ];
}

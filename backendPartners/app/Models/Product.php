<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $table = 'товар';

    public $timestamps = false;
    protected $fillable = [
        'name', 'category', 'eighteen', 'characters_in', 'image',
        'articul', 'brand', 'description', 'price', 'Barcodes',
        'length', 'Width', 'Height', 'Weight_product_with_pack',
        'quality_document', 'quality_number', 'datafrom', 'databefore'
    ];

    protected $casts = [
        'characters_in' => 'array',
        'image' => 'array',
        'eighteen' => 'boolean',
        'quality_document' => 'boolean',
        'datafrom' => 'date',
        'databefore' => 'date'
    ];
}

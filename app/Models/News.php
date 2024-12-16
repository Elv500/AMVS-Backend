<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class News extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'content', 'attachment']; // Campos permitidos para asignación masiva

    
    //Para que se elimine en storage el archivo.
    protected static function booted()
    {
        static::deleted(function ($news) {
            if ($news->attachment) {
                Storage::delete(str_replace('storage/', 'public/', $news->attachment));
            }
        });
    }
}
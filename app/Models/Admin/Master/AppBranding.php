<?php

namespace App\Models\Admin\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppBranding extends Model
{
    use HasFactory;

    protected $table = 'app_branding';

    protected $fillable = [
        'app_name',
        'logo_path',
        'favicon_path',
        'primary_color',
        'secondary_color',
        'accent_color',
        'font_family',
        'custom_css',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([
            'app_name' => 'The Skool Store',
            'primary_color' => '#490d59',
            'secondary_color' => '#f7f2fb',
            'accent_color' => '#f97316',
        ]);
    }
}


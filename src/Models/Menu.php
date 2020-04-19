<?php

namespace Selene\Modules\MenuModule\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class Menu extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'menu';

    protected $appends = ['id'];
    protected $hidden  = ['_id'];

    protected $primaryKey = '_id';

    protected $fillable = [
        'lang',
        'name',
        'translations',
        'structure'
    ];

    public function getTranslations() {
        $translations = [];
        if (!empty($this->translations)) {
            foreach (json_decode($this->translations) as $id) {
                $menu = self::query()->where('_id', '=', $id)->first();
                if ($menu) {
                    $translations[$menu->lang] = $menu->id;
                }
            }
        }
        return $translations;
    }

    public static function getByLang(string $lang) {
        $menus = [];
        foreach (self::query()->where('lang', '=', $lang)->get() as $menu) {
            $menus[$menu->name] = $menu->structure;
        }
        return $menus;
    }

    public static function getByName(string $lang, string $name) {
        $menu = self::query()->where('lang', '=', $lang)
            ->where('name', '=', $name)
            ->first();

        return $menu ? $menu->structure : [];
    }
}

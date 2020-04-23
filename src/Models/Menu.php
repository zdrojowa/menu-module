<?php

namespace Selene\Modules\MenuModule\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Selene\Modules\PagesModule\Models\Page;
use Selene\Modules\RevisionModule\Models\Revision;

class Menu extends Model
{
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

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

    public static function getByPage($id) {
        return self::query()
            ->where('structure.id', '=', $id)
            ->orWhere('structure.elements.id', '=', $id)
            ->orWhere('structure.elements.elements.id', '=', $id)
            ->orWhere('structure.elements.elements.elements.id', '=', $id)
            ->get();
    }

    public static function changeMenu(Page $page, $action, $userId) {
        foreach (self::getByPage($page->id) as $menu) {
            $menu->structure = self::changeStructure($menu->structure, $page, $action);
            if ($menu->save()) {
                Revision::create([
                    'table'      => 'menu',
                    'action'     => 'auto',
                    'content_id' => $menu->id,
                    'content'    => json_encode($menu),
                    'created_at' => now(),
                    'user_id'    => $userId
                ]);
            }
        }
    }

    protected static function changeStructure($structure, Page $page, $action) {
        foreach ($structure as $i => $item) {
            if (isset($item['id']) && $item['id'] === $page->id) {
                if ($action === self::ACTION_UPDATE) {
                    $structure[$i]['name'] = $page->name;
                    $structure[$i]['url']  = $page->permalink;
                } else {
                    unset($structure[$i]);
                }
            }
            if (isset($item['elements'])) {
                $structure[$i]['elements'] = self::changeStructure($item['elements'], $page, $action);
            }
        }
        return $structure;
    }
}

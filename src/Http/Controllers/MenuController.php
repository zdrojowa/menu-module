<?php

namespace Selene\Modules\MenuModule\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\DashboardModule\ZdrojowaTable;
use Selene\Modules\LanguageModule\Models\Language;
use Selene\Modules\MenuModule\Models\Menu;
use Selene\Modules\MenuModule\Support\Type;
use Selene\Modules\RevisionModule\Models\Revision;
use Selene\Modules\SettingsModule\Models\Setting;

class MenuController extends Controller {

    public function index(Request $request)
    {
        $menu = Menu::query()->orderByDesc('_id');

        if ($request->has('lang')) {
            $lang = $request->get('lang', 'pl');
        } else {
            $setting = Setting::query()
                ->where('key', '=', 'lang')
                ->first();
            if ($setting) {
                $lang = $setting->value;
            } else {
                $lang = 'pl';
            }
        }

        $menu->where('lang', '=', $lang);

        $name = $request->get('name', '');
        if (!empty($name)) {
            $menu->where('name', 'LIKE', '%' . $name . '%');
        }

        return view('MenuModule::index', [
            'menus' => $menu->paginate(50, ['*'], 'page', $request->get('page') ?? 1),
            'langs' => Language::all(),
            'lang'  => $lang,
            'name'  => $name
        ]);
    }

    public function get(Request $request) {
        $menu = Menu::query();

        if ($request->isMethod('POST')) {
            return ZdrojowaTable::response($menu, $request);
        }

        if ($request->has('id')) {
            $menu->where('_id', '=', $request->get('id'));
            return response()->json($menu->first());
        }

        if ($request->has('query')) {
            $query = $request->get('query', '');

            if (!empty($query)) {
                $menu->where('name', 'like', '%' . $query . '%')
                    ->orWhere('_id', '=', $query);
            }
            $menu->where('_id', '!=', $request->get('qid', 0));
        }

        if ($request->has('lang')) {
            $menu->where('lang', '=', $request->get('lang'));
        }

        return response()->json($menu->get());
    }

    public function add() {
        return view('MenuModule::edit');
    }

    public function edit(Menu $menu) {
        return view('MenuModule::edit', [
            'menu'      => $menu,
            'lang'      => $menu->lang,
            'revisions' => Revision::getByContent('manu', $menu->_id, 10)
        ]);
    }

    public function store(Request $request) {
        $menu = $this->save($request);
        if ($menu) {
            $request->session()->flash('alert-success', 'Menu zostało utworzone');
        } else {
            $request->session()->flash('alert-error', 'Ooops. Try again.');
        }

        return ['redirect' => route('MenuModule::edit', ['menu' => $menu])];
    }

    public function update(Request $request, Menu $menu) {

        if ($this->save($request, $menu)) {
            $request->session()->flash('alert-success', 'Menu zostało zaktualizowane');
        } else {
            $request->session()->flash('alert-error', 'Ooops. Try again.');
        }

        return ['redirect' => route('MenuModule::edit', ['menu' => $menu])];
    }

    private function save(Request $request, Menu $menu = null) :? Menu
    {
        $obj = json_decode($request->get('obj'), true, 512, JSON_THROW_ON_ERROR);

        $obj['lang'] = $obj['lang']['key'];

        $request->merge($obj);

        $action = 'updated';
        if ($menu === null) {
            $menu = Menu::create($request->all());
            $action = 'created';
        } elseif (!$menu->update($request->all())) {
            return null;
        }

        if ($request->has('translation')) {
            $translationFrom = $request->get('translation');

            if ($translationFrom !== $menu->_id) {
                $from = Menu::where('_id', '=', $translationFrom)->first();
                if ($from) {
                    $translations = json_decode($from->translations, true);
                    if (empty($translations)) {
                        $translations = [];
                    }
                    $translations[] = $menu->_id;
                    $from->translations = json_encode($translations);
                    $from->save();

                    $translations[] = $from->_id;
                    foreach ($translations as $tr) {
                        $trPage = Menu::where('_id', '=', $tr)->first();
                        if ($trPage) {
                            $trPage->translations = json_encode(array_values(array_diff($translations, [$tr])));
                            $trPage->save();
                        }
                    }
                }
            }
        }

        $menu->refresh();

        Revision::create([
            'table' => 'menu',
            'action' => $action,
            'content_id' => $menu->id,
            'content' => json_encode($menu),
            'created_at' => now(),
            'user_id' => $request->user()->id
        ]);

        return $menu;
    }

    public function destroy(Menu $menu, Request $request): void
    {
        try {

            $id = $menu->_id;

            if (!empty($menu->translations)) {
                $translations = array_diff(json_decode($menu->translations, true), [$menu->_id]);

                foreach ($translations as $id) {
                    $translation = Menu::where('_id', '=', $id)->first();
                    if ($translation) {
                        $translation->translations = json_encode(array_values(array_diff($translations, [$id])));
                        $translation->save();
                    }
                }
            }

            $menu->delete();

            Revision::create([
                'table' => 'menu',
                'action' => 'deleted',
                'content_id' => $id,
                'content' => null,
                'created_at' => now(),
                'user_id' => $request->user()->id
            ]);

            $request->session()->flash('alert-success', 'Menu zostało usunęte');
        } catch (\Exception $e) {
            $request->session()->flash('alert-error', 'Error: ' . $e->getMessage());
        }
    }

    public function types(): JsonResponse
    {
        $types = [];
        foreach (Type::toArray() as $key => $value) {
            $types[] = ['id' => $value, 'name' => $key];
        }
        return response()->json($types);
    }

    public function addTranslation(Menu $menu, $lang) {
        return view('MenuModule::edit', ['menu' => $menu, 'lang' => $lang]);
    }

    public function check($id, Request $request): JsonResponse
    {
        $menu = Menu::query()->where('_id', '!=', $id);

        if ($request->has('lang')) {
            $menu->where('lang', '=', $request->get('lang'));
        }
        if ($request->has('name')) {
            $menu->where('name', '=', $request->get('name'));
        }
        return response()->json(!$menu->exists());
    }
}

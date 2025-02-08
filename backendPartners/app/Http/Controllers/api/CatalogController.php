<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog;
use Illuminate\Support\Facades\Validator;

class CatalogController extends Controller
{
    /**
     * Метод для создания записи в таблице "каталог".
     */
    public function store(Request $request)
    {
        // Валидация входных данных.
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'characters_valid'  => 'required|array',
            'size_visible'      => 'required|boolean',
            'size'              => 'required|integer',
            'size_ru'           => 'required|integer',
            'sub_name'          => 'nullable|string'
        ]);

        //валидация данных

        $data = $request->all();

        if (isset($data['characters_valid']) && is_array($data['characters_valid'])) {
            foreach ($data['characters_valid'] as $index => $character) {
                // Проверяем, что элемент является массивом
                if (!is_array($character)) {
                    return response()->json([
                        'error' => ['characters_valid' => ["Элемент с индексом {$index} должен быть массивом."]]
                    ], 422);
                }

                // Проверяем наличие всех обязательных ключей и их содержимое
                foreach (['value', 'charcName', 'unitName'] as $key) {
                    if (!array_key_exists($key, $character) || empty($character["charcName"]) || empty($character["unitName"])) {
                        return response()->json([
                            'error' => ['characters_valid' => ["обязательные элементы не заполнены"]]
                        ], 422);
                    }
                }
            }
        }


        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Создание записи в БД. Поле characters_valid благодаря кастингу автоматически будет сохранено в формате JSON.
        $catalog = Catalog::create($request->only([
            'name', 'characters_valid', 'size_visible', 'size', 'size_ru', 'sub_name'
        ]));

        return response()->json([
            'message' => 'Запись успешно создана',
            'data'    => $catalog
        ], 201);
    }

    public function index(Request $request)
    {
        $query = Catalog::query();

        // Если указан параметр fields, выбираем только его поля, иначе выбираем все.
        if ($request->has('fields')) {
            $fields = explode(',', $request->query('fields'));
            $query->select($fields);
        } else {
            $query->select('*');
        }

        // Если указан параметр limit, ограничиваем количество записей.
        if ($request->has('limit')) {
            $limit = (int) $request->query('limit');
            $query->limit($limit);
        }

        $catalogs = $query->get();

        return response()->json($catalogs, 200);
    }

    public function show($id)
    {
        $product = Catalog::find($id);

        if (!$product) {
            return response()->json(['message' => 'Каталог не найден'], 404);
        }

        return response()->json($product, 200);
    }


    public function update(Request $request, $id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return response()->json(['message' => 'Каталог не найден'], 404);
        }

        // Валидация входных данных для обновления.
        $validator = Validator::make($request->all(), [
            'name'             => 'sometimes|required|string',
            'characters_valid' => 'sometimes|required|array',
            'size_visible'     => 'sometimes|required|boolean',
            'size'             => 'sometimes|required|integer',
            'size_ru'          => 'sometimes|required|integer',
            'sub_name'         => 'sometimes|nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors()
            ], 422);
        }

        // Если characters_valid передан, дополнительно проверим его структуру.
        if ($request->has('characters_valid')) {
            $data = $request->input('characters_valid');
            if (is_array($data)) {
                foreach ($data as $index => $character) {
                    if (!is_array($character)) {
                        return response()->json([
                            'error' => ['characters_valid' => ["Элемент с индексом {$index} должен быть массивом."]]
                        ], 422);
                    }
                    foreach (['value', 'charcName', 'unitName'] as $key) {
                        if (!array_key_exists($key, $character) || empty($character['charcName']) || empty($character['unitName'])) {
                            return response()->json([
                                'error' => ['characters_valid' => ["Обязательные элементы не заполнены в элементе с индексом {$index}."]]
                            ], 422);
                        }
                    }
                }
            }
        }

        // Обновление полей, если они присутствуют в запросе.
        if ($request->has('name')) {
            $catalog->name = $request->name;
        }
        if ($request->has('characters_valid')) {
            // При использовании кастинга в модели нет необходимости явно применять json_encode.
            $catalog->characters_valid = $request->characters_valid;
        }
        if ($request->has('size_visible')) {
            $catalog->size_visible = $request->size_visible;
        }
        if ($request->has('size')) {
            $catalog->size = $request->size;
        }
        if ($request->has('size_ru')) {
            $catalog->size_ru = $request->size_ru;
        }
        if ($request->has('sub_name')) {
            $catalog->sub_name = $request->sub_name;
        }

        $catalog->save();

        return response()->json([
            'message' => 'Каталог обновлен',
            'data'    => $catalog
        ], 200);
    }

    public function destroy($id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return response()->json(['message' => 'Каталог не найден'], 404);
        }

        $catalog->delete();

        return response()->json(['message' => 'Каталог удален'], 200);
    }

}

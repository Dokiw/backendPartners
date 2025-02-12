<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Создание товара (POST /api/products).
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'                      => 'required|string',
            'category'                  => 'nullable|exists:каталог,id',
            'eighteen'                  => 'nullable|boolean',
            'characters_in'             => 'nullable|array',
            'image'                     => 'nullable|array',
            'articul'                   => 'nullable|string',
            'brand'                     => 'nullable|string',
            'description'               => 'nullable|string',
            'price'                     => 'nullable|numeric',
            'Barcodes'                  => 'nullable|string',
            'length'                    => 'nullable|numeric',
            'Width'                     => 'nullable|numeric',
            'Height'                    => 'nullable|numeric',
            'Weight_product_with_pack'  => 'nullable|numeric',
            'quality_document'          => 'nullable|boolean',
            'quality_number'            => 'nullable|string',
            'datafrom'                  => 'nullable|date',
            'databefore'                => 'nullable|date'
        ]);

        $data = $request->all();

        if (isset($data['characters_in']) && is_array($data['characters_in'])) {
            foreach ($data['characters_in'] as $index => $character) {
                // Проверяем, что элемент является массивом
                if (!is_array($character)) {
                    return response()->json([
                        'error' => ['characters_in' => ["Элемент с индексом {$index} должен быть массивом."]]
                    ], 422);
                }

                // Если переданы данные для характеристик, проверяем наличие обязательных ключей
                foreach (['value', 'charcName', 'unitName'] as $key) {
                    // Здесь проверяем наличие ключа и непустоту полей charcName и unitName
                    if (!array_key_exists($key, $character) || empty($character["charcName"]) || empty($character["unitName"])) {
                        return response()->json([
                            'error' => ['characters_in' => ["Обязательные элементы не заполнены"]]
                        ], 422);
                    }
                }
            }
        }

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product = Product::create([
            'name'                      => $request->name,
            'category'                  => $request->category,
            'eighteen'                  => $request->eighteen,
            'characters_in'             => json_encode($request->characters_in),
            'image'                     => json_encode($request->image),
            'articul'                   => $request->articul,
            'brand'                     => $request->brand,
            'description'               => $request->description,
            'price'                     => $request->price,
            'Barcodes'                  => $request->Barcodes,
            'length'                    => $request->length,
            'Width'                     => $request->Width,
            'Height'                    => $request->Height,
            'Weight_product_with_pack'  => $request->Weight_product_with_pack,
            'quality_document'          => $request->quality_document,
            'quality_number'            => $request->quality_number,
            'datafrom'                  => $request->datafrom,
            'databefore'                => $request->databefore
        ]);

        return response()->json(['message' => 'Товар создан', 'product' => $product], 201);
    }


    /**
     * Получение списка товаров с возможностью указания ограничения по количеству и выбора полей.
     * Пример запроса:
     *   GET /api/products?limit=10&fields=id,name,price,articul
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Обработка выбора полей (если передан параметр fields, например: fields=id,name,price)
        if ($request->has('fields')) {
            $fields = explode(',', $request->query('fields'));
            $query->select($fields);
        } else {
            // По умолчанию возвращаем "легкий" набор полей
            $query->select('id', 'name', 'price', 'articul');
        }

        // Ограничение количества записей (например, limit=10)
        if ($request->has('limit')) {
            $limit = (int) $request->query('limit');
            $query->limit($limit);
        }

        $products = $query->get();

        return response()->json($products, 200);
    }





    /**
     * Получение конкретного товара по его ID.
     * Пример запроса:
     *   GET /api/products/3
     */
    public function show($id)
    {
        $product = Product::find($id);

        if (!$product) {
            return response()->json(['message' => 'Товар не найден'], 404);
        }

        return response()->json($product, 200);
    }
    /**
     * Удаление товара по ID (DELETE /api/products/{id})
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Товар не найден'], 404);
        }

        $product->delete();

        return response()->json(['message' => 'Товар удален'], 200);
    }

    public function update(Request $request, $id)
    {
        // Поиск товара по ID
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Товар не найден'], 404);
        }

        // Валидируем только те поля, которые переданы (правило "sometimes")
        $validator = Validator::make($request->all(), [
            'name'                => 'sometimes|required|string',
            'category'            => 'sometimes|required|exists:каталог,id',
            'eighteen'            => 'sometimes|required|boolean',
            'characters_in'       => 'sometimes|required|array',
            'image'               => 'sometimes|required|array',
            'articul'             => 'sometimes|required|string',
            'brand'               => 'sometimes|required|string',
            'description'         => 'sometimes|required|string',
            'price'               => 'sometimes|required|numeric',
            'Barcodes'            => 'sometimes|required|string',
            'length'              => 'sometimes|required|numeric',
            'Width'               => 'sometimes|required|numeric',
            'Height'              => 'sometimes|required|numeric',
            'Weight_product_with_pack' => 'sometimes|required|numeric',
            'quality_document'    => 'sometimes|required|boolean',
            'quality_number'      => 'sometimes|nullable|string',
            'datafrom'            => 'sometimes|nullable|date',
            'databefore'          => 'sometimes|nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Обновляем только переданные поля
        if ($request->has('name')) {
            $product->name = $request->name;
        }
        if ($request->has('category')) {
            $product->category = $request->category;
        }
        if ($request->has('eighteen')) {
            $product->eighteen = $request->eighteen;
        }
        if ($request->has('characters_in')) {
            $data = $request->all();

            if (isset($data['characters_in']) && is_array($data['characters_in'])) {
                foreach ($data['characters_in'] as $index => $character) {
                    // Проверяем, что элемент является массивом
                    if (!is_array($character)) {
                        return response()->json([
                            'error' => ['characters_in' => ["Элемент с индексом {$index} должен быть массивом."]]
                        ], 422);
                    }

                    // Проверяем наличие всех обязательных ключей и их содержимое
                    foreach (['value', 'charcName', 'unitName'] as $key) {
                        if (!array_key_exists($key, $character) || empty($character["charcName"]) || empty($character["unitName"])) {
                            return response()->json([
                                'error' => ['characters_in' => ["обязательные элементы не заполнены"]]
                            ], 422);
                        }
                    }
                }
            }
            $product->characters_in = json_encode($request->characters_in);
        }
        if ($request->has('image')) {
            $product->image = json_encode($request->image);
        }
        if ($request->has('articul')) {
            $product->articul = $request->articul;
        }
        if ($request->has('brand')) {
            $product->brand = $request->brand;
        }
        if ($request->has('description')) {
            $product->description = $request->description;
        }
        if ($request->has('price')) {
            $product->price = $request->price;
        }
        if ($request->has('Barcodes')) {
            $product->Barcodes = $request->Barcodes;
        }
        if ($request->has('length')) {
            $product->length = $request->length;
        }
        if ($request->has('Width')) {
            $product->Width = $request->Width;
        }
        if ($request->has('Height')) {
            $product->Height = $request->Height;
        }
        if ($request->has('Weight_product_with_pack')) {
            $product->Weight_product_with_pack = $request->Weight_product_with_pack;
        }
        if ($request->has('quality_document')) {
            $product->quality_document = $request->quality_document;
        }
        if ($request->has('quality_number')) {
            $product->quality_number = $request->quality_number;
        }
        if ($request->has('datafrom')) {
            $product->datafrom = $request->datafrom;
        }
        if ($request->has('databefore')) {
            $product->databefore = $request->databefore;
        }

        $product->save();

        return response()->json([
            'message' => 'Товар обновлен',
            'product' => $product
        ], 200);
    }

    public function IndexForWeb(Request $request)
    {
        // Получаем лимит записей на страницу (по умолчанию 20)
        $limit = $request->input('limit', 5);

        // Выполняем запрос с пагинацией
        $products = Product::paginate($limit);

        // Для веб-приложения (возврат представления)
        // return view('products.index', compact('products'));

        // Для API (возврат данных в JSON)
        return response()->json($products);
    }

}

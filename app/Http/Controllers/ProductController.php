<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;


class ProductController extends Controller
{
    //
    public function index(Request $request){
        $query = Product::query(); // esto permite construir una consulta de Eloquent para el modelo Product para ir modificando la consulta según los filtros que se apliquen
        // Aplicar filtros de búsqueda si se proporcionan
        if ($request->has('category') && $request->category) { // si el request tiene un parametro category y este no es nulo, entonces se aplica el filtro de categoria
            $query->where('category_id', $request->category); // el category_id es el nombre de la columna en la tabla products que hace referencia a la categoría del producto , el category de arriba es el nombre del parametro del url con el que se envia el filtro de categoria        
        }
        if($request->has('search') && $request->search){
            $query->where('name', 'LIKE', '%' . $request->search . '%'); // el name es el nombre de la columna en la tabla products que hace referencia al nombre del producto, el search de arriba es el nombre del parametro del url con el que se envia el filtro de busqueda
        }
        // /* ENTENDER BIEN ESTA PARTE..................
        
        if($request->has('tags') && is_array($request->tags) && count($request->tags) > 0) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->whereIn('tags.id', $request->tags);
            });
        }
        //$products = Product::paginate(5);
        //return $products;
        $products = $query->with(['category','tags'])->paginate(5)->withQueryString(); // para obtener la categoría asociada a cada producto
        $categories = Category::withCount('products')->get(); // para obtener la cantidad de productos asociados a cada categoría
        $tags = Tag::all();
        //"Toma la consulta que hemos preparado, carga también la relación category y finalmente ejecútala devolviendo 5 productos por página
        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'tags' => $tags,
            'filters' => [
                'category' => $request->category,
                'search' => $request->search,
                'tags' => $request->tags ?? [], // enviar tas seleccionados al interhia
            ]
        ]);
    }

    /* public function edit(Product $product){
        return Inertia::render('Products/Edit', [
            'product' => $product
        ]);
    } */

    public function store(Request $request){

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id', // valida que el category_id exista en la tabla categories
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // valida que la imagen sea de tipo imagen y que tenga un tamaño máximo de 2MB
            'tags' => 'nullable|array', // ← Validar tags de que sea null o bien que sea array (no numeros, ni string)
            'tags.*' => 'exists:tags,id', // ← Cada tag debe existir en cada elemento del array tags, osea busca el id del tag insertado en la tabla product_tag
            // el "*"  es un comodín que significa "cada elemento del array".
            /* 
            exists	Regla de validación
            tags	Tabla donde buscar
            id	Columna donde buscar
            */
        ]);

        //dd($request->all());
        $data = $request->all();
        // esto trae data hasta el momento: 
        /* 
        array:6 [▼ // app\Http\Controllers\ProductController.php:67
        "name" => "Lavadora multipotente"
        "description" => "lavadora a prueba de agua"
        "price" => "3333"
        "stock" => "444"
        "category_id" => "1"
        "image" => 
                Illuminate\Http
                \
                UploadedFile
                {#450 ▶} contiene los metadatos de la imagen presubida
            ]
         */
        //dd($data);
        $data = $request->except('tags'); // ← Excluir tags
        $data['user_id'] = auth()->id(); // asignar el id del usuario logeado
        if( $request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public'); // guarda la imagen en la carpeta storage/app/public/products
            $data['image'] = $imagePath; // guarda la ruta de la imagen en el array de datos
        }
        $product = Product::create($data);
         // 🔑 Sincronizar tags
        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
        }
        return redirect()->route('products.index')->with('success', 'Producto creado exitosamente.');
    }

    public function edit(Product $product){
        $this->authorize('update', $product);
        $product->load(['category','tags']);
        $categories = Category::withCount('products')->get();
        $tags = Tag::all();
        return Inertia::render('Products/Edit', [
            'product' => $product,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }

    public function update(Request $request, Product $product){
        $this->authorize('update', $product); // verificar permiso
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id', // valida que el category_id exista en la tabla categories
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // valida que la imagen sea de tipo imagen y que tenga un tamaño máximo de 2MB
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        //$data = $request->all();
        $data = $request->except('tags');
        if($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $data['image'] = $imagePath;
        }
        $product->update($data);
        // 🔑 Sincronizar tags
        if ($request->has('tags')) {
            $product->tags()->sync($request->tags);
        }

        return redirect()->route('products.index')->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Product $product){
        $this->authorize('delete', $product); // verificar permiso
        if($product->image && Storage::disk('public')->exists($product->image)){
            Storage::disk('public')->delete($product->image); // esto elimina public/product/image.jpg
        }
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Producto eliminado exitosamente.');
    }
}

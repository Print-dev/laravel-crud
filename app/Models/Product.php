<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    // son campo permitidos para que se pueda hacer un insert o update masivo
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'image', // Agrega la columna 'image' a la lista de campos permitidos
        'user_id',
    ];

    // estas relaciones son de uno a uno
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Relación: un producto pertenece a una categoría
        // ✅ Correcto: singular (pertenece a UNA)

    public function category(): BelongsTo{ // esto es singular asi que no lleva "s" al final
        return $this->belongsTo(Category::class);
    }

    // ✅ Correcto: plural (tiene MUCHAS)
    // relacion muchos a muchos con la tabla tag (agrear una "S" despues del nombre)
    public function tags(): BelongsToMany { // es es plural por eso lleva "s" al final
        return $this->belongsToMany(Tag::class, 'product_tag');
    }
}

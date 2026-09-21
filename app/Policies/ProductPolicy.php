<?php

namespace App\Policies;
// al importar o aber creado un policy referente a Product le decimos que haremos validacion de autorizacion para este modelo
use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // cualquier usuario logeado puede crear
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): bool
    {   // hara el update solo si el user id logeado (o el q le pasen al paremtro al momento de usarlo) es igual al user id del producto al que se quiere actualizar o bien si el usuario logeado es admin
        return $user->id === $product->user_id || $user->is_admin; // retorna true  o  false ... si es false lanza AuthorizationException y laravel lo capta (controlador) y muestra el error 403
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Product $product): bool
    {
        return $user->id === $product->user_id || $user->is_admin;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Product $product): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Product $product): bool
    {
        return false;
    }
}

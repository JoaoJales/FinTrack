<?php

namespace App\Services;

use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Collection;

class CategoryService
{
    public function getAllByUser(int $userId): Collection
    {
        return Category::whereNull('user_id') // Não precisamos verificar as deletadas (softDelete já faz o filtro)
            ->orWhere('user_id', $userId) // orWhere: Busca as globais e as do user -> O resultado final é uma lista única contendo as duas coisas juntas.
            ->orderBy('is_editable', 'desc')
            ->get();
    }

    public function getAllCreatedByUser(int $userId, $type): Collection
    {
        return Category::where('user_id', $userId)
            ->where('is_editable', true)
            ->where('type', $type)
            ->orderBy('type')
            ->get();
    }

    public function getOnlyTrashedByUser(int $userId): Collection
    {
        return Category::where('user_id', $userId)
            ->onlyTrashed()
            ->get();
    }

    public function store(array $request, int $userId): Category
    {
        $request['user_id'] = $userId;
        $request['is_editable'] = true;

        return Category::create($request);
    }

    public function update(Category $category, array $request): void
    {

        $category->update($request);

        //        return $category->fresh();
    }

    public function destroy(Category $category): void
    {
        $category->delete();
    }
}

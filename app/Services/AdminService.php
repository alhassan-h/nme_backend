<?php

namespace App\Services;

use App\Models\Newsletter;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminService
{
    public function getDashboardStats(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('verified', true)->count(),
            'total_products' => Product::count(),
            'pending_products' => Product::where('status', Product::STATUS_PENDING)->count(),
        ];
    }

    public function paginatedUsers(int $perPage): LengthAwarePaginator
    {
        return User::paginate($perPage);
    }

    public function updateUserStatus(User $user, string $status): User
    {
        $user->update(['status' => $status]);
        return $user;
    }

    public function getPendingProducts(int $perPage): LengthAwarePaginator
    {
        return Product::where('status', Product::STATUS_PENDING)
            ->with('seller')
            ->paginate($perPage);
    }

    public function approveProduct(Product $product): Product
    {
        $product->status = Product::STATUS_ACTIVE;
        $product->save();

        return $product;
    }

    public function createNewsletter(array $data): Newsletter
    {
        return Newsletter::create($data);
    }
}

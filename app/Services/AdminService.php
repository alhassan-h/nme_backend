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
        try {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('verified', true)->count(),
                'total_products' => Product::count(),
                'pending_products' => Product::where('status', Product::STATUS_PENDING)->count(),
                'total_revenue' => Product::where('status', Product::STATUS_SOLD)->sum('price') ?? 0,
                'total_newsletters' => \DB::table('newsletters')->count(),
            ];
        } catch (\Exception $e) {
            // Return default values if there's a database error
            return [
                'total_users' => 0,
                'active_users' => 0,
                'total_products' => 0,
                'pending_products' => 0,
                'total_revenue' => 0,
                'total_newsletters' => 0,
            ];
        }
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

    public function getRecentActivity(int $limit = 10): array
    {
        try {
            $activities = [];

            // Get recent user registrations
            $recentUsers = User::latest()->take(3)->get();
            foreach ($recentUsers as $user) {
                $activities[] = [
                    'id' => "user_{$user->id}",
                    'type' => 'user_registration',
                    'message' => "New user registered: {$user->first_name} {$user->last_name}",
                    'time' => $user->created_at->diffForHumans(),
                    'status' => 'success',
                    'user' => [
                        'id' => $user->id,
                        'name' => trim("{$user->first_name} {$user->last_name}"),
                        'email' => $user->email
                    ],
                    'timestamp' => $user->created_at->toISOString()
                ];
            }

            // Get recent products
            $recentProducts = \DB::table('products')
                ->join('users', 'products.seller_id', '=', 'users.id')
                ->select('products.*', 'users.first_name', 'users.last_name', 'users.email')
                ->latest('products.created_at')
                ->take(3)
                ->get();

            foreach ($recentProducts as $product) {
                $status = match($product->status) {
                    'pending' => 'warning',
                    'active' => 'success',
                    'sold' => 'success',
                    default => 'info'
                };

                $activities[] = [
                    'id' => "product_{$product->id}",
                    'type' => $product->status === 'pending' ? 'listing_pending' : 'listing_approved',
                    'message' => $product->status === 'pending'
                        ? "New listing pending approval: {$product->title}"
                        : "Product listing approved: {$product->title}",
                    'time' => \Carbon\Carbon::parse($product->created_at)->diffForHumans(),
                    'status' => $status,
                    'user' => [
                        'id' => $product->seller_id,
                        'name' => trim("{$product->first_name} {$product->last_name}"),
                        'email' => $product->email
                    ],
                    'timestamp' => $product->created_at
                ];
            }

            // Sort by timestamp and limit
            return collect($activities)
                ->sortByDesc('timestamp')
                ->take($limit)
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            // Return empty array if there's a database error
            return [];
        }
    }

    public function getPendingTasks(): array
    {
        try {
            return [
                [
                    'task' => 'Review pending product listings',
                    'priority' => 'high',
                    'count' => Product::where('status', Product::STATUS_PENDING)->count(),
                ],
                [
                    'task' => 'Review pending gallery images',
                    'priority' => 'high',
                    'count' => 5, // This could be replaced with actual count from gallery_images table
                ],
                [
                    'task' => 'Update market insights',
                    'priority' => 'low',
                    'count' => 3,
                ],
                [
                    'task' => 'Send newsletter campaign',
                    'priority' => 'medium',
                    'count' => 1,
                ],
            ];
        } catch (\Exception $e) {
            // Return empty array if there's a database error
            return [];
        }
    }
}

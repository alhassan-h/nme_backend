<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\MarketInsight;
use App\Models\ForumPost;
use App\Models\GalleryImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function stats(Request $request): JsonResponse
    {
        $user = Auth::user();

        $stats = [
            'listings' => 0,
            'active_listings' => 0,
            'total_views' => 0,
            'saved_products' => 0,
            'revenue' => 0,
            'recent_activity' => [],
        ];

        if ($user->userType === 'seller' || $user->userType === 'both') {
            $products = Product::where('user_id', $user->id)->get();

            $stats['listings'] = $products->count();
            $stats['active_listings'] = $products->where('status', 'active')->count();
            $stats['total_views'] = $products->sum('views');
            // For now, revenue is calculated as a placeholder - in real app this would come from orders/transactions
            $stats['revenue'] = $products->where('status', 'sold')->sum('price');
        }

        if ($user->userType === 'buyer' || $user->userType === 'both') {
            $stats['saved_products'] = $user->favorites()->count();
        }

        return response()->json($stats);
    }

    public function recentActivity(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = $request->get('limit', 10);

        $activities = [];

        // Get recent product activities
        if ($user->userType === 'seller' || $user->userType === 'both') {
            $productActivities = Product::where('user_id', $user->id)
                ->orderBy('updated_at', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($product) {
                    return [
                        'id' => "product_{$product->id}",
                        'type' => 'product_update',
                        'title' => 'Listing Updated',
                        'description' => $product->title,
                        'timestamp' => $product->updated_at,
                        'icon' => 'package',
                        'color' => 'green',
                    ];
                });

            $activities = array_merge($activities, $productActivities->toArray());
        }

        // Get recent forum activities
        $forumActivities = ForumPost::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($post) {
                return [
                    'id' => "forum_{$post->id}",
                    'type' => 'forum_post',
                    'title' => 'Forum Post Created',
                    'description' => $post->title,
                    'timestamp' => $post->created_at,
                    'icon' => 'message-square',
                    'color' => 'blue',
                ];
            });

        $activities = array_merge($activities, $forumActivities->toArray());

        // Get recent gallery activities
        $galleryActivities = GalleryImage::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($image) {
                return [
                    'id' => "gallery_{$image->id}",
                    'type' => 'gallery_upload',
                    'title' => 'Gallery Image Uploaded',
                    'description' => $image->title ?? 'New image',
                    'timestamp' => $image->created_at,
                    'icon' => 'image',
                    'color' => 'purple',
                ];
            });

        $activities = array_merge($activities, $galleryActivities->toArray());

        // Sort by timestamp and limit
        usort($activities, function ($a, $b) {
            return strtotime($b['timestamp']) - strtotime($a['timestamp']);
        });

        return response()->json(array_slice($activities, 0, $limit));
    }

    public function revenue(Request $request): JsonResponse
    {
        $user = Auth::user();

        if ($user->userType !== 'seller' && $user->userType !== 'both') {
            return response()->json(['revenue' => 0, 'monthly' => [], 'yearly' => 0]);
        }

        // Calculate revenue from sold products
        $totalRevenue = Product::where('user_id', $user->id)
            ->where('status', 'sold')
            ->sum('price');

        // Monthly revenue for the last 12 months
        $monthlyRevenue = Product::where('user_id', $user->id)
            ->where('status', 'sold')
            ->where('updated_at', '>=', now()->subMonths(12))
            ->select(
                DB::raw('YEAR(updated_at) as year'),
                DB::raw('MONTH(updated_at) as month'),
                DB::raw('SUM(price) as revenue')
            )
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'month' => date('M Y', strtotime("{$item->year}-{$item->month}-01")),
                    'revenue' => (float) $item->revenue,
                ];
            });

        // Yearly revenue
        $yearlyRevenue = Product::where('user_id', $user->id)
            ->where('status', 'sold')
            ->where('updated_at', '>=', now()->subYear())
            ->sum('price');

        return response()->json([
            'total' => (float) $totalRevenue,
            'monthly' => $monthlyRevenue,
            'yearly' => (float) $yearlyRevenue,
        ]);
    }
}

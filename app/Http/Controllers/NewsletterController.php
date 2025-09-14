<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use App\Models\NewsletterSubscriber;
use App\Models\NewsletterRecipient;
use App\Services\AdminService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NewsletterController extends Controller
{
    protected AdminService $adminService;

    public function __construct(AdminService $adminService)
    {
        $this->adminService = $adminService;
    }

    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:newsletter_subscribers,email',
            'name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        $subscriber = NewsletterSubscriber::create([
            'email' => $request->email,
            'name' => $request->name,
            'subscribed_at' => now(),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Successfully subscribed to newsletter',
            'subscriber' => $subscriber,
        ]);
    }

    public function index(Request $request): LengthAwarePaginator
    {
        $query = Newsletter::with('recipients');

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($request->get('per_page', 15));
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'html_content' => 'nullable|string',
            'scheduled_for' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $newsletter = Newsletter::create([
            'subject' => $request->subject,
            'content' => $request->content,
            'html_content' => $request->html_content,
            'status' => $request->scheduled_for ? 'scheduled' : 'draft',
            'scheduled_for' => $request->scheduled_for,
        ]);

        return response()->json([
            'message' => 'Newsletter created successfully',
            'newsletter' => $newsletter->load('recipients'),
        ], 201);
    }

    public function show(Newsletter $newsletter): JsonResponse
    {
        return response()->json([
            'newsletter' => $newsletter->load('recipients.subscriber'),
        ]);
    }

    public function update(Request $request, Newsletter $newsletter): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'html_content' => 'nullable|string',
            'scheduled_for' => 'nullable|date|after:now',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        $newsletter->update([
            'subject' => $request->subject,
            'content' => $request->content,
            'html_content' => $request->html_content,
            'scheduled_for' => $request->scheduled_for,
        ]);

        return response()->json([
            'message' => 'Newsletter updated successfully',
            'newsletter' => $newsletter->load('recipients'),
        ]);
    }

    public function destroy(Newsletter $newsletter): JsonResponse
    {
        $newsletter->delete();

        return response()->json([
            'message' => 'Newsletter deleted successfully',
        ]);
    }

    public function send(Newsletter $newsletter): JsonResponse
    {
        if ($newsletter->status === 'sent') {
            return response()->json(['message' => 'Newsletter has already been sent'], 400);
        }

        // Use service class to handle sending logic
        $this->adminService->sendNewsletter($newsletter);

        return response()->json([
            'message' => 'Newsletter sent successfully',
            'newsletter' => $newsletter->load('recipients'),
        ]);
    }

    public function subscribers(Request $request): LengthAwarePaginator
    {
        $query = NewsletterSubscriber::query();

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        return $query->orderBy('subscribed_at', 'desc')->paginate($request->get('per_page', 15));
    }

    public function checkStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid data', 'errors' => $validator->errors()], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if (!$subscriber) {
            return response()->json(['message' => 'Subscriber not found'], 404);
        }

        // Verify token
        $expectedToken = sha1($subscriber->email . $subscriber->id . config('app.key'));
        if ($request->token !== $expectedToken) {
            return response()->json(['message' => 'Invalid token'], 403);
        }

        return response()->json([
            'status' => $subscriber->status,
            'email' => $subscriber->email,
            'name' => $subscriber->name,
            'subscribed_at' => $subscriber->subscribed_at,
            'unsubscribed_at' => $subscriber->unsubscribed_at,
        ]);
    }

    public function unsubscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:newsletter_subscribers,email',
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid request', 'errors' => $validator->errors()], 422);
        }

        $subscriber = NewsletterSubscriber::where('email', $request->email)->first();

        if (!$subscriber) {
            return response()->json(['message' => 'Subscriber not found'], 404);
        }

        if ($subscriber->status === 'unsubscribed') {
            return response()->json(['message' => 'You have already unsubscribed from our newsletter']);
        }

        // Verify token
        $expectedToken = sha1($subscriber->email . $subscriber->id . config('app.key'));
        if ($request->token !== $expectedToken) {
            return response()->json(['message' => 'Invalid unsubscribe token'], 403);
        }

        // Update subscriber status
        $subscriber->update([
            'status' => 'unsubscribed',
            'unsubscribed_at' => now(),
        ]);

        return response()->json([
            'message' => 'You have been successfully unsubscribed from our newsletter',
        ]);
    }

    public function stats(): JsonResponse
    {
        $totalSubscribers = NewsletterSubscriber::count();
        $activeSubscribers = NewsletterSubscriber::where('status', 'active')->count();
        $unsubscribedSubscribers = NewsletterSubscriber::where('status', 'unsubscribed')->count();
        $totalNewsletters = Newsletter::count();
        $sentNewsletters = Newsletter::where('status', 'sent')->count();

        $avgOpenRate = Newsletter::where('status', 'sent')->get()->avg('open_rate') ?? 0;
        $avgClickRate = Newsletter::where('status', 'sent')->get()->avg('click_rate') ?? 0;

        // Calculate unsubscribe rate
        $unsubscribeRate = $totalSubscribers > 0 ? round(($unsubscribedSubscribers / $totalSubscribers) * 100, 2) : 0;

        return response()->json([
            'total_subscribers' => $totalSubscribers,
            'active_subscribers' => $activeSubscribers,
            'unsubscribed_subscribers' => $unsubscribedSubscribers,
            'unsubscribe_rate' => $unsubscribeRate,
            'total_newsletters' => $totalNewsletters,
            'sent_newsletters' => $sentNewsletters,
            'avg_open_rate' => round($avgOpenRate, 2),
            'avg_click_rate' => round($avgClickRate, 2),
        ]);
    }
}

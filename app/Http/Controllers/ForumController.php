<?php

namespace App\Http\Controllers;

use App\Http\Requests\Forum\ForumPostCreateRequest;
use App\Http\Requests\Forum\ForumReplyCreateRequest;
use App\Models\ForumPost;
use App\Services\ForumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForumController extends Controller
{
    protected ForumService $forumService;

    public function __construct(ForumService $forumService)
    {
        $this->forumService = $forumService;
    }

    public function index(Request $request): JsonResponse
    {
        $category = $request->get('category');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 15);

        $data = $this->forumService->getPosts($category, $perPage, $page);

        return response()->json($data);
    }

    public function show(ForumPost $post): JsonResponse
    {
        return response()->json($post->load('author'));
    }

    public function store(ForumPostCreateRequest $request): JsonResponse
    {
        $user = Auth::user();
        $post = $this->forumService->createPost($request->validated(), $user);

        return response()->json($post, Response::HTTP_CREATED);
    }

    public function replies(ForumPost $post): JsonResponse
    {
        $replies = $this->forumService->getReplies($post);

        return response()->json($replies);
    }

    public function storeReply(ForumPost $post, ForumReplyCreateRequest $request): JsonResponse
    {
        $user = Auth::user();

        $reply = $this->forumService->createReply($post, $request->validated(), $user);

        return response()->json($reply, Response::HTTP_CREATED);
    }
}

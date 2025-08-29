<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid email', 'errors' => $validator->errors()], 422);
        }

        // For demo, we simulate newsletter subscription success
        // In real, integrate mailing list system here.

        return response()->json([
            'message' => 'Successfully subscribed to newsletter',
        ]);
    }
}

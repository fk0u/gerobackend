<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponseTrait;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    use ApiResponseTrait;

    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|string|in:bug_report,feature_request,general,complaint',
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'rating' => 'nullable|integer|min:1|max:5',
            'email' => 'nullable|email|max:255',
        ]);

        $data['user_id'] = Auth::id();
        $data['status'] = 'pending';

        $feedback = Feedback::create($data);

        return $this->successResponse(
            $feedback,
            'Feedback submitted successfully',
            201
        );
    }

    public function index(Request $request)
    {
        $query = Feedback::query();
        
        // Filter by user (for regular users, only show their own feedback)
        if (!Auth::user()->role === 'admin') {
            $query->where('user_id', Auth::id());
        }
        
        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->string('type'));
        }
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $perPage = $request->integer('per_page', 20);
        $feedback = $query->latest()->paginate($perPage);

        return $this->successResponse($feedback, 'Feedback retrieved successfully');
    }
}
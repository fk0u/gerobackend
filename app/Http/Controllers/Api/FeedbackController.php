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

    public function show(int $id)
    {
        $feedback = Feedback::findOrFail($id);
        
        // Users can only see their own feedback unless admin
        if (Auth::user()->role !== 'admin' && $feedback->user_id !== Auth::id()) {
            return $this->errorResponse('Forbidden', 403);
        }
        
        return $this->successResponse($feedback, 'Feedback retrieved successfully');
    }

    public function update(Request $request, int $id)
    {
        $feedback = Feedback::findOrFail($id);
        
        // Users can only update their own feedback
        if ($feedback->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return $this->errorResponse('Forbidden: You can only update your own feedback', 403);
        }
        
        $data = $request->validate([
            'type' => 'sometimes|string|in:bug_report,feature_request,general,complaint',
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:2000',
            'rating' => 'sometimes|nullable|integer|min:1|max:5',
            'status' => 'sometimes|string|in:pending,reviewed,resolved,closed',
        ]);

        $feedback->update($data);
        
        return $this->successResponse($feedback, 'Feedback updated successfully');
    }

    public function destroy(int $id)
    {
        $feedback = Feedback::findOrFail($id);
        
        // Users can delete their own feedback
        if ($feedback->user_id !== Auth::id() && Auth::user()->role !== 'admin') {
            return $this->errorResponse('Forbidden: You can only delete your own feedback', 403);
        }
        
        $feedback->delete();
        
        return $this->successResponse(null, 'Feedback deleted successfully', 200);
    }
}
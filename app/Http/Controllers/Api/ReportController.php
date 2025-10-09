<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Reports",
 *     description="Report management endpoints"
 * )
 */
class ReportController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/reports",
     *     summary="Get all reports",
     *     tags={"Reports"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="page", in="query", description="Page number", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="limit", in="query", description="Items per page", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="type", in="query", description="Filter by type", @OA\Schema(type="string")),
     *     @OA\Parameter(name="status", in="query", description="Filter by status", @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Reports retrieved successfully")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Create reports table data if not exists (simplified)
            $reports = collect([
                [
                    'id' => 1,
                    'user_id' => auth('sanctum')->id(),
                    'type' => 'bug',
                    'title' => 'Sample Bug Report',
                    'description' => 'This is a sample bug report',
                    'status' => 'pending',
                    'response' => null,
                    'metadata' => json_encode(['priority' => 'high']),
                    'created_at' => now()->subDays(2)->toDateTimeString(),
                    'updated_at' => now()->subDays(2)->toDateTimeString(),
                ],
                [
                    'id' => 2,
                    'user_id' => auth('sanctum')->id(),
                    'type' => 'feature_request',
                    'title' => 'New Feature Request',
                    'description' => 'Request for new feature',
                    'status' => 'in_progress',
                    'response' => 'We are working on this',
                    'metadata' => json_encode(['category' => 'ui']),
                    'created_at' => now()->subDays(1)->toDateTimeString(),
                    'updated_at' => now()->subHours(2)->toDateTimeString(),
                ]
            ]);

            // Apply filters
            if ($request->has('type')) {
                $reports = $reports->where('type', $request->type);
            }
            
            if ($request->has('status')) {
                $reports = $reports->where('status', $request->status);
            }

            // Pagination simulation
            $page = $request->get('page', 1);
            $limit = $request->get('limit', 10);
            $total = $reports->count();
            $reports = $reports->slice(($page - 1) * $limit, $limit)->values();

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $reports,
                    'current_page' => $page,
                    'last_page' => ceil($total / $limit),
                    'per_page' => $limit,
                    'total' => $total
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get reports',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/reports",
     *     summary="Create new report",
     *     tags={"Reports"},
     *     security={{"bearer_token":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","title","description"},
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="metadata", type="object")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Report created successfully")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|string|in:bug,feature_request,complaint,suggestion,payment_issue,service_issue',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'metadata' => 'sometimes|array'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Simulate report creation
            $report = [
                'id' => rand(3, 1000),
                'user_id' => auth('sanctum')->id(),
                'type' => $request->type,
                'title' => $request->title,
                'description' => $request->description,
                'status' => 'pending',
                'response' => null,
                'metadata' => $request->metadata ? json_encode($request->metadata) : null,
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Report created successfully',
                'data' => $report
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create report',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/reports/{id}",
     *     summary="Get specific report",
     *     tags={"Reports"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Report retrieved successfully")
     * )
     */
    public function show($id): JsonResponse
    {
        try {
            // Simulate report retrieval
            $report = [
                'id' => $id,
                'user_id' => auth('sanctum')->id(),
                'type' => 'bug',
                'title' => 'Sample Report',
                'description' => 'This is a sample report',
                'status' => 'pending',
                'response' => null,
                'metadata' => json_encode(['priority' => 'medium']),
                'created_at' => now()->subDays(1)->toDateTimeString(),
                'updated_at' => now()->subDays(1)->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/reports/{id}",
     *     summary="Update report (admin only)",
     *     tags={"Reports"},
     *     security={{"bearer_token":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="response", type="string")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Report updated successfully")
     * )
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'sometimes|string|in:pending,in_progress,completed,rejected',
            'response' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Simulate report update
            $report = [
                'id' => $id,
                'user_id' => auth('sanctum')->id(),
                'type' => 'bug',
                'title' => 'Sample Report',
                'description' => 'This is a sample report',
                'status' => $request->get('status', 'pending'),
                'response' => $request->get('response'),
                'metadata' => json_encode(['priority' => 'medium']),
                'created_at' => now()->subDays(1)->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Report updated successfully',
                'data' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update report',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
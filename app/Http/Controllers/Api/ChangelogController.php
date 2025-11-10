<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

/**
 * @OA\Tag(
 *     name="Changelog",
 *     description="API endpoints untuk changelog dari GitHub repository"
 * )
 */
class ChangelogController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/changelog",
     *     summary="Get changelog dari GitHub commits",
     *     description="Mengambil daftar commit terbaru dari GitHub repository dengan caching",
     *     operationId="getChangelog",
     *     tags={"Changelog"},
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="Jumlah commit yang ingin ditampilkan (default: 20, max: 100)",
     *         required=false,
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Parameter(
     *         name="branch",
     *         in="query",
     *         description="Branch name (default: main)",
     *         required=false,
     *         @OA\Schema(type="string", example="main")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Changelog berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="repository", type="string", example="fk0u/gerobackend"),
     *                 @OA\Property(property="branch", type="string", example="main"),
     *                 @OA\Property(
     *                     property="commits",
     *                     type="array",
     *                     @OA\Items(
     *                         @OA\Property(property="sha", type="string", example="a1b2c3d"),
     *                         @OA\Property(property="message", type="string", example="feat: add schedule lifecycle endpoints"),
     *                         @OA\Property(property="author", type="string", example="John Doe"),
     *                         @OA\Property(property="email", type="string", example="john@example.com"),
     *                         @OA\Property(property="date", type="string", format="date-time", example="2024-01-15T10:30:00Z"),
     *                         @OA\Property(property="url", type="string", example="https://github.com/fk0u/gerobackend/commit/a1b2c3d")
     *                     )
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=20),
     *                 @OA\Property(property="cached", type="boolean", example=true),
     *                 @OA\Property(property="cache_expires_at", type="string", format="date-time", example="2024-01-15T11:30:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Gagal mengambil changelog dari GitHub",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Failed to fetch changelog from GitHub"),
     *             @OA\Property(property="error", type="string", example="GitHub API rate limit exceeded")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $repo = config('l5-swagger.changelog.github_repo', env('GITHUB_REPO', 'fk0u/gerobackend'));
        $token = config('l5-swagger.changelog.github_token', env('GITHUB_TOKEN'));
        $page = max((int) $request->get('page', 1), 1);
        $perPage = min((int) $request->get('per_page', 5), 20); // Default 5, max 20
        $branch = $request->get('branch', 'main');
        
        $cacheKey = "github_changelog_{$repo}_{$branch}_page_{$page}_per_{$perPage}";
        $cacheTtl = (int) config('l5-swagger.changelog.cache_ttl', 3600);
        
        try {
            $isCached = Cache::has($cacheKey);
            $cacheExpiresAt = now()->addSeconds($cacheTtl);
            
            $commits = Cache::remember($cacheKey, $cacheTtl, function () use ($repo, $token, $perPage, $page, $branch) {
                $url = "https://api.github.com/repos/{$repo}/commits";
                
                $headers = [
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'Gerobaks-Backend-API',
                ];
                
                if ($token) {
                    $headers['Authorization'] = "token {$token}";
                }
                
                $response = Http::withHeaders($headers)
                    ->timeout(10)
                    ->get($url, [
                        'per_page' => $perPage,
                        'page' => $page,
                        'sha' => $branch,
                    ]);
                
                if (!$response->successful()) {
                    Log::error('GitHub API Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'headers' => $response->headers(),
                    ]);
                    
                    throw new \Exception("GitHub API returned status {$response->status()}");
                }
                
                return collect($response->json())->map(function ($commit) use ($repo) {
                    return [
                        'sha' => $commit['sha'],
                        'commit' => [
                            'message' => $commit['commit']['message'],
                            'author' => [
                                'name' => $commit['commit']['author']['name'],
                                'email' => $commit['commit']['author']['email'],
                                'date' => $commit['commit']['author']['date'],
                            ],
                        ],
                        'author' => $commit['author'] ? [
                            'login' => $commit['author']['login'],
                            'avatar_url' => $commit['author']['avatar_url'],
                            'html_url' => $commit['author']['html_url'],
                        ] : null,
                        'html_url' => $commit['html_url'],
                    ];
                })->toArray();
            });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'repository' => $repo,
                    'branch' => $branch,
                    'commits' => $commits,
                    'pagination' => [
                        'current_page' => $page,
                        'per_page' => $perPage,
                        'total' => count($commits),
                        'has_more' => count($commits) === $perPage,
                    ],
                    'cached' => $isCached,
                    'cache_expires_at' => $cacheExpiresAt->toIso8601String(),
                ],
                'meta' => [
                    'github_url' => "https://github.com/{$repo}",
                    'commits_url' => "https://github.com/{$repo}/commits/{$branch}",
                ],
            ]);
            
        } catch (\Exception $e) {
            Log::error('Changelog fetch error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch changelog from GitHub',
                'error' => $e->getMessage(),
                'data' => [
                    'repository' => $repo,
                    'commits' => [],
                    'total' => 0,
                ],
            ], 500);
        }
    }
    
    /**
     * @OA\Post(
     *     path="/api/changelog/clear-cache",
     *     summary="Clear changelog cache",
     *     description="Menghapus cache changelog untuk force refresh dari GitHub",
     *     operationId="clearChangelogCache",
     *     tags={"Changelog"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Cache berhasil dihapus",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Changelog cache cleared successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated.")
     *         )
     *     )
     * )
     */
    public function clearCache()
    {
        try {
            // Clear semua cache yang berhubungan dengan changelog
            $repo = config('l5-swagger.changelog.github_repo', env('GITHUB_REPO', 'fk0u/gerobackend'));
            
            // Clear semua variant cache
            foreach (['main', 'master', 'develop'] as $branch) {
                foreach ([10, 20, 50, 100] as $limit) {
                    $cacheKey = "github_changelog_{$repo}_{$branch}_{$limit}";
                    Cache::forget($cacheKey);
                }
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Changelog cache cleared successfully',
                'timestamp' => now()->toIso8601String(),
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * @OA\Get(
     *     path="/api/changelog/stats",
     *     summary="Get repository statistics",
     *     description="Mengambil statistik repository dari GitHub",
     *     operationId="getChangelogStats",
     *     tags={"Changelog"},
     *     @OA\Response(
     *         response=200,
     *         description="Statistik berhasil diambil",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="stars", type="integer", example=15),
     *                 @OA\Property(property="watchers", type="integer", example=3),
     *                 @OA\Property(property="forks", type="integer", example=2),
     *                 @OA\Property(property="open_issues", type="integer", example=5),
     *                 @OA\Property(property="language", type="string", example="PHP"),
     *                 @OA\Property(property="default_branch", type="string", example="main"),
     *                 @OA\Property(property="created_at", type="string", example="2024-01-01T00:00:00Z"),
     *                 @OA\Property(property="updated_at", type="string", example="2024-01-15T12:00:00Z")
     *             )
     *         )
     *     )
     * )
     */
    public function stats()
    {
        $repo = config('l5-swagger.changelog.github_repo', env('GITHUB_REPO', 'fk0u/gerobackend'));
        $token = config('l5-swagger.changelog.github_token', env('GITHUB_TOKEN'));
        
        $cacheKey = "github_repo_stats_{$repo}";
        
        try {
            $stats = Cache::remember($cacheKey, 3600, function () use ($repo, $token) {
                $url = "https://api.github.com/repos/{$repo}";
                
                $headers = [
                    'Accept' => 'application/vnd.github.v3+json',
                    'User-Agent' => 'Gerobaks-Backend-API',
                ];
                
                if ($token) {
                    $headers['Authorization'] = "token {$token}";
                }
                
                $response = Http::withHeaders($headers)->get($url);
                
                if (!$response->successful()) {
                    throw new \Exception("GitHub API returned status {$response->status()}");
                }
                
                $data = $response->json();
                
                return [
                    'name' => $data['name'],
                    'full_name' => $data['full_name'],
                    'description' => $data['description'],
                    'stars' => $data['stargazers_count'],
                    'watchers' => $data['watchers_count'],
                    'forks' => $data['forks_count'],
                    'open_issues' => $data['open_issues_count'],
                    'language' => $data['language'],
                    'default_branch' => $data['default_branch'],
                    'created_at' => $data['created_at'],
                    'updated_at' => $data['updated_at'],
                    'homepage' => $data['homepage'],
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch repository stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

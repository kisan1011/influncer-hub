<?php

namespace App\Http\Controllers\api\v1;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Exception;

class BlogController extends Controller
{
    // Get published blogs list with pagination and search
    public function index(Request $request)
    {
        try {
            $perPage = $request->per_page ?? 10;
            $query = $request->search; // Search query parameter

            $blogs = Blog::published()
                        ->latest()
                        ->select([
                            'id', 'title', 'slug', 'short_description',
                            'featured_image', 'author_name', 'published_at',
                            'meta_title', 'meta_description', 'meta_keywords'
                        ])
                        // Apply search filter if query exists
                        ->when(!empty($query), function ($q) use ($query) {
                            $q->where(function ($subQ) use ($query) {
                                $subQ->where('title', 'LIKE', "%{$query}%")
                                     ->orWhere('short_description', 'LIKE', "%{$query}%")
                                     ->orWhere('content', 'LIKE', "%{$query}%")
                                     ->orWhere('meta_keywords', 'LIKE', "%{$query}%");
                            });
                        })
                        ->paginate($perPage);

            // Check if page exists
            if($blogs->lastPage() < $request->page){
                throw new Exception("Page not found.");
            }

            // Transform the paginated collection
            $blogs->getCollection()->transform(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'excerpt' => $blog->excerpt,
                    'featured_image' => $blog->featured_image,
                    'author_name' => $blog->author_name ?: 'Admin',
                    'published_at' => $blog->published_at->format('M d, Y'),
                    'published_date' => $blog->published_at->format('Y-m-d'),
                    'reading_time' => $blog->reading_time,
                    'meta' => [
                        'title' => $blog->meta_title ?: $blog->title,
                        'description' => $blog->meta_description ?: $blog->short_description,
                        'keywords' => $blog->meta_keywords,
                        'image' => $blog->featured_image,
                        'type' => 'article'
                    ]
                ];
            });

            // Add search query info to the response
            $blogs->appends(['search_query' => $query]);

            $message = !empty($query) ? "Search results fetched successfully." : "Blogs fetched successfully.";
            return CustomFacade::successResponse($message, $blogs);
        } catch (Exception $e) {
            $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
            return CustomFacade::errorResponse($message);
        }
    }

    // Get single blog by slug
    public function show($slug)
    {
        try {
            $blog = Blog::published()
                       ->where('slug', $slug)
                       ->select([
                           'id', 'title', 'slug', 'content', 'short_description',
                           'featured_image', 'author_name', 'published_at',
                           'meta_title', 'meta_description', 'meta_keywords'
                       ])
                       ->first();

            if (!$blog) {
                return CustomFacade::errorResponse('Blog not found.', [], 404);
            }

            $blogData = [
                'id' => $blog->id,
                'title' => $blog->title,
                'slug' => $blog->slug,
                'content' => $blog->content,
                'short_description' => $blog->short_description,
                'featured_image' => $blog->featured_image,
                'author_name' => $blog->author_name ?: 'Admin',
                'published_at' => $blog->published_at->format('M d, Y'),
                'published_date' => $blog->published_at->format('Y-m-d'),
                'reading_time' => $blog->reading_time,
                'meta' => [
                    'title' => $blog->meta_title ?: $blog->title,
                    'description' => $blog->meta_description ?: $blog->short_description,
                    'keywords' => $blog->meta_keywords,
                    'image' => $blog->featured_image,
                    'type' => 'article'
                ]
            ];

            return CustomFacade::successResponse("Blog fetched successfully.", $blogData);
        } catch (Exception $e) {
            $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
            return CustomFacade::errorResponse($message);
        }
    }

    // Get latest blogs (for homepage or sidebar)
    public function latest(Request $request)
    {
        try {
            $limit = $request->limit ?? 5;

            $blogs = Blog::published()
                        ->latest()
                        ->select([
                            'id', 'title', 'slug', 'short_description',
                            'featured_image', 'author_name', 'published_at',
                            'meta_title', 'meta_description', 'meta_keywords'
                        ])
                        ->limit($limit)
                        ->get();

            // Transform the data
            $blogs->transform(function ($blog) {
                return [
                    'id' => $blog->id,
                    'title' => $blog->title,
                    'slug' => $blog->slug,
                    'excerpt' => $blog->excerpt,
                    'featured_image' => $blog->featured_image,
                    'author_name' => $blog->author_name ?: 'Admin',
                    'published_at' => $blog->published_at->format('M d, Y'),
                    'reading_time' => $blog->reading_time,
                    'meta' => [
                        'title' => $blog->meta_title ?: $blog->title,
                        'description' => $blog->meta_description ?: $blog->short_description,
                        'keywords' => $blog->meta_keywords,
                        'image' => $blog->featured_image,
                        'type' => 'article'
                    ]
                ];
            });

            return CustomFacade::successResponse("Latest blogs fetched successfully.", $blogs);
        } catch (Exception $e) {
            $message = $e->getMessage() ?? 'Something went wrong. Please try again.';
            return CustomFacade::errorResponse($message);
        }
    }
}

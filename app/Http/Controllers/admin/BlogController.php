<?php

namespace App\Http\Controllers\admin;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\ImageTrait;
use App\Models\Blog;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BlogController extends Controller
{
    use ImageTrait;

    // Blog page and datatable show
    public function index()
    {

        if (request()->ajax()) {
            $data = Blog::orderBy('created_at', 'desc')->get();

            return DataTables::of($data)->addIndexColumn()
                ->addColumn('image', function ($row) {
                    $imageUrl = $row->featured_image ?: url('default/default_blog.png');
                    return '<img src="' . $imageUrl . '" class="image-col img-rounded" style="width: 60px; height: 40px; object-fit: cover;">';
                })
                ->addColumn('title', function ($row) {
                    return '<div class="text-wrap" style="max-width: 250px;">' . $row->title . '</div>';
                })
                ->addColumn('excerpt', function ($row) {
                    return '<div class="text-wrap" style="max-width: 200px;">' . Str::limit($row->short_description ?: strip_tags($row->content), 100) . '</div>';
                })
                ->addColumn('author', function ($row) {
                    return $row->author_name ?: 'Admin';
                })
                ->addColumn('published_date', function ($row) {
                    return $row->published_at ? $row->published_at->format('M d, Y') : 'Not Published';
                })
                ->addColumn('status', function ($row) {
                    $class = "danger";
                    $status = Blog::$status[Blog::STATUS_INACTIVE];
                    if ($row->status == Blog::STATUS_ACTIVE) {
                        $class = "success";
                        $status = Blog::$status[Blog::STATUS_ACTIVE];
                    }
                    return '<button type="button" data-id="' . $row->id . '" data-title="blog" class="change-status-record btn btn-block btn-' . $class . '" data-toggle="tooltip" title="Click to change status">' . $status . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = "<a href='" . url('/admin/blog/' . $row->id . '/edit') . "' class='btn btn-primary btn-sm' title='Update'><i class='fa fa-edit white cicon'></i></a>
                                <a href='" . url('/admin/blog/' . $row->id) . "' class='btn btn-info btn-sm' title='View'><i class='fa fa-eye white cicon'></i></a>
                                <a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/blog/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
                    return $actionBtn;
                })
                ->rawColumns(['image', 'title', 'excerpt', 'status', 'action'])
                ->make(true);
        }

        return view('admin.pages.blog.index');
    }

    // Show blog details
    public function show($id)
    {
        try {
            $blog = Blog::with(['creator', 'updater'])->findOrFail($id);
            return view('admin.pages.blog.components.view', compact('blog'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Blog not found.');
        }
    }

    // Show create form
    public function create()
    {
        return view('admin.pages.blog.create');
    }

    // Store new blog
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author_name' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
            'published_at' => 'nullable|date',
        ]);

        try {
            $blogData = $request->only([
                'title', 'content', 'short_description', 'author_name',
                'meta_title', 'meta_description', 'meta_keywords', 'status'
            ]);

            // Generate slug
            $blogData['slug'] = Blog::generateUniqueSlug($request->title);

            // Handle featured image upload
            if ($request->file('featured_image') != null) {
                $blogData['featured_image'] = $this->imageUpload($request, 'featured_image', 'storage/image/blog');
            }            // Handle published date
            if ($request->published_at) {
                $blogData['published_at'] = Carbon::parse($request->published_at);
            } elseif ($request->status == Blog::STATUS_ACTIVE) {
                $blogData['published_at'] = now();
            }

            $blogData['created_by'] = auth()->id();

            $blog = Blog::create($blogData);

            return redirect()->route('blog.index')->with('success', 'Blog created successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error creating blog: ' . $e->getMessage())->withInput();
        }
    }

    // Show edit form
    public function edit($id)
    {
        try {
            $blog = Blog::findOrFail($id);
            return view('admin.pages.blog.edit', compact('blog'));
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Blog not found.');
        }
    }

    // Update blog
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'author_name' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:500',
            'status' => 'required|in:0,1',
            'published_at' => 'nullable|date',
        ]);

        try {
            $blog = Blog::findOrFail($id);

            $blogData = $request->only([
                'title', 'content', 'short_description', 'author_name',
                'meta_title', 'meta_description', 'meta_keywords', 'status'
            ]);

            // Update slug if title changed
            if ($blog->title !== $request->title) {
                $blogData['slug'] = Blog::generateUniqueSlug($request->title);
            }

            // Handle featured image upload
            if ($request->file('featured_image') != null) {
                $blogData['featured_image'] = $this->imageUpload($request, 'featured_image', 'storage/image/blog');
                if ($blog->featured_image) {
                    $this->imageDelete($blog->featured_image);
                }
            }            // Handle published date
            if ($request->published_at) {
                $blogData['published_at'] = Carbon::parse($request->published_at);
            } elseif ($request->status == Blog::STATUS_ACTIVE && !$blog->published_at) {
                $blogData['published_at'] = now();
            }

            $blogData['updated_by'] = auth()->id();

            $blog->update($blogData);

            return redirect()->route('blog.index')->with('success', 'Blog updated successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error updating blog: ' . $e->getMessage())->withInput();
        }
    }

    // Delete blog
    public function destroy($id)
    {
        try {
            $blog = Blog::findOrFail($id);

            // Delete featured image if exists
            if ($blog->featured_image) {
                $this->imageDelete($blog->featured_image);
            }

            $blog->delete();            return response()->json(['success' => true, 'message' => 'Blog deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting blog: ' . $e->getMessage()]);
        }
    }

    // Status update
    public function statusUpdate(Request $request)
    {
        try {
            $blog = Blog::findOrFail($request->id);
            $blog->status = $blog->status == Blog::STATUS_ACTIVE ? Blog::STATUS_INACTIVE : Blog::STATUS_ACTIVE;

            // Set published_at when activating
            if ($blog->status == Blog::STATUS_ACTIVE && !$blog->published_at) {
                $blog->published_at = now();
            }

            $blog->updated_by = auth()->id();
            $blog->save();

            return response()->json(['success' => true, 'message' => 'Blog status updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error updating status: ' . $e->getMessage()]);
        }
    }

    // Multiple delete
    public function multiple_delete(Request $request)
    {
        try {
            $ids = $request->ids;
            $blogs = Blog::whereIn('id', $ids)->get();

            foreach ($blogs as $blog) {
                if ($blog->featured_image) {
                    $this->imageDelete($blog->featured_image);
                }
            }            
            Blog::whereIn('id', $ids)->delete();

            return response()->json(['success' => true, 'message' => 'Selected blogs deleted successfully.']);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => 'Error deleting blogs: ' . $e->getMessage()]);
        }
    }
}

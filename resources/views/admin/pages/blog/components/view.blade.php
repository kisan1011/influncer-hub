@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Blog Details</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('blog.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Blogs
                        </a>
                        <a href="{{ route('blog.edit', $blog->id) }}" class="btn btn-primary">
                            <i class="fa fa-edit"></i> Edit Blog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ $blog->title }}</h3>
                            <div class="card-tools">
                                @if($blog->status == 1)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                        <div class="card-body">
                            @if($blog->featured_image)
                                <div class="mb-4">
                                    <img src="{{ $blog->featured_image }}" alt="{{ $blog->title }}"
                                         class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                                </div>
                            @endif

                            @if($blog->short_description)
                                <div class="mb-4">
                                    <h5>Short Description</h5>
                                    <p class="text-muted">{{ $blog->short_description }}</p>
                                </div>
                            @endif

                            <div class="blog-content">
                                <h5>Content</h5>
                                <div style="border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; background-color: #f8f9fa;">
                                    {!! $blog->content !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($blog->meta_title || $blog->meta_description || $blog->meta_keywords)
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">SEO Information</h3>
                            </div>
                            <div class="card-body">
                                @if($blog->meta_title)
                                    <div class="mb-3">
                                        <strong>Meta Title:</strong>
                                        <p>{{ $blog->meta_title }}</p>
                                    </div>
                                @endif

                                @if($blog->meta_description)
                                    <div class="mb-3">
                                        <strong>Meta Description:</strong>
                                        <p>{{ $blog->meta_description }}</p>
                                    </div>
                                @endif

                                @if($blog->meta_keywords)
                                    <div class="mb-3">
                                        <strong>Meta Keywords:</strong>
                                        <p>{{ $blog->meta_keywords }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Blog Information</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 40%">Status:</th>
                                    <td>
                                        @if($blog->status == 1)
                                            <span class="badge badge-success">Active</span>
                                        @else
                                            <span class="badge badge-secondary">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Author:</th>
                                    <td>{{ $blog->author_name ?: 'Admin' }}</td>
                                </tr>
                                <tr>
                                    <th>Published Date:</th>
                                    <td>{{ $blog->published_at ? $blog->published_at->format('M d, Y H:i') : 'Not Published' }}</td>
                                </tr>
                                <tr>
                                    <th>Created Date:</th>
                                    <td>{{ $blog->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Updated Date:</th>
                                    <td>{{ $blog->updated_at->format('M d, Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Slug:</th>
                                    <td><code>{{ $blog->slug }}</code></td>
                                </tr>
                                <tr>
                                    <th>Reading Time:</th>
                                    <td>{{ $blog->reading_time }}</td>
                                </tr>
                                @if($blog->creator)
                                    <tr>
                                        <th>Created By:</th>
                                        <td>{{ $blog->creator->name }}</td>
                                    </tr>
                                @endif
                                @if($blog->updater)
                                    <tr>
                                        <th>Updated By:</th>
                                        <td>{{ $blog->updater->name }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('footer_script')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        toastr.success('URL copied to clipboard!');
    }, function(err) {
        toastr.error('Failed to copy URL');
    });
}
</script>
@endsection

@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Edit Blog</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('blog.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Back to Blogs
                        </a>

                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <form action="{{ route('blog.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Blog Content</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" value="{{ old('title', $blog->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="short_description">Short Description</label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                              id="short_description" name="short_description" rows="3"
                                              placeholder="Brief summary of the blog post">{{ old('short_description', $blog->short_description) }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">This will be used as excerpt in blog listings.</small>
                                </div>

                                <div class="form-group">
                                    <label for="content">Content <span class="text-danger">*</span></label>
                                    <textarea class="form-control @error('content') is-invalid @enderror"
                                              id="content" name="content" rows="15" required>{{ old('content', $blog->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- SEO Section -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">SEO Settings</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="meta_title">Meta Title</label>
                                    <input type="text" class="form-control @error('meta_title') is-invalid @enderror"
                                           id="meta_title" name="meta_title" value="{{ old('meta_title', $blog->meta_title) }}">
                                    @error('meta_title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">If empty, blog title will be used.</small>
                                </div>

                                <div class="form-group">
                                    <label for="meta_description">Meta Description</label>
                                    <textarea class="form-control @error('meta_description') is-invalid @enderror"
                                              id="meta_description" name="meta_description" rows="3">{{ old('meta_description', $blog->meta_description) }}</textarea>
                                    @error('meta_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="meta_keywords">Meta Keywords</label>
                                    <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror"
                                           id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $blog->meta_keywords) }}"
                                           placeholder="keyword1, keyword2, keyword3">
                                    @error('meta_keywords')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Separate keywords with commas.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <!-- Publish Settings -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Publish Settings</h3>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                                        <option value="1" {{ old('status', $blog->status) == '1' ? 'selected' : '' }}>Active</option>
                                        <option value="0" {{ old('status', $blog->status) == '0' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="published_at">Publish Date</label>
                                    <input type="datetime-local" class="form-control @error('published_at') is-invalid @enderror"
                                           id="published_at" name="published_at"
                                           value="{{ old('published_at', $blog->published_at ? $blog->published_at->format('Y-m-d\TH:i') : '') }}">
                                    @error('published_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty to publish immediately when status is active.</small>
                                </div>

                                <div class="form-group">
                                    <label for="author_name">Author Name</label>
                                    <input type="text" class="form-control @error('author_name') is-invalid @enderror"
                                           id="author_name" name="author_name" value="{{ old('author_name', $blog->author_name) }}">
                                    @error('author_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Leave empty to use "Admin".</small>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Image -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">Featured Image</h3>
                            </div>
                            <div class="card-body">
                                @if($blog->featured_image)
                                    <div class="current-image mb-3">
                                        <label>Current Image:</label>
                                        <div>
                                            <img src="{{ $blog->featured_image }}" alt="Current featured image"
                                                 style="max-width: 100%; height: auto; border-radius: 5px;">
                                        </div>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label for="featured_image">Upload New Image</label>
                                    <input type="file" class="form-control-file @error('featured_image') is-invalid @enderror"
                                           id="featured_image" name="featured_image" accept="image/*">
                                    @error('featured_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Recommended size: 800x450 pixels. Leave empty to keep current image.</small>
                                </div>
                                <div id="image_preview" style="display: none;">
                                    <label>New Image Preview:</label>
                                    <img id="preview_img" src="" alt="Preview" style="max-width: 100%; height: auto; border-radius: 5px;">
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="card">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-save"></i> Update Blog
                                </button>
                                <a href="{{ route('blog.index') }}" class="btn btn-secondary btn-block">
                                    <i class="fa fa-times"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection

@push('page_scripts')
<script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor with enhanced upload support
    CKEDITOR.replace('content', {
        height: 400,
        // Enable upload-related plugins (uploadfile doesn't exist, removed it)
        extraPlugins: 'uploadimage,uploadwidget,clipboard',
        
        // File upload settings
        filebrowserUploadUrl: "{{ route('ckeditor.upload') }}?CKEditorFuncNum=1&_token={{ csrf_token() }}",
        filebrowserUploadMethod: 'form',
        
        // Image upload settings
        filebrowserImageUploadUrl: "{{ route('ckeditor.upload') }}?CKEditorFuncNum=1&_token={{ csrf_token() }}",
        
        // Upload URL for drag & drop and paste
        uploadUrl: "{{ route('ckeditor.upload') }}",
        
        // Image upload URL specifically for paste functionality
        imageUploadUrl: "{{ route('ckeditor.upload') }}",
        
        // Enhanced paste configuration
        pasteFromWordPromptCleanup: true,
        pasteFromWordCleanupFile: '',
        
        // Allow all content for paste
        allowedContent: true,
        
        // Enhanced clipboard settings
        clipboard_handleImages: true,
        
        // Simplified toolbar for better UX
        toolbar: [
            { name: 'clipboard', items: [ 'Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo' ] },
            { name: 'editing', items: [ 'Find', 'Replace' ] },
            '/',
            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat' ] },
            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
            { name: 'links', items: [ 'Link', 'Unlink' ] },
            { name: 'insert', items: [ 'Image', 'Table', 'HorizontalRule' ] },
            '/',
            { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
            { name: 'colors', items: [ 'TextColor', 'BGColor' ] },
            { name: 'tools', items: [ 'Maximize', 'Source' ] }
        ],
        
        // Event handlers for upload
        on: {
            // Handle file upload requests
            fileUploadRequest: function(evt) {
                var xhr = evt.data.fileLoader.xhr;
                xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
            },
            
            // Handle paste events
            paste: function(evt) {
                // Let CKEditor handle the paste naturally
                console.log('Paste event triggered');
            },
            
            // Instance ready
            instanceReady: function(evt) {
                console.log('CKEditor is ready');
                
                // Enable drag and drop
                this.on('dragstart', function(evt) {
                    console.log('Drag started');
                });
                
                this.on('drop', function(evt) {
                    console.log('Drop detected');
                });
            }
        }
    });

    // Image preview for featured image
    $('#featured_image').on('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview_img').attr('src', e.target.result);
                $('#image_preview').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#image_preview').hide();
        }
    });
});
</script>
@endpush

@extends('admin.layouts.master')
@section('content')
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0"> Blog Management </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        <a href="{{ route('blog.create') }}" class="btn btn-block btn-primary">
                            <i class="fa fa-plus"></i> Add New Blog
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-checkable" id="blog-show">
                                    <thead>
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="check_all_record" name="select_all" value="1">
                                            </th>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Excerpt</th>
                                            <th>Author</th>
                                            <th>Published Date</th>
                                            <th class="status-col">Status</th>
                                            <th class="action-col">Action</th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-danger" id="multiple_delete_blog_btn" data-title="blog" disabled>
                                Delete selected
                                <span style="display: none" id="multiple_delete_loader">
                                    <i class="fa fa-spinner fa-spin" aria-hidden="true"></i>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('page_scripts')
<script>
$(document).ready(function() {
    var blogTable = $('#blog-show').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('blog.index') }}",
            type: 'GET'
        },
        columns: [
            {
                data: 'id',
                name: 'id',
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="record_checkbox" name="ids[]" value="' + data + '">';
                }
            },
            {data: 'image', name: 'image', orderable: false, searchable: false},
            {data: 'title', name: 'title'},
            {data: 'excerpt', name: 'excerpt', orderable: false, searchable: false},
            {data: 'author', name: 'author', orderable: false},
            {data: 'published_date', name: 'published_at'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'action', name: 'action', orderable: false, searchable: false}
        ],
        order: [[5, 'desc']],
        pageLength: 25,
        responsive: true
    });

    // Handle status change
    $(document).on('click', '.change-status-record', function() {
        var id = $(this).data('id');
        var title = $(this).data('title');

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $.ajax({
            url: "{{ url('/admin/blog/status-update') }}",
            type: 'POST',
            data: {
                id: id
            },
            success: function(response) {
                if (response.success) {
                    blogTable.ajax.reload();
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                }
            },
            error: function() {
                toastr.error('Something went wrong!');
            }
        });
    });

    // Handle single delete
    $(document).on('click', '.delete_data', function() {
        var url = $(this).data('action');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: url,
                    type: 'DELETE',
                    success: function(response) {
                        if (response.success) {
                            blogTable.ajax.reload();
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });
    });

    // Handle multiple delete
    $('#multiple_delete_blog_btn').on('click', function() {
        var ids = [];
        $('.record_checkbox:checked').each(function() {
            ids.push($(this).val());
        });

        if (ids.length === 0) {
            toastr.warning('Please select at least one record to delete.');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "You want to delete " + ids.length + " selected blog(s)?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete them!'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#multiple_delete_loader').show();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $.ajax({
                    url: "{{ url('/admin/delete-blog') }}",
                    type: 'POST',
                    data: {
                        ids: ids
                    },
                    success: function(response) {
                        $('#multiple_delete_loader').hide();
                        if (response.success) {
                            blogTable.ajax.reload();
                            $('#check_all_record').prop('checked', false);
                            $('#multiple_delete_blog_btn').prop('disabled', true);
                            toastr.success(response.message);
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        $('#multiple_delete_loader').hide();
                        toastr.error('Something went wrong!');
                    }
                });
            }
        });
    });

    // Handle check all
    $('#check_all_record').on('change', function() {
        if ($(this).is(':checked')) {
            $('.record_checkbox').prop('checked', true);
        } else {
            $('.record_checkbox').prop('checked', false);
        }
        toggleDeleteButton();
    });

    // Handle individual checkbox
    $(document).on('change', '.record_checkbox', function() {
        toggleDeleteButton();
    });

    function toggleDeleteButton() {
        var checkedCount = $('.record_checkbox:checked').length;
        if (checkedCount > 0) {
            $('#multiple_delete_blog_btn').prop('disabled', false);
        } else {
            $('#multiple_delete_blog_btn').prop('disabled', true);
            $('#check_all_record').prop('checked', false);
        }
    }
});
</script>
@endpush

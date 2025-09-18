<?php

namespace App\Http\Controllers\admin;

use Exception;
use App\Facade\CustomFacade;
use Illuminate\Http\Request;
use App\Http\Traits\ImageTrait;
use App\Models\ContentCategory;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\ContentCategoryRequest;

class ContentCategoryController extends Controller
{
    use ImageTrait;

    //content category page and datatable show
    public function index()
    {
        if (request()->ajax()) {
            $data = ContentCategory::orderBy('created_at', 'desc')->get();
            return DataTables::of($data)->addIndexColumn()
              ->addColumn('type', function ($row) {
                if($row->type == 0){
                  return 'YouTube';
                } else {
                  return 'Instagram';
                }
              })
                ->addColumn('image', function ($row) {
                    return '<img src="' . $row->logo . '" class="image-col img-rounded">';
                })
                ->addColumn('status', function ($row) {
                    $class = "danger";
                    $status = ContentCategory::$status[ContentCategory::STATUS_INACTIVE];
                    if ($row->status == ContentCategory::STATUS_ACTIVE) {
                        $class = "success";
                        $status = ContentCategory::$status[ContentCategory::STATUS_ACTIVE];
                    }
                    return '<button type="button" data-id = "' . $row->id . '" data-title="content category" class="change-status-record btn btn-block btn-' . $class . '" data-toggle="tooltip" title="Click to change status">' . $status . '</button>';
                })
                ->addColumn('action', function ($row) {
                    $actionBtn = "<a href='javascript:void(0)' class='btn btn-primary btn-sm edit_form' data-action='" . url('/admin/content-category/' . $row->id . '/edit') . "' title='Update'><i class='fa fa-edit white cicon'></i></a>
                        <a href='javascript:void(0)' class='btn btn-info btn-sm show_details' data-action='" . url('/admin/content-category/' . $row->id) . "' title='View'><i class='fa fa-eye white cicon'></i></a>
                        <a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/content-category/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
                    return $actionBtn;
                })
                ->rawColumns(['image', 'status', 'action'])
                ->make(true);
        }
        return view('admin.pages.content-category.index');
    }

    // content category data store
    public function store(ContentCategoryRequest $request)
    {
        $records = [];
        $records['name'] = $request->name;
        $records['type'] = $request->type;
        if ($request->file('logo') != null) {
            $records['logo'] = $this->imageUpload($request, 'logo', 'storage/image/content-category');
            if ($request->has('id') && $request->id != null) {
                $fetchCategory = ContentCategory::select('logo')->find($request->id);
                $this->imageDelete($fetchCategory->logo);
            }
        }
        $contentCategory = ContentCategory::updateOrCreate(['id' => $request->id], $records);
        if (!$contentCategory) {
            return CustomFacade::errorResponse("Something went wrong. Please try again.");
        }
        $message = isset($request->id) ? "Content category update successfully." : "Content category create successfully.";
        return CustomFacade::successResponse($message);
    }

    // Content category details show
    public function show($id)
    {
        $contentCategory = ContentCategory::find($id);
        return view('admin.pages.content-category.components.view', compact('contentCategory'));
    }

    // Category update form
    public function edit($id)
    {
        $contentCategory = ContentCategory::select('id', 'name', 'logo', 'type')->find($id);
        if (!$contentCategory) {
            return CustomFacade::errorResponse("Content category not found.");
        }
        return CustomFacade::successResponse("Content category data fetch successfully.", $contentCategory);
    }

    // Single content category delete
    public function destroy($id)
    {
        $fetchContentCat = ContentCategory::with('channelContentCategory')->find($id);
        if (!$fetchContentCat) {
            return CustomFacade::errorResponse("Content category not found.");
        }
        if(count($fetchContentCat->channelContentCategory) > 0){
            return CustomFacade::errorResponse("You can't delete content category, content category is associated influencer.");
        }
        $this->imageDelete($fetchContentCat->logo);
        $fetchContentCat->delete();
        return CustomFacade::successResponse("Content category delete successfully");
    }

    // Multiple content category delete
    public function multiple_delete(Request $request)
    {
        try {
            $fetchContentCats = ContentCategory::with('channelContentCategory')->whereIn('id', $request->ids)->get();
            if ($fetchContentCats) {
                foreach ($fetchContentCats as $fetchContentCat) {
                    if(count($fetchContentCat->channelContentCategory) > 0){
                        return CustomFacade::errorResponse("You can't delete content category, content category is associated influencer.");
                    }
                    $this->imageDelete($fetchContentCat->logo);
                    $fetchContentCat->delete();
                }
            }
            return CustomFacade::successResponse("Content category deleted successfully.");
        } catch (Exception $e) {
            $message = $e->getMessage() ?? "Something went wrong. Please try again.";
            return CustomFacade::errorResponse($message);
        }
    }

    // Content category status update
    public function statusUpdate(Request $request)
    {
        $fetchContentCat = ContentCategory::find($request->id);
        if (!$fetchContentCat) {
            return CustomFacade::errorResponse("Content category not found.");
        }
        $fetchContentCat->status = $fetchContentCat->status == ContentCategory::STATUS_ACTIVE ? ContentCategory::STATUS_INACTIVE : ContentCategory::STATUS_ACTIVE;
        $fetchContentCat->save();
        return CustomFacade::successResponse("Status update succesfully.");
    }
}

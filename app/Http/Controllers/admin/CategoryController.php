<?php

namespace App\Http\Controllers\admin;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CategoryRequest;
use App\Http\Traits\ImageTrait;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

class CategoryController extends Controller
{
  use ImageTrait;

  // category page and datatable show
  public function index()
  {
    if (request()->ajax()) {
      $data = Category::orderBy('created_at','desc')->get();
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
          $status = Category::$status[Category::STATUS_INACTIVE];
          if ($row->status == Category::STATUS_ACTIVE) {
            $class = "success";
            $status = Category::$status[Category::STATUS_ACTIVE];
          }
          return '<button type="button" data-id = "' . $row->id . '" data-title="category" class="change-status-record btn btn-block btn-' . $class . '" data-toggle="tooltip" title="Click to change status">' . $status . '</button>';
        })
        ->addColumn('action', function ($row) {
          $actionBtn = "<a href='javascript:void(0)' class='btn btn-primary btn-sm edit_form' data-action='" . url('/admin/category/' . $row->id . '/edit') . "' title='Update'><i class='fa fa-edit white cicon'></i></a>
                    <a href='javascript:void(0)' class='btn btn-info btn-sm show_details' data-action='" . url('/admin/category/' . $row->id) . "' title='View'><i class='fa fa-eye white cicon'></i></a>
                    <a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/category/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
          return $actionBtn;
        })
        ->rawColumns(['image', 'status', 'action'])
        ->make(true);
    }
    return view('admin.pages.category.index');
  }

  // category data store
  public function store(CategoryRequest $request)
  {
    $records = [];
    $records['name'] = $request->name;
    $records['type'] = $request->type;
    if ($request->file('logo') != null) {
      $records['logo'] = $this->imageUpload($request, 'logo', 'storage/image/category');
      if ($request->has('id') && $request->id != null) {
        $fetchCategory = Category::select('logo')->find($request->id);
        $this->imageDelete($fetchCategory->logo);
      }
    }
    $category = Category::updateOrCreate(['id' => $request->id], $records);
    if (!$category) {
      return CustomFacade::errorResponse("Something went wrong. Please try again.");
    }
    $message = isset($request->id) ? "Category update successfully." : "Category create successfully.";
    return CustomFacade::successResponse($message);
  }

  // Category details show
  public function show($id)
  {
    $category = Category::find($id);
    return view('admin.pages.category.components.view', compact('category'));
  }

  // Category update form
  public function edit($id)
  {
    $category = Category::select('id','name','logo', 'type')->find($id);
    if(!$category){
        return CustomFacade::errorResponse("Category not found.");
    }
    return CustomFacade::successResponse("Category data fetch successfully.",$category);
  }

  // Single category delete
  public function destroy($id)
  {
    $fetchCategory = Category::with('channel')->find($id);
    if (!$fetchCategory) {
      return CustomFacade::errorResponse("Category not found.");
    }
    if($fetchCategory->channel != null){
      return CustomFacade::errorResponse("You can't delete category, category is associated influencer.");
    }
    $this->imageDelete($fetchCategory->logo);
    $fetchCategory->delete();
    return CustomFacade::successResponse("Category delete successfully");
  }

  // Multiple category delete
  public function multiple_delete(Request $request)
  {
    try {
      $fetchCategorys = Category::with('channel')->whereIn('id', $request->ids)->get();
      if($fetchCategorys){
        foreach ($fetchCategorys as $fetchCategory) {
          if($fetchCategory->channel != null){
            return CustomFacade::errorResponse("You can't delete category, category is associated influencer.");
          }
          $this->imageDelete($fetchCategory->logo);
          $fetchCategory->delete();
        }
      }
      return CustomFacade::successResponse("Category deleted successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? "Something went wrong. Please try again.";
      return CustomFacade::errorResponse($message);
    }
  }

  // Category status update
  public function statusUpdate(Request $request)
  {
    $fetchCategory = Category::find($request->id);
    if (!$fetchCategory) {
      return CustomFacade::errorResponse("Category not found.");
    }
    $fetchCategory->status = $fetchCategory->status == Category::STATUS_ACTIVE ? Category::STATUS_INACTIVE : Category::STATUS_ACTIVE;
    $fetchCategory->save();
    return CustomFacade::successResponse("Status update succesfully.");
  }
}

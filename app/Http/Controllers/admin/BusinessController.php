<?php

namespace App\Http\Controllers\admin;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\ImageTrait;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class BusinessController extends Controller
{
  use ImageTrait;

  // Business list load
  public function index()
  {
    if (request()->ajax()) {
      $data = User::where('role_id', User::ROLE_BUSINESS)->orderBy('created_at','desc')->get();
      return DataTables::of($data)->addIndexColumn()
        ->addColumn('profile', function ($row) {
          return '<img src="' . $row->profile . '" class="image-col img-rounded">';
        })
        ->addColumn('name', function ($row) {
          return $row->name;
        })
        ->addColumn('email', function ($row) {
          return $row->email;
        })
        ->addColumn('type', function ($row) {
          $className = ($row->type == User::TYPE_EMAIL) ? 'info' : 'danger';
          $provider = ($row->type == User::TYPE_EMAIL) ? 'Email' : 'Google';
          return '<span class="badge badge-' . $className . '">' . $provider . '</span>';
        })
        ->addColumn('verified', function ($row) {
          $className = ($row->email_verified_at != null || $row->email_verified_at != '') ? 'success' : 'danger';
          $title = ($row->email_verified_at != null || $row->email_verified_at != '') ? 'Verified' : 'Not verified';
          return '<span class="badge badge-' . $className . '">' . $title . '</span>';
        })
        ->addColumn('status', function ($row) {
          $class = "danger";
          $status = User::$status[User::STATUS_INACTIVE];
          if ($row->status == User::STATUS_ACTIVE) {
            $class = "success";
            $status = User::$status[User::STATUS_ACTIVE];
          }
          return '<button type="button" data-id = "' . $row->id . '" data-title="business" class="change-status-record btn btn-block btn-' . $class . '" data-toggle="tooltip" title="Click to change status">' . $status . '</button>';
        })
        ->addColumn('action', function ($row) {
          $actionBtn = "<a href='javascript:void(0)' class='btn btn-info btn-sm show_details' data-action='" . url('/admin/business/' . $row->id) . "' title='View'><i class='fa fa-eye white cicon'></i></a>
                        <a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/business/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
          return $actionBtn;
        })
        ->rawColumns(['profile', 'name', 'email', 'type' , 'verified', 'status', 'action'])
        ->make(true);
    }
    return view('admin.pages.business.index');
  }

  // Show business details
  public function show($id)
  {
    $user = User::with('role')->find($id);
    return view('admin.pages.business.components.view', compact('user'));
  }

  // Single business delete
  public function destroy($id)
  {
    $fetchBusiness = User::find($id);
    if (!$fetchBusiness) {
      return CustomFacade::errorResponse("Business not found.");
    }
    $this->imageDelete($fetchBusiness->profile);
    $fetchBusiness->delete();
    return CustomFacade::successResponse("Business delete successfully.");
  }

  // Business status update
  public function statusUpdate(Request $request)
  {
    $fetchBusiness = User::find($request->id);
    if (!$fetchBusiness) {
      return CustomFacade::errorResponse("Business not found.");
    }
    $fetchBusiness->status = $fetchBusiness->status == User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
    $fetchBusiness->save();
    return CustomFacade::successResponse("Status update succesfully.");
  }

  // Multiple business delete
  public function multiple_delete(Request $request)
  {
    try {
      $fetchBusiness = User::whereIn('id', $request->ids)->get();
      foreach ($fetchBusiness as $business) {
        $this->imageDelete($business->profile);
        $business->delete();
      }
      return CustomFacade::successResponse("Business deleted successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? "Something went wrong. Please try again.";
      return CustomFacade::errorResponse($message);
    }
  }
}

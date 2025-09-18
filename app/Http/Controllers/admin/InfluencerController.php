<?php

namespace App\Http\Controllers\admin;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Traits\ImageTrait;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Exception;

class InfluencerController extends Controller
{
  use ImageTrait;

  // Influencer list load
  public function index()
  {
    if (request()->ajax()) {
      $data = User::where('role_id', User::ROLE_INFLUENCER)->orderBy('created_at','desc')->get();
      return DataTables::of($data)->addIndexColumn()
        ->addColumn('profile', function ($row) {
          return '<img src="' . $row->profile . '" class="image-col img-rounded">';
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
        ->addColumn('plan_expire', function ($row) {
          if($row->currentPlan){
            return Carbon::parse($row->currentPlan['ends_at'])->format('d-m-Y h:i A');
          }
          return "N/A";
        })
        ->addColumn('status', function ($row) {
          $class = "danger";
          $status = User::$status[User::STATUS_INACTIVE];
          if ($row->status == User::STATUS_ACTIVE) {
            $class = "success";
            $status = User::$status[User::STATUS_ACTIVE];
          }
          return '<button type="button" data-id = "' . $row->id . '" data-title="influencer" class="change-status-record btn btn-block btn-' . $class . '" data-toggle="tooltip" title="Click to change status">' . $status . '</button>';
        })
        ->addColumn('action', function ($row) {
          $actionBtn = "<a href='javascript:void(0)' class='btn btn-info btn-sm show_details' data-action='" . url('/admin/influencer/' . $row->id) . "' title='View'><i class='fa fa-eye white cicon'></i></a>
                      <a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/influencer/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
          return $actionBtn;
        })
        ->rawColumns(['profile', 'email', 'type', 'verified', 'plan_expire', 'status', 'action'])
        ->make(true);
    }
    return view('admin.pages.influencer.index');
  }

  // Show influencer details
  public function show($id)
  {
    $user = User::with('role','channels.countries','channels.category','channels.audio')->find($id);
    return view('admin.pages.influencer.components.view', compact('user'));
  }

  // Single influencer delete
  public function destroy($id)
  {
    $fetchInfluencer = User::with('channels')->find($id);
    if (!$fetchInfluencer) {
      return CustomFacade::errorResponse("Influencer not found.");
    }elseif ($fetchInfluencer->channels->isNotEmpty()) {
      return CustomFacade::errorResponse($fetchInfluencer->name." has a channels. First delete channels.");
    }
    $this->imageDelete($fetchInfluencer->profile);
    $fetchInfluencer->delete();
    return CustomFacade::successResponse("Influencer delete successfully");
  }

  // Influencer status update
  public function statusUpdate(Request $request)
  {
    $fetchInfluencer = User::find($request->id);
    if (!$fetchInfluencer) {
      return CustomFacade::errorResponse("Influencer not found.");
    }
    $fetchInfluencer->status = $fetchInfluencer->status == User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
    $fetchInfluencer->save();
    return CustomFacade::successResponse("Status update succesfully.");
  }

  // Multiple influencer delete
  public function multiple_delete(Request $request)
  {
    try {
      $fetchInfluencer = User::with('channels')->whereIn('id', $request->ids)->get();
      foreach ($fetchInfluencer as $influencer) {
        if ($influencer->channels->isNotEmpty()) {
          return CustomFacade::errorResponse($influencer->name." has a channels. First delete channels.");
        }
        $this->imageDelete($influencer->profile);
        $influencer->delete();
      }
      return CustomFacade::successResponse("Influencer deleted successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? "Something went wrong. Please try again.";
      return CustomFacade::errorResponse($message);
    }
  }
}

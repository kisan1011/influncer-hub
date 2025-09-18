<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use Illuminate\Http\Request;
use App\Http\Traits\ImageTrait;
use App\Models\User;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;
use Exception;

class InfluencerNetworksController extends Controller
{
  public function index(Request $request, $type)
  {
    $typeVal = ($type == 'instagram') ? 1 : 0;
    if (request()->ajax()) {
      $data = Channel::with(['user', 'category', 'contentCategory.contentCategoryDetails', 'chanelDataUpdateRequest'])->where('type', $typeVal)->orderBy('created_at','desc')->get();
      return DataTables::of($data)->addIndexColumn()
        ->addColumn('username', function ($row) {
            return $row->user->name;
        })
        ->addColumn('email', function ($row) {
          return $row->email;
        })
        ->addColumn('thumbnail', function ($row) {
          return '<img src="' . $row->thumbnail . '" class="image-col img-rounded">';
        })
        ->addColumn('channel_name', function ($row) {
          return $row->channel_name;
        })
        ->addColumn('insta_username', function ($row) {
          return $row->username;
        })
        ->addColumn('category', function ($row) {
          return $row->category->name;
        })
        ->addColumn('content_category', function ($row) {
          $categoryNames = [];
          foreach ($row->contentCategory as $contentCategory) {
              $details = $contentCategory->contentCategoryDetails;
              if ($details->isNotEmpty()) {
                  $categoryNames = array_merge($categoryNames, $details->pluck('name')->toArray());
              }
          }
          return !empty($categoryNames) ? implode(', ', $categoryNames) : '';
        })
        ->addColumn('subscriber_or_followers_count', function ($row) use($type) {
          if($type == 'instagram'){
          return $row->followers_count;
          } else {
          return $row->subscriber_count;
          }
        })
        ->addColumn('view_or_follows_count', function ($row) use($type) {
          if($type == 'instagram'){
            return $row->follows_count;
           } else {
            return $row->view_count;
           }
        })
        ->addColumn('video_or_media_count', function ($row) use($type) {
          if($type == 'instagram'){
            return $row->media_count;
           } else {
            return $row->video_count;
           }
        })
        ->addColumn('is_verified', function ($row) {
          $className = ($row->is_verified == 1) ? 'success' : 'danger';
          $title = ($row->is_verified == 1) ? 'Verified' : 'Not verified';
          return '<span class="badge badge-' . $className . '">' . $title . '</span>';
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
          $actionBtn = "<a href='javascript:void(0)' class='btn btn-info btn-sm show_details' data-action='" . url('/admin/influencer-network/show/' . $row->id) . "' title='View'><i class='fa fa-eye white cicon'></i></a>
                      <a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/influencer-network/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
          return $actionBtn;
        })

        ->rawColumns(['thumbnail','is_verified','status', 'action'])
        ->make(true);
    }
    return view('admin.pages.influencer-network.index', ['type' => $type]);
  }

  public function showDetails($id)
  {
    $details = Channel::with(['user', 'category', 'contentCategory.contentCategoryDetails', 'chanelDataUpdateRequest'])->find($id);

    $countryArray = collect($details->countries)->pluck('name')->toArray();
      unset($details->countries);
      $country = implode(', ', $countryArray);
      $details->country = $country;

      if($details->video_length != "" ){
        $videoLengthArray = explode(':',$details->video_length);
        $details->display_video_length = $videoLengthArray[0]." Min ".$videoLengthArray[1]." Sec";
      }else{
        $details->display_video_length = "";
      }
      $details->upload_time = Channel::$upload[$details->upload_time];

    return view('admin.pages.influencer-network.components.view', compact('details'));
  }

}

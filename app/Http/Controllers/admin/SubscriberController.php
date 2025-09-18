<?php

namespace App\Http\Controllers\admin;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Models\Subscribe;
use Exception;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class SubscriberController extends Controller
{
  // Subscriber page and list
  public function index()
  {
    if (request()->ajax()) {
      $data = Subscribe::get();
      return DataTables::of($data)->addIndexColumn()
        ->addColumn('email', function ($row) {
          return $row->email;
        })
        ->addColumn('status', function ($row) {
          $class = "danger";
          $status = Subscribe::$status[Subscribe::STATUS_UNSUBSCRIBE];
          if ($row->status == Subscribe::STATUS_SUBSCRIBE) {
            $class = "success";
            $status = Subscribe::$status[Subscribe::STATUS_SUBSCRIBE];
          }
          return '<button type="button" data-id = "' . $row->id . '" data-title="subscriber" class="change-status-record btn btn-block btn-' . $class . '" data-toggle="tooltip" title="Click to change status">' . $status . '</button>';
        })
        ->addColumn('action', function ($row) {
          $actionBtn = "<a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/subscriber/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
          return $actionBtn;
        })
        ->rawColumns(['email', 'status', 'action'])
        ->make(true);
    }
    return view('admin.pages.subscriber.index');
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    //
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    //
  }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    //
  }

  // Single subscriber delete
  public function destroy($id)
  {
    $fetchSubscriber = Subscribe::find($id);
    if(!$fetchSubscriber){
      return CustomFacade::errorResponse("Subscriber not found.");
    }
    $fetchSubscriber->delete();
    return CustomFacade::successResponse("Subscriber delete successfully.");
  }

   // Multiple subscriber delete
   public function multiple_delete(Request $request)
   {
     try {
       $fetchSubscriber = Subscribe::whereIn('id', $request->ids)->get();
       if($fetchSubscriber){
         foreach ($fetchSubscriber as $subscriber) {
           $subscriber->delete();
         }
       }
       return CustomFacade::successResponse("Subscriber deleted successfully.");
     } catch (Exception $e) {
       $message = $e->getMessage() ?? "Something went wrong. Please try again.";
       return CustomFacade::errorResponse($message);
     }
   }

  // Subscriber status update
  public function statusUpdate(Request $request)
  {
    $fetchSubscriber = Subscribe::find($request->id);
    if (!$fetchSubscriber) {
      return CustomFacade::errorResponse("Subscriber not found.");
    }
    $fetchSubscriber->status = $fetchSubscriber->status == Subscribe::STATUS_SUBSCRIBE ? Subscribe::STATUS_UNSUBSCRIBE : Subscribe::STATUS_SUBSCRIBE;
    $fetchSubscriber->save();
    return CustomFacade::successResponse("Status update succesfully.");
  }
}

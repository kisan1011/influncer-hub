<?php

namespace App\Http\Controllers\admin;

use Exception;
use App\Models\ContactUs;
use App\Facade\CustomFacade;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class ContactUsController extends Controller
{
    // contact us page and list
  public function index()
  {
    if (request()->ajax()) {
      $data = ContactUs::orderBy('id','DESC')->get();
      return DataTables::of($data)->addIndexColumn()
        ->addColumn('fullname', function ($row) {
          return $row->fullname;
        })
        ->addColumn('email', function ($row) {
            return $row->email;
        })
        ->addColumn('phone', function ($row) {
            return $row->phone;
        })
        ->addColumn('message', function ($row) {
            return $row->message;
        })
        ->addColumn('action', function ($row) {
            $actionBtn = "<a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/contact-us/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
            return $actionBtn;
        })
        ->rawColumns(['fullname', 'email', 'phone', 'message', 'action'])
        ->make(true);
    }
    return view('admin.pages.contact_us.index');
  }

  // Single contact us delete
  public function destroy($id)
  {
    $fetchCategory = ContactUs::find($id);
    if (!$fetchCategory) {
      return CustomFacade::errorResponse("Contact Us not found.");
    }
    $fetchCategory->delete();
    return CustomFacade::successResponse("Contact Us deleted successfully.");
  }

  // Multiple contact us delete
  public function multiple_delete(Request $request)
  {
    try {
      $fetchContactUs = ContactUs::whereIn('id', $request->ids)->get();
      if($fetchContactUs){
        foreach ($fetchContactUs as $data) {
          $data->delete();
        }
      }
      return CustomFacade::successResponse("Contact Us deleted successfully.");
    } catch (Exception $e) {
      $message = $e->getMessage() ?? "Something went wrong. Please try again.";
      return CustomFacade::errorResponse($message);
    }
  }

}

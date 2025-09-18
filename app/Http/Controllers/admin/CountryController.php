<?php

namespace App\Http\Controllers\admin;

use Exception;
use App\Models\Country;
use App\Facade\CustomFacade;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\CountryRequest;

class CountryController extends Controller
{
    public function index()
    {
        if (request()->ajax()) {
        $data = Country::orderBy('created_at','desc')->get();
        return DataTables::of($data)->addIndexColumn()
            ->addColumn('action', function ($row) {
            $actionBtn = "<a href='javascript:void(0)' class='btn btn-danger btn-sm delete_data' data-action='" . url('/admin/country/' . $row->id) . "' title='Delete'><i class='fa fa-trash white cicon'></i></a>";
            return $actionBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
        }
        return view('admin.pages.country.index');
    }

    // country data store
    public function store(CountryRequest $request)
    {
        $records = [];
        $records['name'] = $request->name;
        $records['code'] = $request->code;
        $country = Country::updateOrCreate(['id' => $request->id], $records);
        if (!$country) {
        return CustomFacade::errorResponse("Something went wrong. Please try again.");
        }
        $message = "Country create successfully.";
        return CustomFacade::successResponse($message);
    }

    // Single Country delete
    public function destroy($id)
    {
        $fetchCountry = Country::find($id);
        if (!$fetchCountry) {
            return CustomFacade::errorResponse("Country not found.");
        }
        $fetchCountry->delete();
        return CustomFacade::successResponse("Country delete successfully");
    }

    // Multiple Country delete
    public function multiple_delete(Request $request)
    {
        try {
            $fetchCountrys = Country::whereIn('id', $request->ids)->get();
            if($fetchCountrys){
                foreach ($fetchCountrys as $fetchCountry) {
                    $fetchCountry->delete();
                }
            }
            return CustomFacade::successResponse("Country deleted successfully.");
        } catch (Exception $e) {
            $message = $e->getMessage() ?? "Something went wrong. Please try again.";
            return CustomFacade::errorResponse($message);
        }
    }
}

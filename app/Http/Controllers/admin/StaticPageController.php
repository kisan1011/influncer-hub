<?php

namespace App\Http\Controllers\admin;

use App\Facade\CustomFacade;
use App\Http\Controllers\Controller;
use App\Http\Requests\admin\StaticPageRequest;
use App\Models\Staticpage;
use App\Models\User;

class StaticPageController extends Controller
{
  // Static page
  public function static()
  {
    $role = request()->segment(count(request()->segments()) - 1);
    $page = last(request()->segments());
    $typeArray = [
      'terms-condition' => Staticpage::TYPE_TERMS,
      'privacy-policy' => Staticpage::TYPE_PRIVACY,
      'data-safety' => Staticpage::TYPE_SAFETY,
      'refund-policy' => Staticpage::TYPE_REFUND,
      'disclaimer' => Staticpage::TYPE_DISCLAIMER,
      'dmca-policy' => Staticpage::TYPE_DMCA_POLICY,
      'cookie-consent' => Staticpage::TYPE_COOKIE_CONSENT,
      'about-us' => Staticpage::TYPE_ABOUT_US,
    ];
    $type = $typeArray[$page];
    $role_id = $role == "influencer" ? User::ROLE_INFLUENCER : User::ROLE_BUSINESS;
    $pageData = Staticpage::where('role_id', $role_id)->where('type', $typeArray[$page])->first();
    return view('admin.pages.static.index', compact('role_id', 'page', 'type', 'pageData'));
  }

  // Static page data store
  public function store(StaticPageRequest $request)
  {
    $StaticData = $request->only('role_id', 'type', 'description');
    Staticpage::updateOrCreate(['role_id' => $request->role_id, 'type' => $request->type], $StaticData);
    return CustomFacade::successResponse(ucfirst($request->type) . " update successfully.");
  }
}

<?php

namespace App\Facade;

use Illuminate\Support\Facades\Facade;

class CustomFacade extends Facade
{

  public static function successResponse($message, $data = null)
  {
    $dataArray = [
      'status' => true,
      'message' => $message,
    ];
    if (isset($data) && !is_null($data) && !empty($data)) {
      $dataArray['data'] = $data;
    }
    return response()->json($dataArray, 200);
  }

  public static function errorResponse($message, $data = [])
  {
    $dataArray = [
      'status' => false,
      'message' => $message,
    ];
    if (isset($data) && !is_null($data) && !empty($data)) {
      $dataArray['data'] = $data;
    }
    return response()->json($dataArray, 200);
  }

  public static function validatorError($validate)
  {
    return response()->json(
      [
        'status' => false,
        'message' => $validate->getMessageBag()->first()
      ],
      200
    );
  }

  public static function loginAndSignupSuccess($message, $tokenBody, $data = null)
  {
    $response = array_merge($tokenBody, [
        'status' => true,
        'message' => $message,
    ]);
    if (isset($data)) {
      $response = array_merge($response, [
        'data' =>$data,
      ]);
    }
    return response()->json($response,  200);
  }
}

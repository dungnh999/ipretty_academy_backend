<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{
  public function sendResponse($result, $message)
  {
    return Response::json(ResponseUtil::makeResponse($message, $result));
  }

  public function sendError($error, $code = 404, $data = [])
  {
    return Response::json(ResponseUtil::makeError($error, $data, $code), 200);
  }

  public function sendSuccess($message, $result = [])
  {
    return Response::json([
      'success' => true,
      'message' => $message,
      'data' => $result,
      "status" => 200
    ], 200);

  }

  public function prepareResponseApi($response)
  {
    if (!isApiResponseSuccess($response)) {
      if (isset($response['code']) && $response['code'] == 'session.not_found') {
        session()->flush();
        return [
          'success' => false,
          'message' => __('argame/messages.un_authenticated')
        ];
      }
    }

    return $response;
  }

  public function sendErrorTokenNotFound($error, $code = 422)
  {
    return Response::json([
      'success' => false,
      'code' => "",
      'message' => $error,
      "status_code" => $code
    ]);
  }

  public function getErrorMessage($response)
  {
    if (count($response['errors']) > 0) {
      foreach ($response['errors'] as $key => $value) {
        $message = __('messages.input_error') . ' : ' . $value[0];
        return $message;
      }
    }
    return isset($response['message']) ? $response['message'] : 'Inputs error!';
  }

  public function sendResponseWithError($result, $message, $error)
  {
    return Response::json([
      'success' => true,
      'data' => $result,
      'message' => $message,
      'error' => $error,
    ], 200);
  }

  public function sendErrorForValidation($message, $code = 422, $errors = [])
  {

    return Response::json([
      'success' => false,
      'message' => $message,
      "status_code" => $code,
      'errors' => $errors,

    ], $code);
  }

  public function formartDateTime($time, $formart = 'd/m/Y H:i')
  {
    $carbonDate = Carbon::parse($time);
    return $carbonDate->format($formart); // Định dạng ngày tháng
  }
}

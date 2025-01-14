<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use InfyOm\Generator\Utils\ResponseUtil;
use Intervention\Image\ImageManagerStatic as Image;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Response;

/**
 * @SWG\Swagger(
 *   basePath="/api/v1",
 *   @SWG\Info(
 *     title="Laravel Generator APIs",
 *     version="1.0.0",
 *   )
 *   @OA\Server(
 *     url="http://localhost:8000/api"
 *   )
 *   @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Nhập Token tại đây"
 *   )
 * )
 * This class should be parent class for other API controllers
 * Class AppBaseController
 */
class AppBaseController extends Controller
{

    use InteractsWithMedia;

    public function sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }

    public function sendError($error, $code = 404, $data = [])
    {
        return Response::json([
            'success' => true,
            'message' => $error,
            'data' => '',
            "status" => $code
        ], 200);
        return Response::json(ResponseUtil::makeError($error, $data, $code), 200);
    }

    /**
     * @OA\Schema(
     *     schema="SuccessResponse",
     *     type="object",
     *     @OA\Property(property="success", type="boolean"),
     *     @OA\Property(property="message", type="string"),
     *     @OA\Property(property="data", type="array",
     *          @OA\Items()
     *     ),
     *     @OA\Property(property="status", type="integer", example="200")
     * )
     */
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

    public static function formatVND($number)
    {
        return number_format($number, 0, ',', ',');
    }

    public function formartDateTime($time, $formart = 'd/m/Y H:i')
    {
        $carbonDate = Carbon::parse($time);
        return $carbonDate->format($formart); // Định dạng ngày tháng
    }

    public function generateAvatar($name)
    {

        // Chuyển đổi tên về UTF-8 nếu cần
        $name = mb_convert_encoding($name, 'UTF-8', 'UTF-8');

        // Lấy hai ký tự đầu tiên
        $initials = strtoupper(substr($name, 0, 2));

        // Tạo ảnh nền 200x200
        $img = Image::canvas(200, 200, '#f0f0f0');
        // Vẽ chữ lên ảnh
        $img->text($initials, 100, 100, function($font) {
            $font->size(100);
            $font->file(public_path("/assets/font/OpenSans.ttf"));
            $font->color('#a1acb8');
            $font->align('center');
            $font->valign('middle');
        });

        // Trả về HTML cho ảnh
        return (string) $img->encode('data-url');
    }

    public function getNameAvatarDataTable($name, $avatar, $email) {
        return '<div class="d-flex justify-content-start align-items-center user-name" >
                     <div class="avatar-wrapper">
                        <div class="avatar avatar-sm me-3" >
                          <img src="' . $avatar . '" alt="Avatar" 
                    onerror="this.onerror=null; this.src=\'' . $this->generateAvatar($name) . '\'"
                    class="rounded-circle object-fit-cover" />
                        </div>
                     </div>
                     <div class="d-flex flex-column" >
                          <a href="app-user-view-account.html" class="text-body text-truncate">
                              <span class="fw-medium">' .
            $name .
            '</span>
                          </a>
                          <small class="text-muted">' .
            $email .
            '</small>
                    </div>
                  </div>';
    }

    public function uploadMediaTemplate($request, $folder = 'media'){
        if ($request->hasFile($folder) && $request->file($folder)->isValid()) {
            $this->addMediaFromRequest($folder)->toMediaCollection($folder);
            $this->getFirstMediaUrl($folder);
            $this->save(); //remember to save again
        }
    }
}

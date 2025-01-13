<?php

namespace App\Contract;

use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

trait CommonBusiness
{
    public function makeTokensConnectMicroservices($idThisServiceEnvKey = 'ID_KEY_SILCOIN_PLATFORM')
    {
        $tokens = [
            'X-Id-Token' => base64_encode(envOrFail($idThisServiceEnvKey)),
            'X-Security-Token' => base64_encode(envOrFail('SECURITY_KEY_MICROSERVICES')),
        ];
        return $tokens;
    }

    public function isValidTokensConnectMicroservices(Request $request)
    {
        $idTokenKey = $this->isRequestFromArGameService($request) 
                            ? envOrFail('ID_KEY_ARGAME_SERVICE')
                            : envOrFail('ID_KEY_HIDDEN_SILCOIN_SERVICE');
        if (
            $idTokenKey == base64_decode($request->header('X-Id-Token')) &&
            envOrFail('SECURITY_KEY_MICROSERVICES') == base64_decode($request->header('X-Security-Token'))
        ) {
            return true;
        }
        return false;
    }

    public function isRequestFromArGameService(Request $request)
    {        
        if (
            base64_decode($request->header('X-Id-Token')) == envOrFail('ID_KEY_ARGAME_SERVICE')
        ) {
            return true;
        }
        return false;
    }

    public function isRequestFromHiddenSilcoinGameService(Request $request)
    {        
        if (
            base64_decode($request->header('X-Id-Token')) == envOrFail('ID_KEY_HIDDEN_SILCOIN_SERVICE')
        ) {
            return true;
        }
        return false;
    }

    public function codeHash($str, $len=null)
    {
        $binhash = md5($str, true);
        $numhash = unpack('N2', $binhash);
        $hash = $numhash[1] . $numhash[2];
        if($len && is_int($len)) {
            $hash = substr($hash, 0, $len);
        }
        return $hash;
    }

    public function defaultValidatorMicroservice(Request $request, $additionalRules = [])
    {
        $inputData = $request->all();
        $inputData['id_token'] = $request->header('X-Id-Token');
        $inputData['security_token'] = $request->header('X-Security-Token');

        $rules = [ 
            'id_token' => 'required',
            'security_token' => 'required',
        ];
        if (!empty($additionalRules)) {
            $rules = array_merge($rules, $additionalRules);
        }
        $validator = Validator::make($inputData, 
            $rules,
            $messages = [
                'required' => __('messages.params_required'),
            ]
        );
        return $validator;
    }

    public function generateNewToken()
    {
        $salt = Str::random(10);
        $datetimeString = Carbon::now()->toDateTimeString();
        $message = $salt . '@' . $datetimeString;
        $token = Crypt::encryptString($message);
        return $token;
    }

    public function decryptToken($token)
    {
        return Crypt::decryptString($token);
    }

    public static function createSlug($string, $delimiter = '-')
    {

        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'D', 'đ' => 'd', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', '/' => '-', ' ' => '-'
        );

        // -- Remove duplicated spaces
        $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

        // -- Returns the slug
        return strtolower(strtr($string, $table));
    }

    public static function getTimeNow($time){

        return (new \DateTime(date($time)))->format('Y:m:d H:i:00');
        
    }

    public static function isIP($ip = null)
    {
        if( !$ip or strlen(trim($ip)) == 0){
            return false;
        }
        if(!filter_var($ip, FILTER_VALIDATE_IP))
        {
            return false;
        } 

        return true;
    }

    public static function change_alias($alias)
    {
        $utf8 = array(
            '/[áàảãạấầẩẫậâăắằẵẳặãªäẵÁÀẢẠÃÄÂẤẦẪẨẬẮẰĂẶẲẴ]/u'   =>   'a',
            '/[đĐ]/u'        =>   'd',
            '/[ýỳỷỹỵÝỲỶỸỴ]/u'        =>   'y',
            '/[íìîïịỉĩÍÌÎÏỊỈĨ]/u'     =>   'i',
            '/[éèêếềëẹệẻẽểễÉÈÊẾỀËẸẺẼỆỂỄ]/u'     =>   'e',
            '/[ơớờởợỡóòỏõôốồõºöọỏộổỗOÓÒÔỐỒÕÖỌỎỘỔỖƠỚỜỞỠỢ]/u'   =>   'o',
            '/[úùûüụủũưựửữứừUÚÙÛÜỤỦŨỰỬỮỨỪ]/u'     =>   'u',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/ /'           =>   '-',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            // '/ /'           =>   '', // nonbreaking space (equiv. to 0x160)
        );
        
        return preg_replace(array_keys($utf8), array_values($utf8), mb_strtolower($alias));
    }

    public static function handleMediaFromUrl ($model, $collectionName, $getMedia = false) {

        // dd($model->$collectionName);



        $collectionNames = explode(",", $model->$collectionName);
        // dd($collectionNames);

        try {
            if (count($collectionNames) > 1) {

                $files = explode(",", $model->$collectionName);

                $new_file = [];
    
                foreach ( $files as $file ) {

                    $newMedia = $model->addMediaFromUrl($file)
                        ->usingFileName(
                            Str::random()
                        )
                        ->toMediaCollection($collectionName);

                    $mediaUrl = $newMedia->getUrl();

                    array_push($new_file, $mediaUrl);

                    return implode(',', $new_file);
                }
            } else {

                $file = $model->$collectionName;

                $newMedia = $model->addMediaFromUrl($file)
                        ->usingFileName(
                            Str::random()
                        )
                        ->toMediaCollection($collectionName);

                if ($getMedia) {
                    return $newMedia;
                }

                return $model->getFirstMediaUrl($collectionName);

            }
        } catch (\Throwable $th) {
            return false;
        }  
        
    }

    public static function handleMediaJsonFull ($model, $request, $collectionName, $getMedia = false) {
        if ($request->file($collectionName) == null) {
            return true;
        }
        try {
            if ($request->hasFile($collectionName) && !is_array($request->file($collectionName)) && $request->file($collectionName)->isValid()) {

                $file = $request->file($collectionName);

                $newMedia = $model->addMedia($file)
                    ->usingFileName(
                        CommonBusiness::change_alias($file->getClientOriginalName())
                    )
                    ->toMediaCollection($collectionName);

                $fileName = $newMedia->file_name; // Tên file
                $fileUrl = $newMedia->getFullUrl(); // URL đầy đủ của file
                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Phần mở rộng file                if ($getMedia) {
                dd(1);

                return json_encode([
                    'name' => $fileName,
                    'url' => $fileUrl,
                    'extension' => $fileExtension
                ]);
                // dd($newMedia->getUrl());
//                if ($getMedia) {
//                    return $newMedia;
//                }
//                return $model->getFirstMediaUrl($collectionName);

            } else if ($request->hasFile($collectionName) && is_array($request->file($collectionName)) && count($request->file($collectionName)) > 0) {

                $files = $request->file($collectionName);

                $results = [];

                foreach ($files as $key => $file) {

                    $newMedia = $model->addMedia($file)
                        ->usingFileName(
                            CommonBusiness::change_alias($file->getClientOriginalName())
                        )
                        ->toMediaCollection($collectionName);
                    $fileName = $newMedia->file_name; // Tên file
                    $fileUrl = $newMedia->getFullUrl(); // URL đầy đủ của file
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Phần mở rộng file

                    // $mediaUrl = Media::all()->last()->getUrl();
                    $mediaUrl = $newMedia->getUrl();

                    array_push($results, [
                        'name' => $fileName,
                        'url' => $fileUrl,
                        'extension' => $fileExtension
                    ]);
                }
                return json_encode($results);
            }
        } catch (\Throwable $th) {

            return false;
            //throw $th;
        }
    }


    public static function handleMedia ($model, $request, $collectionName, $getMedia = false) {
        if ($request->file($collectionName) == null) {
            return false;
        }
        try {
            if ($request->hasFile($collectionName) && !is_array($request->file($collectionName)) && $request->file($collectionName)->isValid()) {

                $file = $request->file($collectionName);

                $newMedia = $model->addMedia($file)
                    ->usingFileName(
                        CommonBusiness::change_alias($file->getClientOriginalName())
                    )
                    ->toMediaCollection($collectionName);
                        // dd($newMedia->getUrl());
                if ($getMedia) {
                    return $newMedia;
                }
                return $model->getFirstMediaUrl($collectionName);
 
            } else if ($request->hasFile($collectionName) && is_array($request->file($collectionName)) && count($request->file($collectionName)) > 0) {
                $files = $request->file($collectionName);

                $results = [];

                foreach ($files as $key => $file) {

                    $newMedia = $model->addMedia($file)
                        ->usingFileName(
                            CommonBusiness::change_alias($file->getClientOriginalName())
                        )
                        ->toMediaCollection($collectionName);
                    $fileName = $newMedia->file_name; // Tên file
                    $fileUrl = $newMedia->getFullUrl(); // URL đầy đủ của file
                    $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION); // Phần mở rộng file

                    // $mediaUrl = Media::all()->last()->getUrl();
                    $mediaUrl = $newMedia->getUrl();
                    array_push($results, $mediaUrl);
                }

                return implode(',', $results);
            }
        } catch (\Throwable $th) {

            return false;
            //throw $th;
        }
    }

    public static function searchInCollection($query, $fieldSearchable = [], $keyword = null, $relationSearchable = [], $relationFields = [])
    {

        $search = [];

        $relationSearch = [];

        if (count($fieldSearchable) > 0) {
            $search = array_fill_keys($fieldSearchable, $keyword);
        }

        if (count($relationSearchable) > 0) {
            $relationSearch = array_fill_keys($relationSearchable, $keyword);
        }

        if (count($search)) {
            $query = $query->where(function ($q) use ($search, $fieldSearchable, $relationFields, $relationSearch, $relationSearchable) {
                foreach ($search as $key => $value) {
                    if (in_array($key, $fieldSearchable)) {
                        $q->orwhere($key, 'like', '%' . $value . '%');
                    }
                }
                if (count($relationFields)) {

                    $q = $q->orwhere(function ($qu) use ($relationFields, $relationSearch, $relationSearchable) {
                        foreach ($relationFields as $field) {
                            if (count($relationSearch)) {
                                $qu->orwhereHas($field, function ($where) use ($relationSearch, $relationSearchable) {
                                    foreach ($relationSearch as $key => $value) {
                                        if (in_array($key, $relationSearchable)) {
                                            $where->where($key, 'like', '%' . $value . '%');
                                        }
                                    }
                                });
                            }
                        }
                    });
                }
            });
        }

        return $query;
    }

    public function check_base64_image($base64)
    {
        $result = [

            "isValidFormat" => true,

            "isValidSize" => true,

        ];

        if (!preg_match("#data:image/[^;]+;base64,#", $base64)) {
            // var_dump(2);

            $result["isValidFormat"] = false;

            return $result;
        }

        $validMineType = ["image/jpeg", 'image/jpg', "image/png", "image/gif"];

        $imageSizeInMb = $this->getBase64ImageSize($base64);

        if ($imageSizeInMb && $imageSizeInMb > 10) {

            $result["isValidSize"] = false;

            return $result;

        }

        $imageData = getimagesize($base64);

        if (!$imageData) {
            $result["isValidFormat"] = false;

            return $result;
        }

        $mime_type = $imageData["mime"];

        if (!in_array($mime_type, $validMineType)) 
        {
            $result["isValidFormat"] = false;

            return $result;
        }

        $imgdata = base64_decode(preg_replace('#data:image/[^;]+;base64,#', '', $base64));

        if (!$imgdata) {

            $result["isValidFormat"] = false;

            return $result;
        }

        return $result;
    }

    public function getBase64ImageSize($base64Image)
    { //return memory size in B, KB, MB
        try {
            $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 0.75);
            $size_in_kb    = $size_in_bytes / 1024;
            $size_in_mb    = $size_in_kb / 1024;

            return $size_in_mb;

        } catch (Exception $e) {
            return $e;
        }
    }

    public function handleBase64Media($model, $base64s, $collectionName)
    {
        try {
            $results = [];

            foreach ($base64s as $key => $base64) {

                $imageData = getimagesize($base64);

                $mime_type = str_replace('image/', '.', $imageData["mime"]);

                $newMedia = $model->addMediaFromBase64($base64)
                    ->usingFileName(
                        Str::random() . $mime_type
                    )
                    ->toMediaCollection($collectionName);

                // $mediaUrl = Media::all()->last()->getUrl();
                $mediaUrl = $newMedia->getUrl();

                array_push($results, $mediaUrl);
            }

            return implode(',', $results);
        } catch (\Throwable $th) {
            //throw $th;
            return false;
        }
    }

    public function checkRole($user)
    {

        if ($user->hasRole(['user'])) {

            return "freeStudent";
            
        } else if ($user->hasRole(['employee']) && $user->department_id != null) {
            
            return "localStudent";
            
        } else if ($user->hasRole(['employee']) && !$user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {

            return "employee";

        } else if ($user->hasRole(['employee']) && $user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {

            return "teacher";

        } else if ($user->hasRole('admin')) {

            return "admin";
            
        }
    }

    public static function getTimeNowJob($client_timezone = 7)
    {
        $date = Carbon::now();
        return $date->format('Y-m-d H:i:00');
    }

    public function getUserByMultipleRole($roles)
    {
        $request_roles = explode(',', $roles);
        // dd($request_roles);
        $query = User::whereNotNull('email_verified_at')->where('isLocked', 0)
            ->where(function($q) use($request_roles) {
                    foreach ($request_roles as $key => $request_role) {
                        if ($request_role == 'teacher') {
                                $q->orwhere(function($w) {
                                    $w->whereHas('roles', function ($oq) {
                                        $oq->where('name', 'employee');
                                    })->whereHas('permissions', function ($oq) {
                                        $oq->where("name", PERMISSION["MANAGE_COURSES"]);
                                    });
                            });
                        }else {
                            $q->orwhereHas('roles', function ($q) use ($request_role) {
                                $q->where('name', $request_role);
                            });
                        }
                }
            });
        $model = $query->get();

        return $model;
    }

    public function getCurPrevMonth () {
        $timeCurent = Carbon::now()->setTimezone('Asia/Ho_Chi_Minh');
        $currentMonth = date("Y-m-t", strtotime($timeCurent));

        $previousDate = $timeCurent->subMonth();
        $previousMonth = date("Y-m-t", strtotime($previousDate));

        $prevPreviousDate = $previousDate->subMonth(1);
        $prevPreviousMonth = date("Y-m-t", strtotime($prevPreviousDate));

        $month["currentMonth"] = $currentMonth;
        $month["previousMonth"] = $previousMonth;
        $month["prevPreviousMonth"] = $prevPreviousMonth;
        return $month;
    }

    public function pushNotificationForUser($roles, $receivers = []) {

        $users = $this->getUserByMultipleRole($roles);

        if(count($users) > 0 ){

            foreach($users as $user) {

                event(new \App\Events\PushNotification($user->id));

            }

        }

        if(count($receivers) > 0 ){

            foreach($receivers as $user) {

                event(new \App\Events\PushNotification($user->id));

            }

        }

    }

    public function checkRoleForUser($user)
    {

        if ($user->hasRole(['user'])) {

            return "user";
        } else if ($user->hasRole(['employee']) && !$user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {

            return "employee";
        } else if ($user->hasRole(['employee']) && $user->hasPermissionTo(PERMISSION["MANAGE_COURSES"])) {

            return "teacher";
        } else if ($user->hasRole('admin')) {

            return "admin";
        }
    }
  
}
<?php

use App\Http\Controllers\API\PostAPIController;
use App\Http\Controllers\API\PostCategoryAPIController;
use App\Http\Controllers\API\UserAPIController;
use App\Http\Controllers\API\CourseCategoryAPIController;
use App\Http\Controllers\API\UserDepartmentAPIController;

use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware(['api.access'])->group(function(){
    Route::group([
        'prefix' => 'auth',
    ], function () {
        Route::post('login', 'AuthAPIController@authentication');
        Route::post('login-by-token', 'AuthAPIController@loginByToken');
        Route::post('signup', 'AuthAPIController@register');
        Route::post('reset-password', 'AuthAPIController@resetPassword');
        Route::get('signup/activate', 'AuthAPIController@activate');
        Route::post('send-request-reset', [UserAPIController::class, 'sendRequestResetPassword']);
        Route::post('resend-email-active', [UserAPIController::class, 'resendEmailActive']);

    });

    Route::group([
        'middleware' => 'auth:api'
    ], function () {
        Route::get('logout', 'UserAPIController@logout');
        Route::get('me', 'UserAPIController@getMyInformation');
        Route::post('setLang', 'UserAPIController@setLang');
        Route::post('update-profile', 'UserAPIController@updateMyProfile');
        Route::post('upload-avatar', 'UserAPIController@updateAvatar');
        Route::post('upload-images', 'UserAPIController@uploadMedia');
        Route::delete('images/{id}', 'UserAPIController@deleteMedia');
        Route::post('users/{user_id}/lock', 'UserAPIController@lockUser');
        Route::post('users/{user_id}/active', 'UserAPIController@reActivateAccount');
        Route::post('change-password', 'AuthAPIController@changePassword');
        Route::get('user-overview-data', 'UserAPIController@overviewDataForUser');

        Route::group([
            'middleware' => ['role:admin']
        ], function () {
            Route::post('create-account', 'UserAPIController@CreateUserAccount');
        });

        Route::get('user_departments', [UserDepartmentAPIController::class, 'index']);
        Route::post('course_categories/{category_id}', 'CourseCategoryAPIController@update');
        Route::post('course_categories/{category_id}/change-published', 'CourseCategoryAPIController@changePublished');

        Route::get('steps', 'LessonAPIController@step');

        Route::post('lessons', 'LessonAPIController@store');
        Route::get('lessons', 'LessonAPIController@index');
        Route::get('lessons/{id}', 'LessonAPIController@show');
        Route::post('delete-lessons', 'LessonAPIController@destroy');
        Route::post('lessons/{id}/media', 'LessonAPIController@deleteAttachment');
        Route::post('lessons/{id}', 'LessonAPIController@update');
        Route::post('finish-lesson', 'LessonAPIController@finishLesson');

        Route::get('courses', 'CourseAPIController@index');
        Route::get('tracks', 'CourseAPIController@tracks');
        Route::get('related_courses/{id}', 'CourseAPIController@getRelatedCourses');
        Route::get('search-courses', 'CourseAPIController@searchCourse');



//        //post api
//        Route::get('posts', [PostAPIController::class, 'index']);
//        Route::post('posts', [PostAPIController::class, 'storePost']);
//        Route::post('posts/{post_id}', [PostAPIController::class, 'updatePost']);
//        Route::get('posts/{post_id}', [PostAPIController::class, 'detailPost']);
//        Route::post('posts/{post_id}/change-published', [PostAPIController::class, 'changePublishedPost']);
//        Route::post('posts/{post_id}/media', [PostAPIController::class, 'deleteBanner']);
//        Route::post('delete-posts', [PostAPIController::class, 'destroy']);
//    Route::post('banners', 'PostAPIController@createBanner');
//    Route::post('banners/{id}', 'PostAPIController@updateBanner');


        Route::post('faqs', 'FrequentlyAskedQuestionsAPIController@createFaqs');
        Route::get('frequently_asked_questions/{id}', 'FrequentlyAskedQuestionsAPIController@showFaq');
        Route::post('faqs/{id}', 'FrequentlyAskedQuestionsAPIController@updateFaqs');
        Route::get('frequently_asked_questions/{faq_id}/faq_question/{question_id}', 'FrequentlyAskedQuestionsAPIController@getQuestionById');

        Route::post('courses', 'CourseAPIController@store');
        Route::get('course/category', 'CourseAPIController@getListCourseByCategory');
        Route::post('clone-courses/{id}', 'CourseAPIController@cloneCouse');
        Route::post('courses/{id}', 'CourseAPIController@update');
        Route::get('courses/{id}', 'CourseAPIController@show');
        Route::get('courses/{id}/postions', 'CourseAPIController@getPostionsUserOfCourse');
        Route::get('courses/{id}/students', 'CourseAPIController@getStudentsOfCourse');
        Route::get('courses/{id}/leaders', 'CourseAPIController@getLeadersOfCourse');
        Route::post('delete-courses', 'CourseAPIController@deleteMuitipleCourse');
        Route::post('questions/{id}/media', 'QuestionAPIController@deleteQuestionAttachment');
        Route::post('options/{id}/media', 'QuestionOptionAPIController@deleteOptionAttachment');
        Route::post('courses/{id}/change-publish', 'CourseAPIController@changePublishCourse');
        Route::get('feedback', 'CourseStudentAPIController@getListCommentAndRating');

        Route::post('surveys', 'SurveyAPIController@store');
        Route::get('surveys/{id}', 'SurveyAPIController@show');
        Route::get('survey-detail/{id}', 'SurveyAPIController@surveyDetailForUser');
        Route::post('survey-result-detail', 'SurveyAPIController@surveyResultDetailForUser');
        Route::get('surveys', 'SurveyAPIController@index');
        Route::post('surveys/{id}', 'SurveyAPIController@update');

        Route::get('users/{id}', 'UserAPIController@getUserDetail');
        Route::post('users/{id}', 'UserAPIController@updateUserProfile');
        Route::post('invite-users', 'UserAPIController@inviteUser');
        Route::post('upload-invite-users', 'UserAPIController@inviteUserByImport');
        Route::post('upload-users', 'UserAPIController@importUser');
        Route::get('my-courses', 'UserAPIController@myCourses');
        Route::get('my-free-courses', 'UserAPIController@getFreeCourses');
        Route::get('export-template', 'UserAPIController@downloadTemplate');
        Route::get('positions', 'UserAPIController@getPositions');
        // Route::get('positions-by-leader', 'UserAPIController@getPositionsByRole');

        Route::post('confirm-notice', 'CourseStudentAPIController@confirmNotice');
        Route::post('courses/{course_id}/rating-comment', 'CourseStudentAPIController@commentAndRatingCourse');
        Route::post('join-course', 'CourseStudentAPIController@store');
        Route::get('list-rank-course-category', 'CourseStudentAPIController@getListRankCourseCategory');
        Route::get('list-rank-course', 'CourseStudentAPIController@getListRankCourse');
        Route::post('add-students-into-course', 'CourseStudentAPIController@addStudentsIntoCourse');
        Route::post('add-leaders-into-course', 'CourseLeaderAPIController@addLeadersIntoCourse');
        Route::post('course/{cid}/leader/{uid}', 'CourseLeaderAPIController@removeLeaderOfCourse');
        Route::resource('frequently_asked_questions', FrequentlyAskedQuestionsAPIController::class);
        Route::post('like-faq/{faq_id}', 'FrequentlyAskedQuestionsAPIController@likeFaq');
        Route::post('faq-question-like-dislike', 'FAQQuestionAPIController@likeOrDislikeFaqQuestion');
        Route::post('faq-question-comment', 'FAQQuestionAPIController@commentQuestion');
        Route::post('frequently_asked_questions/{faq_id}/comments', 'FrequentlyAskedQuestionsAPIController@commentFAQ');
        Route::get('get-list-faq', 'FrequentlyAskedQuestionsAPIController@getAllListFAQ');
        Route::resource('messages', MessagesAPIController::class);

        Route::post('learning-process', 'LearningProcessAPIController@getLearningProcess');
        Route::get('courses/{course_id}/learning-process/{student_id}', 'LearningProcessAPIController@getLearningProcessesForUser');

        Route::get('messages/user/{user_id}', 'MessagesAPIController@showAllMessagesPrivate');
        Route::delete('messages/user/{user_id}', 'MessagesAPIController@deleteMessengers');
        Route::post('messages/receiver-seen/{user_id}', 'MessagesAPIController@receiverSeen');
        Route::post('user-online/{user_id}', 'MessagesAPIController@userOnline');
        Route::get('list-students', 'MessagesAPIController@getListStudentForTeacher');
        Route::get('list-teachers', 'MessagesAPIController@getListTeacherForStudent');
        Route::get('list-chat', 'MessagesAPIController@getListChat');
        Route::get('count-unread-messages', 'MessagesAPIController@countUnreadMessages');

        Route::get('faqs', 'FrequentlyAskedQuestionsAPIController@index');
        Route::resource('events', EventAPIController::class);
        Route::post('answers', 'AnswerAPIController@store');
        Route::post('rework-survey', 'AnswerAPIController@reWorkSurvey');
        Route::post('courses/{id}/check-joined-course', 'CourseAPIController@checkIsJoinedCourse');
        Route::resource('discount_codes', DiscountCodeAPIController::class);
        Route::post('discount_codes/{id}', 'DiscountCodeAPIController@update');
        Route::get('discount-code', 'DiscountCodeAPIController@generateDiscountCode');
        Route::get('list-events', 'EventAPIController@getListEvent');
        Route::get('list-all-events', 'EventAPIController@getListEventsApproved');
        Route::post('approved-event', 'EventAPIController@approvedEvent');
        Route::post('events/{id}', 'EventAPIController@update');
        Route::post('remove-events', 'EventAPIController@removeEvent');
        Route::post('report-errors', 'ReportContactAPIController@sendReportError');
        Route::get('checked-notifications', 'NotificationAPIController@setCheckStatusTheNotifications');
        Route::post('read-notifications', 'NotificationAPIController@readAllNotifications');
        Route::resource('notifications', NotificationAPIController::class);

        Route::get('push_notifications', 'PushNotificationAPIController@index');
        Route::get('push_notifications/{id}', 'PushNotificationAPIController@show');
        Route::post('push_notifications', 'PushNotificationAPIController@store');
        Route::post('push_notifications/{id}', 'PushNotificationAPIController@update');

        Route::get('overview-data', 'DashboardAPIController@overviewData');
        Route::get('feature-courses', 'CourseAPIController@featureCourses');
        Route::post('check-course-for-me', 'CourseAPIController@checkCourseForMe');
        Route::get('feature-course-categories', 'CourseCategoryAPIController@feature_course_categories');
        Route::get('feature-teachers', 'UserAPIController@feature_teachers');
        Route::get('feature-members', 'UserAPIController@feature_members');

        Route::get('business-courses', 'CourseAPIController@getBussinessCourses');
        Route::get('statistical-courses', 'CourseAPIController@statisticalCourses');
        Route::get('statistical-business', 'CourseAPIController@statisticalBusiness');
        Route::get('analysis-business', 'TransactionAPIController@analysisBusiness');

        // Route::resource('orders', OrderAPIController::class);
        Route::post('orders', 'OrderAPIController@store');
        Route::get('order-detail', 'OrderAPIController@show');

        Route::post('transactions', 'TransactionAPIController@store');
        Route::get('transactions/{id}', 'TransactionAPIController@show');
        Route::get('transactions', 'TransactionAPIController@index');
        Route::post('transactions/{id}', 'TransactionAPIController@update');
        Route::post('check-transaction', 'TransactionAPIController@checkTransactionCode');
        Route::post('change-status-transaction', 'TransactionAPIController@approveOrRejectTransaction');
        Route::post('reject-transactions/{id}', 'TransactionAPIController@rejectTransaction');
        Route::post('approve-transactions/{id}', 'TransactionAPIController@approveTransaction');
        Route::get('transaction-histories', 'TransactionAPIController@getTransactionHistories');
        Route::get('test-remove/{t_id}/{u_id}', 'TransactionAPIController@testRemoveCartitem');

        Route::get('report-business-courses', 'CourseAPIController@reportBusinessCourses');
        Route::get('report-detail-courses', 'CourseAPIController@reportDetailCourses');

        Route::post('check-discount', 'DiscountCodeAPIController@checkDiscountCode');

        Route::get('opinions', 'PostAPIController@getOpinions');

        Route::group([
            'middleware' => ['role:admin']
        ], function () {
            Route::get('users', 'UserAPIController@getUserList');
        });
        Route::get('certificates/{course_id}/download', 'CourseAPIController@downloadCertificate');
        Route::get('course-detail-learning-by-slug', 'CourseAPIController@getDetailCourseOfUserLearningBySlug');
        Route::post('/payments', 'PaymentController@createPayment');
    });

    Route::get('allCourses', 'CourseAPIController@index');
    Route::get('get-list-course', 'CourseAPIController@getListCourse')->name('courses.list');
    Route::post('send-contacts', 'ReportContactAPIController@sendContact');
    Route::post('receive-information', 'ReportContactAPIController@receiveInformation');
    Route::post('carts', 'CartAPIController@store');
    Route::get('carts/{id}', 'CartAPIController@show');
    Route::post('carts/{id}/cart_items/{cart_item}', 'CartAPIController@deleteItemOutCart');
    Route::get('course-categories', [CourseCategoryAPIController::class, 'index']);
//Route::get('course-categories-type', [CourseCategoryAPIController::class, 'getCourseCategoryType']);
    Route::get('course-categories-menu', [CourseCategoryAPIController::class, 'getCategoryMenu']);
    Route::get('course-detail-by-slug', 'CourseAPIController@getDetailCourseOfUserBySlug');
    Route::get('course-detail/{id}', 'CourseAPIController@getDetailCourseOfUser');
    Route::get('rating-comment-course', 'CourseStudentAPIController@getListCommentAndRatingByCourse');


    Route::get('user-by-role', [UserAPIController::class, 'getUserByRole']);
    Route::get('banners', 'PostAPIController@getAllBanner');


// /*
//  * UPLOAD S3
//  * **/

// Route::post('/upload-file', 'UploadS3Controller@uploadFile');
// Route::get('/get-link', 'UploadS3Controller@getPresignedUrl');


    Route::post('upload', 'UploadFileController@upload');
    Route::get('image/{filename}', 'UploadFileController@getImage');

// Route::resource('order_items', OrderItemAPIController::class);

    //post api
    Route::get('posts', [PostAPIController::class, 'index']);
    Route::post('posts', [PostAPIController::class, 'storePost']);
    Route::post('posts/{post_id}', [PostAPIController::class, 'updatePost']);
//    Route::get('posts/{post_id}', [PostAPIController::class, 'detailPost']);
    Route::get('posts/posts-slug', [PostAPIController::class, 'detailPostSlug']);
    Route::post('posts/{post_id}/change-published', [PostAPIController::class, 'changePublishedPost']);
    Route::post('posts/{post_id}/media', [PostAPIController::class, 'deleteBanner']);
    Route::post('delete-posts', [PostAPIController::class, 'destroy']);


    //post-category api
    Route::post('post-categories', [PostCategoryAPIController::class, 'storePostCategory']);
    Route::get('post-categories', [PostCategoryAPIController::class, 'index']);
    Route::post('post-categories/{category_id}', 'PostCategoryAPIController@updatePostCategory');
    Route::get('post-categories/{category_id}', [PostCategoryAPIController::class, 'detailPostCategory']);
    Route::post('delete-post-categories', [PostCategoryAPIController::class, 'destroy']);
});
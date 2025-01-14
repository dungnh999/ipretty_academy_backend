<?php

use App\Http\Controllers\UsersController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\AuthController;

// ===================================================================
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\dashboard\Analytics;
use App\Http\Controllers\layouts\WithoutMenu;
use App\Http\Controllers\layouts\WithoutNavbar;
use App\Http\Controllers\layouts\Fluid;
use App\Http\Controllers\layouts\Container;
use App\Http\Controllers\layouts\Blank;
use App\Http\Controllers\pages\AccountSettingsAccount;
use App\Http\Controllers\pages\AccountSettingsNotifications;
use App\Http\Controllers\pages\AccountSettingsConnections;
use App\Http\Controllers\pages\MiscError;
use App\Http\Controllers\pages\MiscUnderMaintenance;
use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\authentications\RegisterBasic;
use App\Http\Controllers\authentications\ForgotPasswordBasic;
use App\Http\Controllers\cards\CardBasic;
use App\Http\Controllers\user_interface\Accordion;
use App\Http\Controllers\user_interface\Alerts;
use App\Http\Controllers\user_interface\Badges;
use App\Http\Controllers\user_interface\Buttons;
use App\Http\Controllers\user_interface\Carousel;
use App\Http\Controllers\user_interface\Collapse;
use App\Http\Controllers\user_interface\Dropdowns;
use App\Http\Controllers\user_interface\Footer;
use App\Http\Controllers\user_interface\ListGroups;
use App\Http\Controllers\user_interface\Modals;
use App\Http\Controllers\user_interface\Navbar;
use App\Http\Controllers\user_interface\Offcanvas;
use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
use App\Http\Controllers\user_interface\Progress;
use App\Http\Controllers\user_interface\Spinners;
use App\Http\Controllers\user_interface\TabsPills;
use App\Http\Controllers\user_interface\Toasts;
use App\Http\Controllers\user_interface\TooltipsPopovers;
use App\Http\Controllers\user_interface\Typography;
use App\Http\Controllers\extended_ui\PerfectScrollbar;
use App\Http\Controllers\extended_ui\TextDivider;
use App\Http\Controllers\icons\Boxicons;
use App\Http\Controllers\form_elements\BasicInput;
use App\Http\Controllers\form_elements\InputGroups;
use App\Http\Controllers\form_layouts\VerticalForm;
use App\Http\Controllers\form_layouts\HorizontalForm;
use App\Http\Controllers\tables\Basic as TablesBasic;

// Main Page Route

// layout
Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

// pages
Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name(
    'pages-account-settings-account'
);
Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name(
    'pages-account-settings-notifications'
);
Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name(
    'pages-account-settings-connections'
);
Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name(
    'pages-misc-under-maintenance'
);

// authentication
Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

// cards
Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

// User Interface
Route::get('/ui/accordion', [Accordion::class, 'index'])->name('ui-accordion');
Route::get('/ui/alerts', [Alerts::class, 'index'])->name('ui-alerts');
Route::get('/ui/badges', [Badges::class, 'index'])->name('ui-badges');
Route::get('/ui/buttons', [Buttons::class, 'index'])->name('ui-buttons');
Route::get('/ui/carousel', [Carousel::class, 'index'])->name('ui-carousel');
Route::get('/ui/collapse', [Collapse::class, 'index'])->name('ui-collapse');
Route::get('/ui/dropdowns', [Dropdowns::class, 'index'])->name('ui-dropdowns');
Route::get('/ui/footer', [Footer::class, 'index'])->name('ui-footer');
Route::get('/ui/list-groups', [ListGroups::class, 'index'])->name('ui-list-groups');
Route::get('/ui/modals', [Modals::class, 'index'])->name('ui-modals');
Route::get('/ui/navbar', [Navbar::class, 'index'])->name('ui-navbar');
Route::get('/ui/offcanvas', [Offcanvas::class, 'index'])->name('ui-offcanvas');
Route::get('/ui/pagination-breadcrumbs', [PaginationBreadcrumbs::class, 'index'])->name('ui-pagination-breadcrumbs');
Route::get('/ui/progress', [Progress::class, 'index'])->name('ui-progress');
Route::get('/ui/spinners', [Spinners::class, 'index'])->name('ui-spinners');
Route::get('/ui/tabs-pills', [TabsPills::class, 'index'])->name('ui-tabs-pills');
Route::get('/ui/toasts', [Toasts::class, 'index'])->name('ui-toasts');
Route::get('/ui/tooltips-popovers', [TooltipsPopovers::class, 'index'])->name('ui-tooltips-popovers');
Route::get('/ui/typography', [Typography::class, 'index'])->name('ui-typography');

// extended ui
Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name('extended-ui-perfect-scrollbar');
Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

// icons
Route::get('/icons/boxicons', [Boxicons::class, 'index'])->name('icons-boxicons');

// form elements
Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

// form layouts
Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

// tables
Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('signup/activate/{id}/{token}', [UsersController::class, 'activate'])->name('signupActivate');

Route::group(['middleware' => 'auth'], function () {
    /**
     *  Quản lý tổng quan
     */
    Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');

    /**
     *  Quản lý bộ phận
     */
    Route::controller('DepartmentController')
        ->prefix('department')
        ->as('department.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get-data-department', 'getListDepartment');
            Route::post('/create', 'CreateDepartment')->name('create');
            Route::post('/update', 'UpdateDepartment')->name('create');
            Route::get('/get-detail-department', 'getDetailDepartment')->name('detail');
            Route::post('/change-status', 'changeStatus')->name('change-status');
            Route::post('/change-status', 'changeStatus')->name('change-status');
        });

    /**
     *  Quản lý tài khoản
     */
    Route::controller('UsersController')
        ->prefix('users')
        ->as('users.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get-data-user', 'getListUsers');
            Route::post('/create', 'CreateUserAccount');
            Route::post('/update', 'updateUserAccount');
            Route::get('/get-data-department', 'getDataDepartment');
            Route::post('/change-status', 'lockUser');
            Route::get('/get-profile', 'getProfileUser');
        });

    /**
     *  Quản lý danh mục khoá học
     */
    Route::controller('CoursesCategoryController')
        ->prefix('courses-category')
        ->as('courses-category.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get-data-category', 'getListCategory');
            Route::get('/detail', 'getDetail');
            Route::post('/create', 'CreateCoursesCategory');
            Route::post('/update', 'UpdateCoursesCategory');
            Route::post('/change-status', 'ChangeStatusCoursesCategory');
            Route::get('/get-data-category-types', 'getCategoryTypes');
        });

    /**
     *  Quản lý danh mục khoá học
     */
    Route::controller('CoursesCategoryTypesController')
        ->prefix('courses-category-type')
        ->as('courses-category-type.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get-data-category-type', 'getListCategoryTypes');
            Route::post('/create-course-category-type', 'createCourseCategoryTypes');
            Route::get('/data-update-course-category-type', 'dataUpdateCourseCategoryTypes');
            Route::post('/update-course-category-type', 'updateCourseCategoryTypes');
            Route::post('/change-status', 'changeStatusCoursesCategoryTypes');
        });

    /**
     *  Quản lý banner
     */
    Route::controller('BannerController')
        ->prefix('banner')
        ->as('banner.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get-data-banner', 'getListBanner');
            Route::post('/create', 'CreateBanner');
            Route::post('/change-status', 'changeStatus');
            Route::get('/detail', 'detailBanner');
            Route::post('/update', 'updateBanner');
        });

    /**
     *  Quản lý khoá học
     */
    Route::controller('CourseController')
        ->prefix('course')
        ->as('course.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get-data-course', 'getDataCourse')->name('index');
            Route::get('/get-detail-course', 'getDetailCourse')->name('detail');
            Route::get('/get-teacher', 'getDataTeacher')->name('getdata');
            Route::post('/change-status-course', 'changeCourse')->name('getdata');
            Route::post('/change-published-course', 'changePublished')->name('published');
            Route::get('/get-category', 'getDataCategory')->name('getcategory');
            Route::post('/create-course', 'createCourse')->name('create');
            Route::post('/update-course', 'UpdateCourse')->name('update');
            Route::post('/create-chapter-course', 'createChapterCourse')->name('create');
            Route::post('/update-chapter-course', 'updateChapterCourse')->name('create');
            Route::post('/update-lesson-course', 'updateLessonCourse')->name('update');
            Route::post('/create-lesson-course', 'createLessonCourse')->name('create-lesson');
            Route::get('/get-detail-lesson', 'getDetailLessonCourse')->name('lesson');
            Route::get('/get-data-chapter-course', 'getDataChapterCourse')->name('chapter');
        });

    /**
     *  Quản lý chương trình
     */
    Route::controller('ChapterController')
        ->prefix('chapters')
        ->as('chapters.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/get-data-course', 'getDataCourse')->name('course');
            Route::get('/get-data-chapter-course', 'getDataChapterCourse')->name('chapter');
            Route::post('/create-chapter', 'create');
        });

    /**
     *  Thiết lập hệ thống
     */
    Route::controller('SettingController')
        ->prefix('setting')
        ->as('setting.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/update-logo', 'updateLogo')->name('index');
        });

    /**
     *  Thông tin tài khoản
     */
    Route::controller('ProfileController')
        ->prefix('profile')
        ->as('profile.')
        ->group(function () {
            Route::get('/', 'index')->name('profile');
        });

    /**
     *  Quản lý đơn hàng
     */
    Route::controller('OrderController')
        ->prefix('order')
        ->as('order.')
        ->group(function () {
            Route::get('/', 'index')->name('order');
            Route::get('/get-data-order', 'getDataOrder')->name('order');
        });

    /** VNPAY
     */
    Route::controller('VnpayController')
        ->prefix('vnpay')
        ->as('vnpay.')
        ->group(function () {
            Route::get('/callback', 'handleVNPayCallback')->name('callback');
        });

    /** VNPAY
     */
    Route::controller('NotificationController')
        ->prefix('notification')
        ->as('notification.')
        ->group(function () {
            Route::get('/', 'getAll')->name('getAll');
        });

    /**
     *  Thiết lập hệ thống
     */
    // Route::controller('UploadS3Controller')
    //   ->prefix('upload')
    //   ->as('upload.')
    //   ->group(function () {
    //     Route::post('/post-file', 'uploadFile')->name('index');
    //   });
});

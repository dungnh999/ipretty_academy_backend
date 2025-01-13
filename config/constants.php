<?php

defined('HEADER_SERVICE_TOKEN') or define('HEADER_SERVICE_TOKEN', 'X-Security-Token');

defined('DERARTMENT_NAME_EDUCATE') or define('DERARTMENT_NAME_EDUCATE', 'Bộ Phận Đào Tạo');
defined('SESSION_KEY_ACCESS_TOKEN') or define('SESSION_KEY_ACCESS_TOKEN', 'SESSION_KEY_ACCESS_TOKEN');
defined('SESSION_KEY_REGISTER_REFERRAL_CAMPAIGN') or define('SESSION_KEY_REGISTER_REFERRAL_CAMPAIGN', 'SESSION_KEY_REGISTER_REFERRAL_CAMPAIGN');
defined('URI_PARAM_REGISTER_REFERRAL_CAMPAIGN') or define('URI_PARAM_REGISTER_REFERRAL_CAMPAIGN', 'ref_camp');
defined('SESSION_KEY_SOCIAL_REGISTER_REFERRAL_CAMPAIGN') or define('SESSION_KEY_SOCIAL_REGISTER_REFERRAL_CAMPAIGN', 'SESSION_KEY_SOCIAL_REGISTER_REFERRAL_CAMPAIGN');
defined('URI_PARAM_SOCIAL_REGISTER_REFERRAL_CAMPAIGN') or define('URI_PARAM_SOCIAL_REGISTER_REFERRAL_CAMPAIGN', 'social_ref_camp');
defined('PERPAGE') or define('PERPAGE', 10);
defined('MEDIA_COLLECTION') or define('MEDIA_COLLECTION', [
  'CERTIFICATE_IMAGE' => 'certificate_image',
  'COURSE_FEATURE_IMAGE' => 'course_feature_image',
  'LESSON_ATTACHMENT' => 'lesson_attachment',
  'LESSON_MATERIAL' => 'lesson_material_file',
  'LESSON_MAIN_ATTACHMENT' => 'main_attachment',
  'COURSE_CATEGORY_ATTACHMENT' => 'course_category_attachment',
  'USER_AVATAR' => 'avatar',
  'QUESTION_ATTACHMENTS' => 'question_attachments',
  'OPTION_ATTACHMENTS' => 'option_attachments',
  'POST_BANNERURL' => 'bannerUrl'
]);

defined('PERMISSION') or define('PERMISSION', [
  'MANAGE_COURSES' => 'manage_courses',
  'VIEW_COURSE' => 'view_course',
  'UPDATE_COURSE' => 'update_course',
  'DELETE_COURSE' => 'delete_course',
  'MANAGE_STUDENTS' => 'manage_students',
  'MANAGE_LEADERS' => 'manage_leaders',
  'MANAGE_MEMBERS' => 'manage_members',
  'MANAGE_LESSONS' => 'manage_lessons',
  'MANAGE_SURVEYS' => 'manage_surveys',
  'MANAGE_DASHBOARD' => 'manage_dashboard',
  'MANAGE_CONTENTS' => 'manage_contents',
  'MANAGE_FAQS' => 'manage_faqs',
  'MANAGE_NOTIFICATIONS' => 'manage_notifications',
  'MANAGE_TRANSACTIONS' => 'manage_transactions',
]);

defined('PERMISSION_TEACHER') or define('PERMISSION_TEACHER', [
  'MANAGE_COURSES' => 'manage_courses',
  'VIEW_COURSE' => 'view_course',
  'UPDATE_COURSE' => 'update_course',
  'DELETE_COURSE' => 'delete_course',
  'MANAGE_STUDENTS' => 'manage_students',
  'MANAGE_LEADERS' => 'manage_leaders',
]);

defined('PERMISSION_LEADER') or define('PERMISSION_LEADER', [
  'VIEW_COURSE' => 'view_course',
  'MANAGE_STUDENTS' => 'manage_students',
]);

defined('COOKIE_ACCESS_TOKEN') or define('COOKIE_ACCESS_TOKEN', 'accessToken');



/**
 * ENUM
*/
define('ENUM_MALE', 'Male');
define('ENUM_FEMALE', 'Female');
define('ENUM_OTHER', 2);
define('ENUM_ACTIVE', 1);
define('ENUM_UNACTIVE', 0);


/**
 * ENUM UPLOAD
 */
define('ENUM_VIDEO', 1);
define('ENUM_IMAGE', 2);
define('ENUM_FILES', 3);

/**
 * ENUM UPLOAD
 */
define('ENUM_BANNER', 1);
define('ENUM_COURSE', 2);
define('ENUM_BLOG', 2);
define('ENUM_ACCOUNT', 2);
define('ENUM_LOGO', 3);


/**
 * ENUM POSITION
 */
define('ENUM_POSITION_ADMIN', 0);
define('ENUM_POSITION_TEACHER', 1);
define('ENUM_POSITION_STUDENT', 2);
define('ENUM_POSITION_EMPLOYEE', 3);


/**
 * ENUM PREFIX ROLE
 */
define('ENUM_PREFIX_ROLE_MANAGER', 0);
define('ENUM_PREFIX_ROLE_TEACHER', 1);
define('ENUM_PREFIX_ROLE_EMPLOYEE', 2);
define('ENUM_PREFIX_ROLE_USER', 3);


/**
 * ENUM TEXT
 */

define('ENUM_TEXT_MALE', 'male');
define('ENUM_TEXT_FEMALE', 'female');
define('ENUM_TEXT_OTHER', 'other');

define('ENUM_TEXT_ACTIVE', 'Đã xác thực');
define('ENUM_TEXT_INACTIVE', 'Chưa xác thực');

define('ENUM_TEXT_ROLE_ADMIN', 'Admin');
define('ENUM_TEXT_ROLE_USER', 'Nhân viên');


define('TEXT_LOCK', 'Đã khoá');
define('TEXT_OPEN', 'Đang mở');



/**
 * ENUM PREFIX ROLE TEXT
 */

define('TEXT_PREFIX_ROLE_MANAGER', 'QL');
define('TEXT_PREFIX_ROLE_TEACHER', 'GV');
define('TEXT_PREFIX_ROLE_EMPLOYEE', 'NV');
define('TEXT_PREFIX_ROLE_USER', 'HV');


/**
 * ENUM UPLOAD TEXT
 */
define('TEXT_URL_VIDEO', 'public/video');
define('TEXT_URL_IMAGE', 'public/image');
define('TEXT_URL_FILES', 'public/image');
define('TEXT_PATH_BANNER', '/banner/');
define('TEXT_PATH_COURSE', '/course/');
define('TEXT_PATH_BLOG', '/blog/');
define('TEXT_PATH_ACCOUNT', '/account/');
define('TEXT_PATH_LOGO', '/logo/');


/**
 * TEXT MESSAGE
 */
define('TEXT_UPLOAD_SUCCESSFULL', 'Tải ảnh lên thành công');
define('TEXT_UPLOAD_ERROR', 'Tải ảnh lên thất bại');


?>

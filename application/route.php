<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
use think\Route;

Route::group('admin',function (){
//登录
    Route::any('login','admin/LoginController/login');
    Route::any('logout','admin/LoginController/logout');
    Route::any('loginverify', 'admin/LoginController/loginverify');
//主页
    Route::any('index','Index/index');
//文件上传
    Route::any('upload','FileUpload/index');
    Route::any('layeditUpload','FileUpload/layeditPicUpload');
    Route::any('excelImport','FileUpload/PhpExcelImport');
//后台类别管理
    Route::any('type','TypeController/index');
    Route::any('typeAdd','TypeController/add');
    Route::any('typeSaveAdd','TypeController/saveAdd');
    Route::any('typeDel','TypeController/del');
//后台部门管理
    Route::any('dept','DeptController/index');
    Route::any('deptAdd','DeptController/add');
    Route::any('deptSaveAdd','DeptController/saveAdd');
    Route::any('deptDel','DeptController/del');
//后台课程管理
    Route::any('lesson','LessonController/index');
    Route::any('lessonAdd','LessonController/add');
    Route::any('lessonSaveAdd','LessonController/saveAdd');
    Route::any('lessonDel','LessonController/del');
    Route::any('lessonDetail','LessonController/detail');
    Route::any('test','LessonController/test');
//后台视频管理
    Route::any('video','VideoController/index');
    Route::any('videoAlt','VideoController/alt');
    Route::any('videoSaveAlt','VideoController/saveAlt');
    Route::any('videoDel','VideoController/del');
//学生信息
    Route::any('student','StudentController/index');
    Route::any('studentAdd','StudentController/add');
    Route::any('studentSaveAdd','StudentController/saveAdd');
    Route::any('studentDel','StudentController/del');
    Route::any('studentExport','StudentController/export');
    Route::any('studentImport','StudentController/import');
//教师注册信息
    Route::any('teacher','TeacherController/index');
    Route::any('teacherAdd','TeacherController/add');
    Route::any('teacherSaveAdd','TeacherController/saveAdd');
    Route::any('teacherDel','TeacherController/del');
    Route::any('teacherExport','TeacherController/export');
    Route::any('teacherImport','TeacherController/import');
//评论信息
    Route::any('comment','CommentController/index');
    Route::any('commentDel','CommentController/del');
    Route::any('commentBan','CommentController/ban');
    Route::any('commentRelieveBan','CommentController/relieveBan');
//问答信息
    Route::any('question','QuestionController/index');
});
Route::group('index',function (){
    Route::any('login','index/LoginController/login');
    Route::any('logout','index/LoginController/logout');
    Route::any('register','index/RegisterController/index');
    Route::any('registerSave','index/RegisterController/registerSave');
    Route::any('resetpass','index/RegisterController/resetPass');
    Route::any('forget','index/RegisterController/forget');
    Route::any('loginverify', 'index/LoginController/loginverify');
    Route::any('index','index/Index/index');
    Route::any('details','index/Index/details');


    Route::any('getComment','index/Index/getCommentsById');
    Route::any('questionDetail','index/Question/detail');
    Route::any('saveQuestionReplay','index/Question/saveQuestionReplay');


    Route::any('thumbsUpSave','index/Index/thumbsUpSave');
    Route::any('curriculumSave','index/Index/curriculumSave');
    Route::any('watchVideoAnchor','index/Index/watchVideoAnchor');
    Route::any('saveComment','index/Index/saveComment');
    Route::any('commentDel','index/Index/commentDel');
    Route::any('saveSubscribe', 'index/Index/saveSubscribe');

    Route::any('teacherhome','index/Index/teacherhome');

    Route::any('test','index/RegisterController/test');

    //email
    Route::any('sendmail','index/MailerController/index');
    Route::any('sendforget','index/MailerController/sendForget');

    //文件上传
    Route::any('upload','index/FileUpload/index');
    Route::any('layeditUpload','index/FileUpload/layeditPicUpload');
    Route::any('excelImport','index/FileUpload/PhpExcelImport');
    Route::any('videoUpload','index/FileUpload/videoUpload');
    Route::any('attachUpload','index/FileUpload/attachUpload');

    //注册
    Route::any('sreg','index/Index/sReg');
    Route::any('treg','index/Index/tReg');

    //搜索
    Route::any('search','index/Index/search');

    //学生个人中心
    Route::any('student','StudentController/index');
    Route::any('curriculum','StudentController/curriculum');
    Route::any('curriculumDel','StudentController/curriculumDel');
    Route::any('studentComment','StudentController/studentComment');
    Route::any('studentQuestion','StudentController/studentQuestion');
    Route::any('studentSubscribe','StudentController/subscribe');
    Route::any('studentSubscribeDel','StudentController/subscribeDel');
    Route::any('studentProfile','StudentController/profile');
    Route::any('studentProfileSave','StudentController/profileSave');

    //教师个人中心
    Route::any('teacher','TeacherController/index');
    Route::any('schedule','TeacherController/schedule');
    Route::any('videos','TeacherController/videos');
    Route::any('videoAlt','TeacherController/videoAlt');
    Route::any('videoSaveAlt','TeacherController/videoSaveAlt');
    Route::any('videoDel','TeacherController/videoDel');
    Route::any('teacherComment','TeacherController/teacherComment');
    Route::any('teacherQuestion','TeacherController/teacherQuestion');
    Route::any('teacherSubscribe','TeacherController/subscribe');
    Route::any('teacherSubscribeDel','TeacherController/subscribeDel');
    Route::any('teacherProfile','TeacherController/profile');
    Route::any('teacherProfileSave','TeacherController/profileSave');
});


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
    Route::any('videoAdd','VideoController/add');
    Route::any('videoSaveAdd','VideoController/saveAdd');
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
    Route::any('index','index/Index/index');
    Route::any('details','index/Index/details');
    Route::any('login','index/Index/login');
    Route::any('logout','index/Index/logout');
    Route::any('tlogin','index/Index/tlogin');
    Route::any('tlogout','index/Index/tlogout');
    //注册
    Route::any('sreg','index/Index/sReg');
    Route::any('treg','index/Index/tReg');

    //搜索
    Route::any('search','index/Index/search');

    //学生个人中心
    Route::any('schedule','StudentCenter/index');
    Route::any('scheduledel','StudentCenter/scheduleDel');
    Route::any('favorite','StudentCenter/favorite');

    Route::any('schangepass','StudentCenter/changepass');
    Route::any('spersoninfo','StudentCenter/personinfo');
    Route::any('schangeinfo','StudentCenter/changeinfo');
    Route::any('dochangepass','StudentCenter/doChangePass');
    Route::any('collect','StudentCenter/collect');

    Route::any('comment','StudentCenter/comment');
    //评论添加在视频详情和学生个人中心可用
    Route::any('commentadd','StudentCenter/commentAdd');
    Route::any('commentdel','StudentCenter/commentDel');

    //教师个人中心
    Route::any('tschedule','TeacherCenter/index');
    Route::any('tfavorite','TeacherCenter/favorite');
    Route::any('tcomment','TeacherCenter/comment');
    Route::any('tchangepass','TeacherCenter/changepass');
    Route::any('tpersoninfo','TeacherCenter/personinfo');
    Route::any('tchangeinfo','TeacherCenter/changeinfo');
    Route::any('tdochangepass','TeacherCenter/doChangePass');
    Route::any('addlesson','TeacherCenter/addLesson');
    Route::any('addvideo','TeacherCenter/addVideo');
    Route::any('doaddlesson','TeacherCenter/doAddLesson');
    Route::any('doaddvideo','TeacherCenter/doAddVideo');
    Route::any('reply','TeacherCenter/reply');
    Route::any('doreply','TeacherCenter/doReply');
});


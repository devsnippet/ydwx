<?php
/**
 * $oldcwd = getcwd();
 * #如需要把工作目录切换到你项目中去，并包含项目的库文件来实现hook中的逻辑
 * chdir($your_work_dir);
 * include_once 'your-lib-file.php';
 * chdir ( $oldcwd );
 */


YDWXHook::add_hook ( YDWXHook::GET_HOST_MCH_KEY, function ($appid) {
    
} );

YDWXHook::add_hook ( YDWXHook::GET_HOST_MCH_ID, function ($appid) {
    
} );

YDWXHook::add_hook ( YDWXHook::GET_HOST_APICLIENT_CERT_PATH, function ($appid) {
    
} );

YDWXHook::add_hook ( YDWXHook::GET_HOST_APICLIENT_KEY_PATH, function ($appid) {
    
} );

YDWXHook::add_hook ( YDWXHook::GET_HOST_ROOT_CA, function ($appid) {
   
} );

YDWXHook::add_hook ( YDWXHook::PAY_NOTIFY_ERROR, function ($error) {
    
} );

YDWXHook::add_hook ( YDWXHook::PAY_NOTIFY_SUCCESS, function (YDWXPaiedNotifyResponse $msg) {
    // 支付成功的处理
    
    return true;
} );

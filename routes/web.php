<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//#region admin route
Route::group(['as' => 'admin.', 'prefix' => 'admin'], function () {

    Route::group(['middleware' => 'guest'], function () {
        Route::get('/login', ['uses' => 'Auth\AdminLoginController@index', 'as' => 'login']);
        Route::post('/login', ['uses' => 'Auth\AdminLoginController@authenticate', 'as' => 'authenticate']);

        Route::get('/password/reset', ['uses' => 'Auth\ForgotPasswordController@showLinkRequestFormAdmin', 'as' => 'password.request']);
        Route::post('/password/email', 'Auth\ForgotPasswordController@sendResetLinkEmailAdmin')->name('password.email');
    });

    Route::group(['middleware' => 'auth'], function () {
        Route::get('/logout', ['uses' => 'Auth\AdminLoginController@logout', 'as' => 'logout']);

        Route::group(['namespace' => 'Admin'], function () {
            Route::get('/dashboard', ['uses' => 'DashboardController@index', 'as' => 'dashboard']);

            Route::resource('/customers', 'CustomerController');
            Route::resource('/numbers', 'NumberController');
            Route::resource('/plans', 'PlanController');

            Route::group(['as' => 'customer.', 'prefix' => 'customer'], function () {
                Route::get('/all', 'CustomerController@getAll')->name('get.all');
                Route::post('/assign-number', 'CustomerController@assignNumber')->name('number.assign');
                Route::post('/remove-number', 'CustomerController@removeNumber')->name('number.remove');
                Route::post('/change-plan', 'CustomerController@changePlan')->name('plan.change');
                Route::post('/login-as', 'CustomerController@loginAs')->name('login.ass');
            });

            Route::group(['as' => 'number.', 'prefix' => 'number'], function () {
                Route::get('/all', 'NumberController@getAll')->name('get.all');
                Route::get('/requests', 'NumberController@requests')->name('requests');
                Route::get('/requests/get', 'NumberController@get_requests')->name('get.requests');
                Route::post('/requests/response', 'NumberController@request_response')->name('requests.response');
            });

            Route::group(['as' => 'plan.', 'prefix' => 'plan'], function () {
                Route::get('/all', 'PlanController@getAll')->name('get.all');
                Route::get('/requests', 'PlanController@requests')->name('requests');
                Route::get('/requests/get', 'PlanController@get_requests')->name('get.requests');
            });

            Route::group(['as' => 'settings.', 'prefix' => 'settings'], function () {
                Route::get('/', 'SettingsController@index')->name('index');
                Route::post('/update/profile', 'SettingsController@profile_update')->name('profile_update');
                Route::post('/update/application', 'SettingsController@app_update')->name('app_update');
                Route::post('/update/smtp', 'SettingsController@smtp_update')->name('smtp_update');
                Route::post('/update/api', 'SettingsController@api_update')->name('api_update');

            });

            Route::group(['as' => 'addon.', 'prefix' => 'addon'], function () {
                Route::get('/', 'AddonController@index')->name('index');
                Route::get('/import', 'AddonController@import')->name('import');
                Route::post('/import', 'AddonController@importPost')->name('import');
                Route::get('/get/all', 'AddonController@getAll')->name('get.all');
                Route::delete('/uninstall', 'AddonController@uninstall')->name('uninstall');
                Route::post('/change-status', 'AddonController@changeStatus')->name('change-status');

            });
        });
    });

});
//#endregion


//#region customer routes

//Guest customer route
Route::group(['middleware' => 'guest:customer'], function () {
    Route::get('/login', ['uses' => 'Auth\CustomerLoginController@index', 'as' => 'login']);
    Route::post('/login', ['uses' => 'Auth\CustomerLoginController@authenticate', 'as' => 'authenticate']);

    Route::get('/sign-up', ['uses' => 'Auth\CustomerLoginController@sign_up', 'as' => 'signup']);
    Route::post('/sign-up', ['uses' => 'Auth\CustomerLoginController@sign_up_create', 'as' => 'signup']);

    Route::get('password/reset', 'Auth\ForgotPasswordController@show_form')->name('password.request');
    Route::post('password/reset', 'Auth\ForgotPasswordController@sent_email')->name('password.sent');
    Route::get('password/reset/confirm', 'Auth\ForgotPasswordController@reset_form')->name('password.reset.confirm');
    Route::post('password/reset/confirm', 'Auth\ForgotPasswordController@reset_confirm')->name('password.reset.confirm');

    Route::post('/inbound/{type}', ['uses' => 'InboundController@process', 'as' => 'inbound.process']);
    Route::get('/check/schedule', ['uses' => 'ScheduleController@process', 'as' => 'schedule.process']);
    Route::get('/verify/customer', ['uses' => 'Auth\CustomerLoginController@verify', 'as' => 'customer.verify']);

});

//Auth customer route
Route::group(['as' => 'customer.', 'middleware' => ['auth:customer', 'email.verify:customer']], function () {
    Route::get('/logout', ['uses' => 'Auth\CustomerLoginController@logout', 'as' => 'logout']);


    Route::group(['namespace' => 'Customer'], function () {
        Route::get('/dashboard', ['uses' => 'DashboardController@index', 'as' => 'dashboard']);

        Route::group(['as' => 'settings.', 'prefix' => 'settings'], function () {
            Route::get('/index', ['uses' => 'SettingsController@index', 'as' => 'index']);
            Route::post('/profile-update', ['uses' => 'SettingsController@profile_update', 'as' => 'profile_update']);
            Route::post('/password-update', ['uses' => 'SettingsController@password_update', 'as' => 'password_update']);
            Route::post('/notification-update', ['uses' => 'SettingsController@notification_update', 'as' => 'notification_update']);
        });
        Route::group(['as' => 'smsbox.', 'prefix' => 'smsbox'], function () {
            //inbox
            Route::get('/inbox', 'InboxController@index')->name('inbox');
            Route::post('/inbox/trash', 'InboxController@move_trash')->name('inbox.trash');
            Route::post('/inbox/change-status', 'InboxController@changeStatus')->name('inbox.change-status');

            //sent
            Route::get('/sent', 'SentController@index')->name('sent');
            Route::post('/sent/trash', 'SentController@move_trash')->name('sent.trash');

            //draft
            Route::get('/draft', 'DraftController@index')->name('draft');
            Route::post('/draft/store', 'DraftController@store')->name('draft.store');
            Route::post('/draft/delete', 'DraftController@delete')->name('draft.delete');

            //trash
            Route::get('/trash', 'TrashController@index')->name('trash');

            //compose
            Route::get('/compose', 'ComposeController@index')->name('compose');
            Route::post('/compose/sent', 'ComposeController@sentCompose')->name('compose.sent');
        });

        Route::group(['as' => 'billing.', 'prefix' => 'billing'], function () {
            Route::get('/', 'BillingController@index')->name('index');
            Route::get('/phone-numbers', 'BillingController@phone_numbers')->name('phone_numbers');
            Route::get('/get-numbers', 'BillingController@get_numbers')->name('get.numbers');
            Route::post('/update', 'BillingController@update')->name('update');
        });

        Route::group(['as' => 'numbers.', 'prefix' => 'phone-numbers'], function () {
            Route::get('/', 'NumberController@phone_numbers')->name('phone_numbers');
            Route::get('/get', 'NumberController@get_numbers')->name('get.numbers');
            Route::get('/purchase', 'NumberController@purchaseList')->name('purchase');
            Route::post('/purchase', 'NumberController@purchaseStore')->name('purchase');
            Route::post('/purchase/remove', 'NumberController@purchase_remove')->name('purchase.remove');
            Route::get('/purchase/list-get', 'NumberController@purchaseListGet')->name('purchase.list_get');
            Route::post('/update-forward', 'NumberController@updateForwardTo')->name('update-forward');
        });


        //Contacts
        Route::resource('/contacts', 'ContactController');
        Route::group(['as' => 'contact.', 'prefix' => 'contact'], function () {
            Route::get('/get', 'ContactController@getAll')->name('get.all');

        });

        //Groups
        Route::resource('/groups', 'GroupController');
        Route::group(['as' => 'group.', 'prefix' => 'group'], function () {
            Route::get('/get', 'GroupController@getAll')->name('get.all');

        });

        //Keywords
        Route::resource('/keywords', 'KeywordController');
        Route::group(['as' => 'keyword.', 'prefix' => 'keyword'], function () {
            Route::get('/get', 'KeywordController@getAll')->name('get.all');

        });

        //download sample
        Route::get('/download/sample/{type}','SettingsController@downloadSample')->name('download.sample');


    });
});

//#endregion

Route::get('/process/upgrade', ['uses' => 'UpgradeController@process', 'as' => 'process.upgrade']);

Route::redirect('/', route('login'));
Route::redirect('/admin', route('admin.login'));

Route::get('/test',['uses'=>'UpgradeController@test','as'=>'test.gen']);

//Route::get('{url}',['uses' => 'RouteController@index']);

<?php
use SmartBots\{
    User,
    Hub,
    Bot,
    Member,
    Schedule,
    Automation,
    HubPermission,
    BotPermission,
    SchedulePermission,
    AutomationPermission,
    Notification
};

use SmartBots\Events\VerifyServerSentEvents;

// Route::group([
//     'middleware' => [],
//     'as'         => 'comingsoon'
// ], function() {
//     Route::get('','ComingSoonController@index')->name('::index');
//     Route::post('/subscribe','ComingSoonController@subscribe')->name('::subscribe');
// });

Route::group([
    'as'         => 'landing'
], function() {
    Route::get('/','LandingController@index')->name('::index');
    Route::post('/contact','LandingController@contact')->name('::contact');
    Route::post('/subscribe','LandingController@subscribe')->name('::subscribe');
});

Route::group([
    'middleware' => [],
    'prefix'     => 'api',
    'as'         => 'api'
], function() {
    // Route::get('{hub_token}/up/{bot_token}/{status}/{hard?}','ApiController@up')->name('::up');
    // Route::get('{hub_token}/down','ApiController@down')->name('::down');
    Route::post('up','ApiController@up')->name('::up');
    Route::post('down','ApiController@down')->name('::down');
});

Route::group([
    'prefix' => 'account',
    'as'     => 'a'
], function () {

    Route::group(['middleware' => ['nonAuthed']], function() {

        Route::get('login','UserController@getLogin')->name('::login');
        Route::post('login','UserController@postLogin')->name('::login');
        Route::get('register','UserController@getRegister')->name('::register');
        Route::post('register','UserController@postRegister')->name('::register');
        Route::get('forgot','UserController@getForgot')->name('::forgot');
        Route::post('forgot','UserController@postForgot')->name('::forgot');
    });

    Route::group(['middleware' => ['authed']], function() {

        Route::get('verify','UserController@getVerify')->name('::verify');
        Route::post('verify','UserController@postVerify');

        Route::get('/verify-status', 'UserController@getVerifyStatus')->name('::verifyStatus');

        Route::get('/', function () {
            return redirect()->to(route('a::edit'),301);
        });

        Route::get('logout','UserController@logout')->name('::logout');
        Route::post('logout','UserController@logout')->name('::logout');
    });

    Route::group(['middleware' => ['authed','verified']], function() {

        Route::get('edit','UserController@edit')->name('::edit');
        Route::post('edit','UserController@update')->name('::update');

        Route::get('change-pass','UserController@getChangePass')->name('::changePass');
        Route::post('change-pass','UserController@postChangePass')->name('::changePass');

        Route::get('search/{query?}','UserController@search')->name('::search');
    });

});

Route::group([
    'prefix'     => 'hub',
    'as'         => 'h',
    'middleware' => ['authed','verified']
], function () {

    Route::get('index','HubController@index')->name('::index');
    Route::get('create','HubController@create')->name('::create');
    Route::post('create','HubController@store')->name('::create');
    Route::post('login','HubController@login')->name('::login');

    Route::group([
        'middleware' => ['hubLogedIn']
    ], function () {

        Route::get('/', function() {
            return redirect()->to(route('h::dashboard'),301);
        });

        Route::get('dashboard','HubController@dashboard')->name('::dashboard');

        Route::get('edit','HubController@edit')->name('::edit')->middleware('can:view');

        Route::get('logout','HubController@logout')->name('::logout');

        Route::group(['middleware' => 'can:editDelete'], function () {

            Route::post('edit','HubController@update')->name('::edit');

            Route::get('deactivate','HubController@deactivate')->name('::deactivate');

            Route::get('reactivate','HubController@reactivate')->name('::reactivate');

            Route::get('destroy','HubController@destroy')->name('::destroy');
        });

        Route::group([
            'prefix' => 'member',
            'as'     => '::m'
        ], function() {

            Route::get('/',function () {
                return redirect()->to(route('h::m::index'),301);
            });

            Route::get('index','MemberController@index')->name('::index')->middleware('can:viewAllMembers');

            Route::group(['middleware' => 'can:addMembers'], function () {

                Route::get('create','MemberController@create')->name('::create');
                Route::post('create','MemberController@store')->name('::create');
            });

            Route::get('{id}/edit','MemberController@edit')->name('::edit')->middleware('can:viewAllMembers');

            Route::group(['middleware' => 'can:editDeleteAllMembers'], function () {

                Route::post('{id}/edit','MemberController@update')->name('::edit');

                Route::post('{id}/deactivate','MemberController@deactivate')->name('::deactivate');
                Route::post('{id}/reactivate','MemberController@reactivate')->name('::reactivate');

                Route::post('{id}/destroy','MemberController@destroy')->name('::destroy');
            });

        });

        Route::group([
            'prefix' => 'bot',
            'as'     => '::b'
        ], function() {

            Route::get('/',function () {
                return redirect()->to(route('h::b::index'),301);
            });

            Route::get('index','BotController@index')->name('::index');

            Route::group(['middleware' => 'can:addBots'], function () {

                Route::get('create','BotController@create')->name('::create');
                Route::post('create','BotController@store')->name('::create');
            });

            Route::group(['middleware' => 'can:low'], function () {

                Route::get('{id}/edit','BotController@edit')->name('::edit');
                Route::get('control','BotController@control')->name('::control');
            });

            Route::group(['middleware' => 'can:high'], function () {

                Route::post('{id}/edit','BotController@update')->name('::edit');

                Route::post('{id}/deactivate','BotController@deactivate')->name('::deactivate');
                Route::post('{id}/reactivate','BotController@reactivate')->name('::reactivate');

                Route::post('{id}/destroy','BotController@destroy')->name('::destroy');
            });

            Route::get('search/{query?}/{query2?}','BotController@search')->name('::search');

        });

        Route::group([
            'prefix' => 'schedule',
            'as'     => '::s'
        ], function() {

            Route::get('/',function () {
                return redirect()->to(route('h::s::index'),301);
            });

            Route::get('index','ScheduleController@index')->name('::index');

            Route::get('{id}/edit','ScheduleController@edit')->name('::edit')->middleware('can:low');

            Route::group(['middleware' => 'can:createSchedules'], function () {

                Route::get('create','ScheduleController@create')->name('::create');
                Route::post('create','ScheduleController@store')->name('::create');
            });

            Route::group(['middleware' => 'can:high'], function () {

                Route::post('{id}/edit','ScheduleController@update')->name('::edit');

                Route::post('{id}/deactivate','ScheduleController@deactivate')->name('::deactivate');
                Route::post('{id}/reactivate','ScheduleController@reactivate')->name('::reactivate');

                Route::post('{id}/destroy','ScheduleController@destroy')->name('::destroy');
            });

        });

        Route::group([
            'prefix' => 'automation',
            'as'     => '::a'
        ], function() {

            Route::get('/',function () {
                return redirect()->to(route('h::a::index'),301);
            });

            Route::get('index','AutomationController@index')->name('::index');

            Route::get('{id}/edit','AutomationController@edit')->name('::edit')->middleware('can:low');

            Route::group(['middleware' => 'can:createAutomations'], function () {

                Route::get('create','AutomationController@create')->name('::create');
                Route::post('create','AutomationController@store')->name('::create');
            });

            Route::group(['middleware' => 'can:high'], function () {

                Route::post('{id}/edit','AutomationController@update')->name('::edit');

                Route::post('{id}/deactivate','AutomationController@deactivate')->name('::deactivate');
                Route::post('{id}/reactivate','AutomationController@reactivate')->name('::reactivate');

                Route::post('{id}/destroy','AutomationController@destroy')->name('::destroy');
            });

        });

        Route::group([
            'prefix' => 'event',
            'as'     => '::e'
        ], function() {

            Route::get('/',function () {
                return redirect()->to(route('h::e::index'),301);
            });

            Route::get('index','EventController@index')->name('::index');

            Route::group(['middleware' => 'can:low'], function () {

                Route::get('{id}/edit','EventController@edit')->name('::edit');
                Route::post('fire','EventController@fire')->name('::fire');

            });

            Route::group(['middleware' => 'can:createEvents'], function () {

                Route::get('create','EventController@create')->name('::create');
                Route::post('create','EventController@store')->name('::create');
            });

            Route::group(['middleware' => 'can:high'], function () {

                Route::post('{id}/edit','EventController@update')->name('::edit');

                Route::post('{id}/deactivate','EventController@deactivate')->name('::deactivate');
                Route::post('{id}/reactivate','EventController@reactivate')->name('::reactivate');

                Route::post('{id}/destroy','EventController@destroy')->name('::destroy');
            });

            Route::get('search/{query?}/{query2?}','EventController@search')->name('::search');
        });

        Route::group([
            'prefix' => 'notification',
            'as'     => '::n'
        ], function() {

            Route::get('/',function () {
                return redirect()->to(route('h::n::index'),301);
            });

            Route::get('index','NotificationController@index')->name('::index');

            Route::get('read','NotificationController@read')->name('::read');
        });

        Route::group([
            'prefix' => 'quickcontrol',
            'as'     => '::q'
        ], function() {

            Route::get('add','QuickControlController@add')->name('::add');

            Route::get('remove','QuickControlController@remove')->name('::remove');
        });

    });

});

Route::get('languague/{lang?}', function ($lang = en) {
    if (session('language') != $lang) {
        session()->set('language',$lang);
        return [true];
    }
})->name('lang');

// Route::any('{all}', function(){
//     abort(404);
// })->where('all', '.*');

Route::get('logs', '\Rap2hpoutre\LaravelLogViewer\LogViewerController@index');

Route::get('/twilio-voice-request', function() {
    return response('<?xml version="1.0" encoding="UTF-8"?><Response><Reject reason="busy" /></Response>')
        ->header('Content-Type', 'application/xml');
})->name('twilioVoiceRequest');

Route::post('/twilio-voice-status-callback', 'UserController@handleTwilioVoiceStatusCallback')->name('twilioVoiceStatusCallBack');

<?php

use Illuminate\Support\Facades\Auth;
use \Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'user','middleware' => ['auth','verified']],function(){
    Route::match(['get'],'/dashboard','UserController@dashboard')->name("vendor.dashboard");
    Route::post('/reloadChart','UserController@reloadChart');

    Route::get('/permanently_delete','UserController@permanentlyDelete')->name("user.permanently.delete");
    Route::get('/profile','UserController@profile')->name("user.profile.index");
    Route::post('/profile','UserController@profileUpdate')->name("user.profile.update");
    Route::get('/profile/change-password','PasswordController@changePassword')->name("user.change_password");
    Route::post('/profile/change-password','PasswordController@changePasswordUpdate')->name("user.change_password.update");
    Route::get('/booking-history','UserController@bookingHistory')->name("user.booking_history");


    Route::post('/wishlist','UserWishListController@handleWishList')->name("user.wishList.handle");
    Route::get('/wishlist','UserWishListController@index')->name("user.wishList.index");
    Route::get('/wishlist/remove','UserWishListController@remove')->name("user.wishList.remove");

    Route::group(['prefix'=>'verification'],function(){
        Route::match(['get'],'/','VerificationController@index')->name("user.verification.index");
        Route::match(['get'],'/update','VerificationController@update')->name("user.verification.update");
        Route::post('/store','VerificationController@store')->name("user.verification.store");
        Route::post('/send-code-verify-phone','VerificationController@sendCodeVerifyPhone')->name("user.verification.phone.sendCode");
        Route::post('/verify-phone','VerificationController@verifyPhone')->name("user.verification.phone.field");
    });

    Route::group(['prefix'=>'/booking'],function(){
        Route::get('{code}/invoice','BookingController@bookingInvoice')->name('user.booking.invoice');
    });

    Route::match(['get'],'/upgrade-vendor','UserController@upgradeVendor')->name("user.upgrade_vendor");

    Route::get('wallet','WalletController@wallet')->name('user.wallet');
    Route::get('wallet/buy','WalletController@buy')->name('user.wallet.buy');
    Route::post('wallet/buyProcess','WalletController@buyProcess')->name('user.wallet.buyProcess');

    Route::get('chat','ChatController@index')->name('user.chat');

    Route::group(['prefix'=>'/2fa'],function(){
        Route::get('/','TwoFactorController@index')->name('user.2fa');
    });
});

Route::group(['prefix'=>config('chatify.routes.prefix'),'middleware'=>'auth'],function(){
    Route::get('/{id?}', 'MessagesController@iframe')->name(config('chatify.path'));
    Route::get('search','MessagesController@search')->name('search');
    Route::get('getContacts', 'MessagesController@getContacts')->name('contacts.get');
    Route::post('idInfo', 'MessagesController@idFetchData');
    Route::post('sendMessage', 'MessageController@send')->name('send.message');
});


Route::group(['prefix'=>'profile'],function(){
    Route::match(['get'],'/{id}','ProfileController@profile')->name("user.profile");
    Route::match(['get'],'/{id}/reviews','ProfileController@allReviews')->name("user.profile.reviews");
    Route::match(['get'],'/{id}/services','ProfileController@allServices')->name("user.profile.services");
});

//Newsletter
Route::post('newsletter/subscribe','UserController@subscribe')->name('newsletter.subscribe');


//Custom User  Register

Route::get('register','Auth\RegisterController@showRegistrationForm')->name('auth.register');
Route::post('register','Auth\RegisterController@register')->name('auth.register.store');

Route::get('/user/my-plan','PlanController@myPlan')->name('user.plan')->middleware(['auth', 'verified']);
Route::get('/plan','PlanController@index')->name('plan');
Route::get('/plan/thank-you','PlanController@thankYou')->name('user.plan.thank-you');
Route::get('/user/plan/buy/{id}','PlanController@buy')->name('user.plan.buy')->middleware(['auth', 'verified']);
Route::post('/user/plan/buyProcess/{id}','PlanController@buyProcess')->name('user.plan.buyProcess')->middleware(['auth', 'verified']);

//BECOME
use Modules\User\Controllers\UserController;


 
//agency
use App\Http\Controllers\AgencyController;
Route::group(['prefix'=>'admin'],function(){
    Route::get('/agencies', [AgencyController::class, 'index'])->name('admin.agencies.index');
    Route::get('/agencies/create', [AgencyController::class, 'create'])->name('admin.agencies.create');
    Route::post('/agencies', [AgencyController::class, 'store'])->name('admin.agencies.store');
    Route::get('/agencies/{agency}/edit', [AgencyController::class, 'edit'])->name('admin.agencies.edit');
    Route::put('/agencies/{agency}', [AgencyController::class, 'update'])->name('admin.agencies.update');
    Route::delete('/agencies/{agency}', [AgencyController::class, 'destroy'])->name('admin.agencies.destroy');
});


use App\Http\Controllers\RoleRequestController;

Route::middleware(['auth'])->group(function () {
    Route::get('/become-agent', [UserController::class, 'showBecomeAgentForm'])->name('user.becomeAgentForm');
    Route::get('/become-distributor', [UserController::class, 'showBecomeDistributorForm'])->name('user.becomeDistributorForm');
    Route::post('/change-role', [UserController::class, 'changeUserRole'])->name('user.changeRole');
    // Ajoutez d'autres routes ici si nécessaire
});


use App\Http\Controllers\TestController;

Route::get('/test-notification', [TestController::class, 'testNotification']);






// routes for agent and distributor requests
Route::get('/admin/agentUpgradeRequest', 'UserController@agentUpgradeRequest')->name('user.admin.agentUpgrade');
Route::get('/admin/distributorUpgradeRequest', 'UserController@distributorUpgradeRequest')->name('user.admin.distributorUpgrade');
Route::get('/admin/roleUpgradeApprove/{id}', 'UserController@roleUpgradeApprove')->name('user.admin.roleUpgradeApprove');
Route::get('/admin/roleUpgradeDecline/{id}', 'UserController@roleUpgradeDecline')->name('user.admin.roleUpgradeDecline');
// Define the route to display the upgrade requests
Route::get('/admin/roleUpgradeRequests', 'UserController@roleUpgradeRequests')->name('user.admin.roleUpgrade');

use Modules\Tour\Controllers\TourController;

Route::post('/tour/check-availability/{tourId}', [TourController::class, 'checkBookingAvailability']);

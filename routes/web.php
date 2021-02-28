<?php

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

Route::get('/', 'HomeController@index');

Auth::routes();
Route::get('google', 'GoogleController@redirect');
Route::get('google/callback', 'GoogleController@callback');

Route::get('/home', 'HomeController@index')->name('home');


Route::get('transaction/{order}/batal', 'TransactionController@cancel')->name('transaction.cancel');
Route::get('transaction/{order}/uncancel', 'TransactionController@uncancel')->name('transaction.uncancel');
Route::resource('transaction', 'TransactionController');
Route::resource('/addon/package', 'Addon\PackageController');
Route::resource('/addon/voucher', 'Addon\VoucherController');
Route::resource('search/transactions', 'SearchTransactionController');
Route::post('search/transactions/guest', 'SearchTransactionController@searchSingle')->name('transactions.store2');
Route::get('voucher/trash', 'VouchersController@trash')->name('voucher.trash');
Route::get('voucher/restore/{voucher}', 'VouchersController@restore')->name('voucher.restore');
Route::get('voucher/delete-permanen/{voucher}', 'VouchersController@delpermanen')->name('voucher.delpermanen');
Route::resource('voucher', 'VouchersController');

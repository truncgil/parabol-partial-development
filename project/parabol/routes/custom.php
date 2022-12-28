<?php

use Illuminate\Support\Facades\Route;


Route::get('/cron', function(){
   return view("cron");
});
Route::get('/efatura', function(){
   return view("efatura");
});


Route::get('/clear-cache', function() {

   Artisan::call('cache:clear');
   Artisan::call('config:clear');
   Artisan::call('view:clear');
   return "Cache is cleared";
});
Route::match(array('GET','POST'),'/{id}', function($id){
   return view("default")->with('id',$id);
 
});

Route::get('/artisan-optimize', function() {
   Artisan::call('optimize');
   return "optimized";
});
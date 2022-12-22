<?php

use Illuminate\Support\Facades\Route;


Route::match(array('GET','POST'),'/{id}', function($id){
   return view("ext.default")->with('id',$id);
 
});


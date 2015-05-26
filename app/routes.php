<?php


Route::get('/', function()
{
    return Redirect::to('/analyzer');
});

Route::get('/analyzer', function()
{
    return View::make('analyzer');
});

Route::post('/analyzer', array('uses' => 'EaController@lexical'));
Route::get('/parse', array('uses' => 'EAController@one_parser'));
Route::get('/completeparse', array('uses' => 'EAController@complete_parser'));
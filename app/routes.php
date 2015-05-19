<?php



Route::get('/analyzer', function()
{
    return View::make('analyzer');
});

Route::post('/analyzer', array('uses' => 'EAController@lexical'));
Route::get('/parse', array('uses' => 'EAController@one_parser'));
Route::get('/completeparse', array('uses' => 'EAController@complete_parser'));
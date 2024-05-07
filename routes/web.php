<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Mail\PostApprovalNotification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () {
    // Mail::to('namprohang2@gmail.com')->send(new PostApprovalNotification());
    // return response()->json(['message' => 'Hello World!']);
    $post = Post::find(10);
    $author = User::find($post->user_id)->email;

    dd($author);

});

$router->get('send_email' ,'Mailcontroller@mail');


$router->group(['prefix'=> 'api'], function () use ($router) {
    $router->get('/posts','PostController@index');
    $router->post('/login','AuthController@login');
    $router->post('/register','AuthController@register');
    $router->get('/posts/show/{id}','PostController@show');
    $router->get('/categories','CategoryController@index');
    $router->post('/categories','CategoryController@store');
    $router->delete('/categories/{id}','CategoryController@destroy');

    $router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/logout','AuthController@logout');
    $router->post('/posts/{id}/comment','CommentController@store');
    $router->put('/posts/comment/{id}','CommentController@update');
    $router->delete('/posts/comment/{id}','CommentController@destroy');

    //favorite routes
    $router->get('/favourites','FavouriteController@index');
    $router->post('/favourites','FavouriteController@store');
    $router->delete('/favourites/{id}','FavouriteController@destroy');
    //rating routes
    $router->post('/ratings','RatingController@store');


    //author routes
    $router->post('/posts','PostController@store');
    $router->put('/posts/{id}','PostController@update');
    $router->get('/posts/approvedbyauthor','PostController@approvedByAuthor');
    $router->get('/posts/pendingbyauthor','PostController@pendingByAuthor');
    $router->get('/posts/rejectedbyauthor','PostController@rejectedByAuthor');

    
    
    //Admin routes
    $router->get('/admin/pending','PostController@pending');
    $router->get('/admin/approved','PostController@approved');
    $router->get('/admin/rejected','PostController@rejected');
    $router->put('/admin/approve/{id}','PostController@approve');
    $router->put('/admin/reject/{id}','PostController@reject');
    $router->put('/admin/editpermissions','AuthController@editPermissions');
    });

   

});


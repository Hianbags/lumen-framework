<?php

namespace App\Console;

use App\Mail\PostApprovalNotification;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Mail;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    $schedule->call(function () {
        $admin = User::where('role', 'admin')->first()->email;
        $posts = Post::where('status', 0)->where('created_at', '<', Date::now()->subMinutes(1))->get();
        foreach ($posts as $post) {
            if (!Notification::where('post_id', $post->id)->exists()) {
                $author = User::find($post->user_id)->email;
                $notification = new Notification();
                $notification->post_id = $post->id;
                $notification->message = 'Bài viết #' .  $post->id . ' chưa được duyệt.';
                $notification->save();
                Mail::to($admin)->send(new PostApprovalNotification($post));
                Mail::to($author)->send(new PostApprovalNotification($post));

            }
        }
    })->everyMinute();
    
    $schedule->call(function () {
        $posts = Post::where('status', 0)->where('created_at', '<', Date::now()->subMinutes(2))->get();
        foreach ($posts as $post) {
            $post->status = 1;
            $post->save();
        }
    })->everyMinute();
}

    
}

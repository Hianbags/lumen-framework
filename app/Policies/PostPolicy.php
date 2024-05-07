<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any posts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the post.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function view(User $user, Post $post)
    {
        //
    }
    //editPermissions
    public function editPermissions(User $user)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can create posts.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->role === 'author' || $user->role === 'admin';
    }
    public function approve(User $user)
    {
        return $user->role === 'admin';
    }
    public function reject(User $user)
    {
        return $user->role === 'admin';
    }
    public function pending(User $user)
    {
        return $user->role === 'admin';
    }
    public function approved(User $user)
    {
        return $user->role === 'admin';
    }
    public function rejected(User $user)
    {
        return $user->role === 'admin';
    }
    //category
    public function storeCategory(User $user)
    {
        return $user->role === 'admin';
    }
    /**
     * Determine whether the user can update the post.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function update(User $user, Post $post)
    {
        return $post->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the post.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Post  $post
     * @return mixed
     */
    public function delete(User $user, Post $post)
    {
        //
    }
}

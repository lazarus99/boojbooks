<?php

namespace App\Policies;

use App\User;
use App\Book;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    
    /**
     * ensure the user is allowed to delete this book
     * (i.e. user owns book)
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function destroy(User $user, Book $book){
        return $user->id === $book->user_id;
    }
    
    /**
     * makre sure user can make changes to the book
     * @param User $user
     * @param Book $book
     * @return bool
     */
    public function edit(User $user, Book $book){
        return $user->id ===$book->user_id;
    }
}

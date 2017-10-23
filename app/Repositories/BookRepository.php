<?php

/**
 * Data repository layer- helps combine models outside of controllers or views
 */

namespace App\Repositories;

use App\User;
use App\Book;

class BookRepository {

    /**
     * Tie user to book model to keep users in their own book list
     * @param User $user injected user object
     * @return array    result array of StdClass
     */
    public function userBooks(User $user) {
        return Book::where('user_id', $user->id)
                        ->orderBy('read_order')
                        ->get();
    }

    /**
     * Get the highest read order value, so we can place book at end of list
     * @param User $user
     * @return obj  result object
     */
    public function next_in_read_order(User $user) {
        return Book::where('user_id', $user->id)->max('read_order');
    }

    /**
     * Get the details for an individual book. COnfirm it's a user owned book
     * @param User $user    user ID to make sure this user should see the book
     * @param type $book    book ID to query
     * @return obj  result object
     */
    public function bookDetails(User $user, $book) {
        return Book::where('user_id', $user->id)
                        ->where('id', $book)
                        ->first();
    }

    /**
     * Remove the speicified book fomr the list.
     * @param User $user
     * @param type $book
     * @return type
     */
    public function destroyBook(User $user, $book) {
        return Book::findOrFail($book)
                        ->delete($book);
    }

    /**
     * update the reading order o all of the books
     * for a user. Rollsback on error, otherwise commits everything
     * @param User $user
     * @param type $changes
     * @return boolean
     * @throws \App\Repositories\Exception
     */
    public function updatePositions(User $user, $changes) {
        $sent = json_decode($changes);
        foreach ($sent as $change) {
            try {
                Book::where('id', $change->book_id)
                        ->where('user_id', $user->id)
                        ->update(['read_order' => $change->order]);
            } catch (Exception $ex) {
                Book::rollback;
                throw $ex;
            }
        }
        return true;
    }

}

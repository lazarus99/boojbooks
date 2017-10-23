<?php

namespace App\Http\Controllers;

use App\Book;
use Illuminate\Http\Request;
use App\Repositories\BookRepository;

class BookController extends Controller {

    protected $books;

    /**
     * constructor forces login, hits data repo
     */
    public function __construct(BookRepository $books) {
        $this->middleware('auth');
        $this->books = $books;
    }

    /**
     * Load HTML base page
     * @param $request  http request, should have user_id
     * @param   response    renders blade template
     */
    public function index(Request $request) {
        return view('bookList');
    }

    /**
     * Ajax only function to return a users book list
     * @param Request $request
     * @return text JSON respons
     */
    public function load_data(Request $request) {
        if ($request->ajax()) {
            $books = $this->books->userBooks($request->user());
            return response()->json($books);
        }
    }

    /**
     * save new books for a user. put book at end of read order
     * @param request   HTTP reuest
     * @return  response
     */
    public function save(Request $request) {
        if ($request->ajax()) {
            $this->validate($request, [
                'title' => 'required|max:255',
                'author' => 'required|max:255',
                'publish_date' => 'nullable|date',
                'notes' => 'nullable|max:255'
            ]);

            //get the last read order number, and increment by 1 to put the new book last
            $next_read_order = $this->books->next_in_read_order($request->user()) + 1;

            //save the book
            $request->user()->books()->create([
                'author' => $request->author,
                'title' => $request->title,
                'publish_date' => $request->publish_date,
                'notes' => $request->notes,
                'user_id' => $request->user(),
                'read_order' => $next_read_order
            ]);
        }
    }

    /**
     * delete a book. Injects book model record based on URL
     * @param Request $request
     */
    public function remove(Request $request, Book $book) {
        $this->authorize('destroy', $book);

        if ($request->ajax()) {
            if (isset($request->book)) {
                $book_id = $request->id;
                if ($book->delete()) {
                    $ok = array('status' => 'success');
                    return response()->json($ok);
                }
            }
        }
    }

    /**
     * load details for a single user book
     * @param Request $request
     * @return type
     */
    public function load_detail(Request $request) {
        if ($request->ajax()) {
            if (isset($request->book)) {
                $book_id = $request->book;
                $book_detail = $this->books->bookDetails($request->user(), $book_id);
                return response()->json($book_detail);
            }
        }
    }

    /**
     * Updates all of the reod_orders of a users books
     * @param Request $request
     * @return type
     */
    public function updateList(Request $request) {
        if ($request->ajax()) {
            if (isset($request->list)) {
                $changes = $request->list;
                $updates = $this->books->updatePositions($request->user(), $changes);
                $status = ($updates) ? array('status' => 'ok') : array('status' => 'error');
                return response()->json($status);
            }
        }
    }

    /**
     * Update the details on an individual record
     * Injects book model record based on URL
     * @param Request $request
     * @param Book $book
     */
    public function updateBook(Request $request, Book $book) {
        $this->authorize('edit', $book);
        
        if ($request->ajax()) {
            $this->validate($request, [
                'edit_title' => 'required|max:255',
                'edit_author' => 'required|max:255',
                'edit_publish_date' => 'nullable|date',
                'edit_notes' => 'nullable|max:255'
            ]);
            

            //update the book
            $book->author = $request->edit_author;
            $book->title = $request->edit_title;
            $book->publish_date = $request->edit_publish_date;
            $book->notes = $request->edit_notes;
            $saved = $book->save();
            
            if($saved){
                $status = array('status' => 'ok');
                return response()->json($status);
            }
        }
    }

}

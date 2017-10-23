@extends('layouts.app')
@section('content')

<!--add book modal-->
<div class="modal fade modal-lg" id="add_book_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4> Add A New Book</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="add_book">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="author">Author</label>
                            <input type="text" class="form-control" name="author" id="author"/>
                        </div>
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" name="title" id="title"/>
                        </div>
                        <div class="form-group">
                            <label for="publish_date">Published Date</label>
                            <input type="text" class="form-control" name="publish_date" id="publish_date" />
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea name="notes" id="notes" class="form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save_book" class="btn btn-primary">Save Book</button>
            </div>
        </div>
    </div>
</div>
<!--add book modal ends-->
<!--book detail modal-->
<div class="modal fade" id="book_detail_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                Book Details
            </div>
            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <h4>Title: </h4><span id="detail_title"></span>
                        <h4>Author: </h4><span id="detail_author"></span>
                        <h4>Published: </h4><span id="detail_published"></span>
                        <h4>Notes:</h4>
                        <p><span id="detail_notes"></span></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!--detail modal ends-->
<!--edit record modal-->
<div class="modal fade modal-lg" id="edit_book_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4> Edit Book</h4>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="edit_book">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="edit_author">Author</label>
                            <input type="text" class="form-control" name="edit_author" id="edit_author"/>
                        </div>
                        <div class="form-group">
                            <label for="edit_title">Title</label>
                            <input type="text" class="form-control" name="edit_title" id="edit_title"/>
                        </div>
                        <div class="form-group">
                            <label for="edit_publish_date">Published Date</label>
                            <input type="text" class="form-control" name="edit_publish_date" id="edit_publish_date" />
                        </div>
                        <div class="form-group">
                            <label for="edit_notes">Notes</label>
                            <textarea name="edit_notes" id="edit_notes" class="form-control"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" id="save_edits" class="btn btn-primary" data-book-id="">Save Book</button>
            </div>
        </div>
    </div>
</div>
<!--edit modal ends-->
<!--list starts-->
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <h3>{{Auth::user()->name}}'s Reading List</h3>
        </div>
        <div class="col-md-8">
            <button type='button' class="btn btn-success" id="add" data-toggle="modal" data-target="#add_book_modal">Add Book</button>
            <button tyep="button" class="btn btn-warning" id="save_changes">Save Changes</button>
            <a href="{{url('/logout')}}" class="btn btn-warning">Logout</a>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <p>To add books to your reading list, click the "Add Book" button above. You can sort sort the 
                list by Author, Title or Publish date. You can also click and drag each book in the list into the order
                you like. Once the list is in the order you want, hit the Save List button to save your changes.</p>
        </div>
    </div>
    <div class="row">
        <table class="table table-striped table-responsive" id="book_list">
            <thead>
                <tr>
                    <th>Details</th>
                    <th>Edit</th>
                    <th>Author</th>
                    <th>Title</th>
                    <th>Date Published</th>
                    <th>Remove Book</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>
@endsection
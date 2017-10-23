$(document).ready(function () {

    /**
     * Base functions and vars for books app
     */

    var loading_spinner = "<tr><td colspan='5'><img src='" + _spinner + "'></td></tr>";
    var table = $('book_list');

    //disable column sort out of the 
    $("table thead th:eq(0)").data("sorter", false);
    $("table thead th:eq(5)").data("sorter", false);
    $("table thead th:eq(1)").data("sorter", false);

    /**
     * force token to be sent with all AJAX requests
     */
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    /**
     * loads users books
     * should work with promise interface
     * @returns {jqXHR}
     */
    function load_books() {
        return $.ajax({
            url: _site + '/load_data',
            method: 'GET'
        });
    }

    /**
     * build table rows from data returned from server
     * @param {JSON} data JSON returned from server  
     * @returns {String} formatted HTML string for DOM injection
     */
    function build_table(data) {
        let table;
        $(data).each(function (idx, val) {
            table += "<tr class='book' data-book-id='" + val.id + "'>";
            table += "<td><a><span class='glyphicon glyphicon-search details'></span></a></td>";
            table += "<td><a><span class='glyphicon glyphicon-pencil edit'</span></a></td>";
            table += "<td>" + val.author + "</td>";
            table += "<td>" + val.title + "</td>";
            table += "<td>" + val.publish_date + "</td>";
            table += "<td><button type='button' class='btn btn-danger remove'>Remove</button>";
            table += "</tr>";
        });
        return table;
    }

    /**
     * perform a full load of the data, and draw the table.
     * Includes swapping table with loading spinner
     * @returns {undefined}
     */
    function load_list() {
        $('#book_list tbody').html(loading_spinner);
        let loader = load_books()
                .done(function (data) {
                    if ($.isEmptyObject(data)) {
                        $('#book_list tbody').html(
                                '<tr><td colspan="5">You should read more! You have nothing in your reading list! Hit the "Add Books" button above to start your reading list.</td></tr>');
                    } else {
                        let rows = build_table(data);
                        $('#book_list tbody').html(rows);
                        $("#book_list").tablesorter({
                            theme: 'bootstrap',
                            widgets: ['uitheme', 'metadata'],
                            headers: {
                                '.detail .deletes': {
                                    sorter: false
                                }
                            },
                            headerTemplate: '{content} {icon}' // needed to add icon for jui themeÏ
                        });
                    }
                });

    }


    /**
     * Save the books data entered by the user to the DB
     * @returns {undefined}
     */
    function save_book() {
        if (!$('#author').val() || !$('#title').val()) {
            alert('Author and Title are required fields');
        } else {
            $.ajax({
                url: _site + '/new',
                method: 'POST',
                data: $('#add_book').serialize(),
                success: function (data) {
                    clear_fields();
                    load_list();
                    $('#add_book_modal').modal('hide');
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    let message;
                    $(xhr.responseText).each(function (idx, val) {
                        message += val;
                    });
                    alert(message);
                }
            });
        }
    }

    /**
     * deletes a book from the list
     * @param {int} book book id to remove
     * @returns {undefined}
     */
    function remove_book(book) {
        if (confirm('Are you sure you want to remove this book?')) {
            $.ajax({
                url: _site + '/removebook/' + book,
                method: 'DELETE',
                dataType: 'json',
                data: {book: book},
                success: function (data) {
                    if (data.status === 'success') {
                        alert('This book has been deleted');
                        load_list();
                    }
                },
                error: function (xhr, opt, error) {
                    alert(xhr.responseText + error);
                }
            });
        }
    }


    /**
     * Update the edit form with the details to be edited
     * @param {type} book
     * @returns {undefined}
     */
    function populate_edits(details) {
        $('#edit_title').val(details.title);
        $('#edit_author').val(details.author);
        $('#edit_notes').val(details.notes);
        $('#edit_publish_date').val(details.publish_date);
        $('#save_edits').attr('data-book-id', details.id);
    }


    /**
     * save changes made to the record
     * @returns {jqXHR}
     */
    function save_edits(book) {
        return $.ajax({
            url: _site + '/edit/' + book,
            method: 'POST',
            data: $('#edit_book').serialize()
        });
    }


    /**
     * Utility function to clear fields on succesful save
     * @returns {undefined}
     */
    function clear_fields() {
        $('#title').val('');
        $('#publish_date').val('');
        $('#notes').val('');
        $('#author').val('');
    }

    /**
     * promise for book details
     * @param {type} book
     * @returns {jqXHR}
     */
    function book_details(book) {
        return  $.ajax({
            url: _site + '/details',
            method: 'GET',
            dataType: 'JSON',
            data: {book: book}
        });
    }

    /**
     * append the details to the correct spots in the modal
     * @param {object} details
     * @returns {undefined}
     */
    function update_details(details) {
        $('#detail_title').text(details.title);
        $('#detail_author').text(details.author);
        $('#detail_notes').text(details.notes);
        $('#detail_published').text(details.publish_date);
    }


    /**
     * scan through the table and put together an array of objects.
     * Book ID and order in list.
     * @returns {Array}
     */
    function read_changes() {
        var rev_order = [];
        $("#book_list > tbody > tr").each(function (idx, elm) {
            var book = new Object();
            book.book_id = $(this).data('book-id');
            book.order = $(this).index();

            rev_order.push(book);
        });
        return rev_order;
    }

    /**
     * setup for promise, sensds updated list positions
     * @param {object} list array with book ids and list positions
     * @returns {jqXHR}
     */
    function update_list(list) {
        return $.ajax({
            url: _site + '/updatelist',
            method: 'POST',
            data: {list: list},
            dataType: 'json'
        });
    }


    /**
     * saves order changes to the list to the DB, and reloads the list if server returns OK
     * @returns {undefined}
     */
    function save_changes() {
        var updates = JSON.stringify(read_changes());
        var saved = update_list(updates)
                .done(function (data) {
                    load_list();
                })
                .fail(function (data) {
                    var msg = error_msg(data);
                    alert(msg);
                });
        ;
    }

    load_list();


    //append event listeners
    $('#save_book').click(function () {
        save_book();
    });

    //save list changes listener
    $('#save_changes').click(function () {
        save_changes();
    });

    //save changes to an individual record
    $('#save_edits').click(function () {
        var book = $(this).attr('data-book-id');
        save_edits(book)
                .done(function (data) {
                    clear_fields();
                    load_list();
                    $('#edit_book_modal').modal('hide');
                })
                .fail(function (data) {
                    var msg = error_msg(data);
                    alert(msg);
                });
    });

    /**
     * Parse error message JSON from server to readable text
     * @param {string} response JSON error response
     * @returns {string}
     */
    function error_msg(response) {
        var returned = response.responseJSON;
        var msg;
        $.each(returned, function (i, v) {
            msg += ' ' + returned[i];
        });

        return msg;
    }

    //delete button listener
    $(document.body).on('click', '.remove', function () {
        var book = $(this).closest('tr').data('book-id');
        remove_book(book);
    });

    //details icon listener
    $(document.body).on('click', '.details', function () {
        var book = $(this).closest('tr').data('book-id');
        var details = book_details(book)
                .done(function (data) {
                    update_details(data);
                    $('#book_detail_modal').modal('show');
                })
                .fail(function (data) {
                    var msg = error_msg(data);
                    alert(msg);
                });
    });

    //edit book listener
    $(document.body).on('click', '.edit', function () {
        var book = $(this).closest('tr').data('book-id');
        var edits = book_details(book)
                .done(function (data) {
                    populate_edits(data);
                    $('#edit_book_modal').modal("show");
                })
                .fail(function (data) {
                    var msg = error_msg(data);
                    alert(msg);
                });
    });


    /**
     * JQueryUI sortable init
     * disable for clickable elements
     * @returns {undefined}
     */
    function makeMove() {
        $("tbody").sortable({
            connectWith: "tbody",
            cancel: ".glyphicon .delete .edit"
        });
    }

    makeMove();

    //append date pickers
    $('#publish_date').datepicker({
        changeMonth: true,
        changeYear: true
    });


    $('#edit_publish_date').datepicker({
        changeMonth: true,
        changeYear: true
    });

    //datepicker additional settings
    $("#publish_date").datepicker("option", "dateFormat", "yy-mm-dd");
    $("#edit_publish_date").datepicker("option", "dateFormat", "yy-mm-dd");

});


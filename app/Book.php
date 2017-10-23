<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    /*
     * mass assignable attributes
     */
    protected $fillable = [
        'author', 'title', 'publish_date', 'read_order', 'notes'
    ];
    
    
    /**
     * Tie user to book
     * @return type
     */
    public function user(){
        return $this->belongsTo(User::class);
    }

    
}

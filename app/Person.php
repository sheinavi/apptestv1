<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class Person extends Model
{
    use FullTextSearch;
    
    protected $table = "persons";
    protected $searchable = [
        'firstName',
        'lastName'
    ];
}

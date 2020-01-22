<?php


namespace App\Models\Web;


class Lead extends \Illuminate\Database\Eloquent\Model
{

    protected $fillable = ['data', 'type'];

    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';

    protected $table = 'leads';

}
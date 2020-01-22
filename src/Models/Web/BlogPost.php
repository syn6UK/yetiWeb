<?php


namespace App\Models\Web;


class BlogPost extends \Illuminate\Database\Eloquent\Model
{

    protected $fillable = [
        'title',
        'content',
        'thumbnail',
        'author',
        'status',
        'seoTitle',
        'seoDescription',
        'ogTitle',
        'ogDescription',
        'ogThumb',
        'url'
    ];

    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';

    protected $table = 'posts';

}






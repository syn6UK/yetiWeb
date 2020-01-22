<?php


namespace App\Models\Web;


class CaseStudy extends \Illuminate\Database\Eloquent\Model
{

    protected $fillable = [
        'company',
        'introText',
        'description',
        'logo',
        'image1',
        'image2',
        'seoTitle',
        'seoDescription',
        'ogTitle',
        'ogDescription',
        'ogThumb'
    ];

    const CREATED_AT = 'dateCreated';
    const UPDATED_AT = 'dateUpdated';

    protected $table = 'case_studies';

}
<?php

namespace App\Modules;


class ThemeEngine
{

    public $themeDirectory = 'default/';

    public function __construct($themeDirectory = 'default/')
    {

       $this->themeDirectory = $themeDirectory;

    }

}
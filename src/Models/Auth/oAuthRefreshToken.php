<?php

namespace App\Models\Auth;

class oAuthRefreshToken extends \Illuminate\Database\Eloquent\Model{
	protected $table = 'oauth_refresh_tokens';
	public $timestamps = false;
}
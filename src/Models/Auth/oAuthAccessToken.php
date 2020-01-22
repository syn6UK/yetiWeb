<?php

namespace App\Models\Auth;

class oAuthAccessToken extends \Illuminate\Database\Eloquent\Model{

	protected $fillable = ["access_token", "user_id", "client_id", "expires", "scope"];

	protected $table = 'oauth_access_tokens';
	public $timestamps = false;
	protected $hidden = array('id');

}
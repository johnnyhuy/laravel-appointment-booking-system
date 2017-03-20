<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public static function loginCustomer($username, $password) {
		return false;
	}
	
	public static function registerCustomer($name, $address, $username, $password, $phone) {
		return false;
	}
}

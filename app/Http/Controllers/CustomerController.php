<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Customer;

class CustomerController extends Controller
{
    public static function loginCustomer($username, $password) {
		return false;
	}
	
	public static function registerCustomer($name, $address, $username, $password, $phone) {
		//Make sure there are no empty fields
		if(empty(trim($name)) ||
			empty(trim($address)) ||
			empty(trim($username)) ||
			empty(trim($password)) ||
			empty(trim($phone))) {
			return false;
		}
		
		//Check the phone number is valid (regex for all phone numbers generted by faker class)
 		if(!preg_match('/\A[+]{0,1}[0-9 ()-.]{8,20}[x]{0,1}[0-9]{1,6}\z/', $phone)) {
			return false;
		}
		
		//Check for conflicting usernames in the customers and business_owners database tables
		
		//SELECT * FROM Customers WHERE username = $username
		$conflictingCustomers = DB::table('customers')->where('username', '=', $username)->get();
		if(count($conflictingCustomers) > 0) {
			echo 'username already in use <br>';
			return false;
		}
		//SELECT * FROM BusinessOwner WHERE username = $username
		$conflictingCustomers = DB::table('business_owners')->where('username', '=', $username)->get();
		if(count($conflictingCustomers) > 0) {
			echo "username already in use <br>";
			return false;
		}
		
		//Validation successful, create customer object
		$customer = new Customer;
		$customer->name = $name;
		$customer->address = $address;
		$customer->username = $username;
		$customer->password = $password;
		$customer->phone = $phone;
		//Push customer data to the database
		$customer->save();
		
		//Otherwise registration was successful, push data to database
		return true;
	}

	
	public function create() {
		$customer_name = request('firstName') . ' ' . request('lastName');
		$customer_address = request('address');
		$customer_username = request('username');
		$customer_password = request('password');
		$customer_phone = request('phone');
		
		if(CustomerController::registerCustomer($customer_name, 
							$customer_address,
							$customer_username,
							$customer_password,
							$customer_phone)) {
			//TODO: Implement what to do when registration is successful
			return view('login.index');
		}
		else {
			echo 'registration_unsuccessful';
			return view('register.index');
		}
	}
}

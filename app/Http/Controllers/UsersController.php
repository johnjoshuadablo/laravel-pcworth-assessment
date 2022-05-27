<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\User;


class UsersController extends Controller
{

	public function loogin(Request $request)
	{
		if (Auth::attempt([
			'username' => $request->username,
			'password' => $request->password
		])) {
			$user = Auth::user();
			return response()->json([
				'success' => true,
				'user' => $user,
			], 200);
		} else {
			return response()->json([
				'error' => 'Unauthorized Access',
				'success' => false,
				'message' => 'Unauthorized Access',
			], 203);
		}
	}

	public function reegister(Request $request)
	{
		$validation = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'username' => 'required',
			'email' => 'required',
			'password' => 'required',
			'confirm_password' => 'required|same:password',
		]);
		if ($validation->fails()) {
			return response()->json([
				'success' => false,
				'error' => $validation->errors(),
				'message' => 'Registration failed.'
			], 202);
		}

		$requestData = $request->all();
		$requestData['password'] = bcrypt($requestData['password']);
		$user = User::create($requestData);

		return response()->json([
			'success' => true,
			'message' => $user->username . ', Registration Success.'
		], 200);
	}

	public function uupdate(Request $request, $id)
	{
		// Check for validation
		$validation = Validator::make($request->all(), [
			'first_name' => 'required',
			'last_name' => 'required',
			'username' => 'required',
		]);
		if ($validation->fails()) {
			return response()->json([
				'success' => false,
				'error' => $validation->errors(),
				'message' => 'Some fields are required failed.'
			], 202);
		}

		$user = User::find($id);
		if ($user) {
			// Encrypt password
			if ($request->password) {
				$request['password'] = bcrypt($request->password);
			}
			$input = $request->all();
			$user->fill($input)->save();

			return [
				'success' => true,
				'user' => $user,
				'message' => 'User updated.'
			];
		}

		return [
			'success' => false,
			'message' => 'User not updated.'
		];
	}

	public function ggetUser(Request $request, $id)
	{
		$user = User::find($id);
		if ($user) {
			return [
				'success' => true,
				'user' => $user,
				'message' => 'User info.'
			];
		}

		return [
			'success' => false,
			'message' => 'User not found.'
		];
	}
}

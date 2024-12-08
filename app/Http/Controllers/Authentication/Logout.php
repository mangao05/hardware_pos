<?php
namespace App\Http\Controllers\Authentication;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class Logout extends Controller
{
    public function __invoke(Request $request)
    {
        Auth::logout(); // Log the user out of the application

        // Invalidate and regenerate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withSuccess('You have successfully logged out!');
    }
}

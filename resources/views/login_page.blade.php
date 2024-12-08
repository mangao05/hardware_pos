<!DOCTYPE html>
<!-- Coding By CodingNepal - www.codingnepalweb.com -->
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pantukan Waterworld Beach Resort</title>
  <link rel="stylesheet" href="{{ asset('css/login.css') }}">
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <script src="{{ asset('js/bootstrap.js') }}"></script>
</head>
<body>
  <div class="wrapper">
    <div class="row justify-content-center">
      <div class="col-8">
        <img class="img-fluid" src="pantukan_logo.png" alt="">
      </div>
    </div>
    <form action="#">
      <h2>Login</h2>
        <div class="input-field">
        <input type="text" required>
        <label>Enter your username</label>
      </div>
      <div class="input-field">
        <input type="password" required>
        <label>Enter your password</label>
      </div>
      <div class="forget">
        <label for="remember">
          <input type="checkbox" id="remember">
          <p>Remember me</p>
        </label>
        <a href="#">Forgot password?</a>
      </div>
      <button type="submit">Log In</button>
      {{-- <a href="{{ url('/dashboard') }}" type="button">Log In</a> --}}
    </form>
  </div>
</body>
</html>
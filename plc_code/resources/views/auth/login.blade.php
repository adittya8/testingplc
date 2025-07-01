@extends('layouts.auth')
@section('title', 'Log In')
@section('content')
  <div class="login-container">
    <div class="card bg-dark-transparent border-0">
      <div class="card-body pb-3 pt-5">
        <form class="row gy-3" action="{{ route('login') }}" method="POST">
          @csrf
          {{-- <div class="col-12 text-center">
            <img src="{{ asset('images/dncc.png') }}" alt="" style="width: 140px">
          </div> --}}
          <div class="col-12 mt-2">
            <h1 class="fs-3 text-center">Central Light Management System</h1>
          </div>

          <div class="col-12 form-icon">
            <span class="icon"><i class="fas fa-user"></i></span>
            <input type="text" name="email" id="email" class="form-control" value="{{ old('email') }}"
              placeholder="Please enter your account email or username" required>
          </div>
          <div class="col-12 form-icon">
            <span class="icon"><i class="fas fa-lock"></i></span>
            <input type="password" name="password" id="password" class="form-control"
              placeholder="Please enter your password" required>
          </div>
          <div class="col-12">
            <select name="language" id="language" class="form-select">
              <option value="en">English</option>
              <option value="bn">Bangla</option>
            </select>
          </div>
          <div class="col-12">
            @error('email')
              <div class="text-danger mb-2">{{ $message }}</div>
            @enderror
            <button type="submit" class="btn btn-success w-100">Log In</button>
          </div>

          <div class="col-12 text-center mt-4">
            <span class="fs-5"><i>Powered By</i></span>
            <img src="{{ asset('images/energy-logo.png') }}" style="width: 120px" alt="">
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

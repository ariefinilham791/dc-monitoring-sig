@extends('layouts.base', ['title' => 'Login'])

@section('content')
    <div class="bg-gradient2 min-vh-100 d-flex align-items-center justify-content-center py-5 px-3">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10 col-lg-5">
                    <div class="card border-0 shadow">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <a href="{{ url('/') }}" class="d-inline-block">
                                    <img src="/images/logo.png" class="align-self-center" alt="Logo" height="32" onerror="this.style.display='none'"/>
                                </a>
                                <h4 class="mt-4 mb-1">Login</h4>
                                <p class="text-muted small">Masukkan email dan password untuk masuk.</p>
                            </div>

                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                @if ($errors->any())
                                    <div class="alert alert-danger py-2">
                                        @foreach ($errors->all() as $error)
                                            <small>{{ $error }}</small>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-lg" id="email" name="email"
                                           value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control form-control-lg" id="password"
                                           name="password" placeholder="Password" required>
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                        <label class="form-check-label text-muted small" for="remember">Ingat saya</label>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100">Masuk</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

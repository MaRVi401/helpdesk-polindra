@extends('layouts.blankLayout')

@section('content')
<div class="authentication-wrapper authentication-cover">
    <div class="authentication-inner row m-0">
        
        <div class="d-flex col-12 col-lg-5 col-xl-4 align-items-center authentication-bg p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
                <h4 class="mb-2">Atur Ulang Kata Sandi 👋</h4>
                
                <form id="formAuthentication" class="mb-3" action="{{ route('password.update') }}" method="POST">
                    @csrf
                    
                    {{-- Token reset dari URL --}}
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Institusi</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               placeholder="Email Institusi"
                               value="{{ old('email', request('email')) }}" required autofocus>

                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>

                    <div class="mb-3 form-password-toggle">
                        <label class="form-label" for="password">Kata Sandi Baru</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   name="password" required />

                            <span class="input-group-text cursor-pointer"><i class="ti ti-eye-off"></i></span>

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3 form-password-toggle">
                        <label class="form-label" for="password_confirmation">Konfirmasi Kata Sandi</label>
                        <div class="input-group input-group-merge">
                            <input type="password" id="password_confirmation" 
                                   class="form-control" name="password_confirmation" required />
                        </div>
                    </div>

                    <button class="btn btn-primary d-grid w-100 mb-3" type="submit">Atur Ulang Kata Sandi</button>

                </form>
            </div>
        </div>

    </div>
</div>
@endsection

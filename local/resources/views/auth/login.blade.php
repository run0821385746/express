@extends('layouts.app')

@section('content')
<div class="container" id="card_login">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info">{{ __('Login') }} </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}" id="login_form">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" value="" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        {{-- <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div> --}}

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="button" class="btn btn-primary" onclick="submit_login()">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}"><!--  -->
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var input = document.getElementById("password");
    input.addEventListener("keyup", function(event) {
        if (event.keyCode === 13) {
            event.preventDefault();
            submit_login();
        }
    });
    function submit_login(){
        email = $("#email").val();
        password = $("#password").val();
        $.ajax({
            method:"POST",
            url:"{{url('check_online')}}",
            dataType: 'json',
            data:{
                "email": email,
                "password": password
            },
            success:function(data){
                if(data.status == '1'){
                    $("form#login_form").submit();
                }else{
                    Swal.fire({
                        icon: 'info',
                        title: '<strong><u>บัญชีนี้</u>มีผู้ใช้งานอยู่</strong>',
                        text: 'หากคุณยืนยันที่จะเข้าสู่ระบบ ผู้ใช้อีกคนจะถูกบังคับออกจากระบบทันที',
                        showDenyButton: true,
                        showCancelButton: true,
                        confirmButtonText: `เข้าสู่ระบบ`,
                        denyButtonText: `ยกเลิก`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $("form#login_form").submit();
                        }
                    })
                }
            }
        });
        // $.post("{{url('check_online')}}",
        //     {
        //         email,
        //         password
        //     },
        //     function(data){
        //         // result = JSON.parse(data);
        //         // console.log(data);
        //         // $("#CLS").html(result.CLS);
        //         // $("#CONS").html(result.CONS);
        //         // $("#POD").html(result.POD);
        //         // $("#DLY").html(result.DLY);
        //         // $("#COD").html(result.COD);
        //         // $("#COD_ALL").html(result.COD_ALL);
        //         // $("#LH").html(result.LH);
        //         // $("#on_LH").html(result.on_LH);
        //         // $("#DVL").html(result.DVL);
        //         // $("#tranfer_bill").html(result.tranfer_bill);
        //     }
        // );
    }
    var body = document.body,
        html = document.documentElement;

    var height = Math.max( body.scrollHeight, body.offsetHeight, html.clientHeight, html.scrollHeight, html.offsetHeight );
    // window_height = window.screen.height;
    nav = document.getElementById("nav").offsetHeight;
    mainheight = height-nav-10;
    document.getElementById("mainlogin").style.height = mainheight+"px";

    card_login = document.getElementById("card_login").offsetHeight;
    // alert(card_login);
    // card_login_haff = card_login/2;
    margin_top = (mainheight/2)-card_login;
    document.getElementById("card_login").style.marginTop  = margin_top+"px";
    document.getElementById("mainlogin").style.backgroundImage = "url('{{url("")}}/local/public/uploadimg/banner.jpg')";
    document.getElementById("mainlogin").style.backgroundSize = "100% 100%";

</script>
@endsection

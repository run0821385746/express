@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Request Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @csrf

                    <div class="form-group row">
                        <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                        <div class="col-md-6">
                            <input id="email" type="email" class="form-control" name="username" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            {{-- <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="username" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror --}}
                        </div>
                    </div>

                    <div class="form-group row mb-0">
                        <div class="col-md-6 offset-md-4">
                            <button type="button" class="btn btn-primary pull-right" onclick="get_otp_for_reset('')">
                                Send Request
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script> --}}
<script>
    var usermail = 0;
    function get_otp_for_reset(){
        email = $("#email").val();
        // alert(email);
        $.ajax({
            method:"POST",
            url:"{{url('get_otp_for_reset')}}",
            dataType: 'json',
            data:{"email":email,"_token": "{{ csrf_token() }}"},
            success:function(data){
                // alert(data.status);
                // console.log(data);
                // result = data
                if(data.status == '1'){
                    usermail = data.msg;
                    // alert(usermail);
                    setTimeout(() => {
                        usermail = "";
                        Swal.fire({
                            position: 'top-end',
                            icon: 'error',
                            title: 'หมดเวลาการยืนยัน OTP',
                            showConfirmButton: false,
                            timer: 1500
                        })
                    }, 900000);
                    Swal.fire({
                        type: 'warning',
                        title: 'OTP Confirm',
                        icon: 'warning',
                        showCancelButton: false,
                        showConfirmButton: false,
                        reverseButtons: false,
                        html:   '<form action="/Employee_request_password" method="POST" id="confirm_otp_form">'+
                                    '<input type="hidden" name="_token" value="{{ csrf_token() }}">'+
                                        '<input type="hidden" name="email" value="'+email+'">'+
                                    '<div class="row"><br><br>'+
                                        '<div class="col-lg-12 col-md-12 text-left">'+
                                            '<div class="position-relative form-group">'+
                                                '<label for="province" class="">กรุณายืนยัน OTP ที่ได้รับจาก E-Mail ของคุณภายใน 15 นาที</label>'+
                                                '<input name="opt_confirm" id="opt_confirm" class="form-control">'+
                                                '<div class="row" id="request_currier_id" style="display:block;">'+
                                                    '<div class="col-lg-12 col-md-12">'+
                                                    '</div>'+
                                                '</div>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>'+
                                    '<div class="row">'+
                                        '<div class="col-lg-12 col-md-12">'+
                                            '<button type="button" class="mt-1 btn btn-danger" onclick="submitOTP()">Confirm</button>'+
                                        '</div>'+
                                    '</div>'+
                                '</form>'
                    });
                }else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.msg
                    })
                }
            }
        });
    }
    function submitOTP(){
        if(usermail !== ""){
            opt_confirm = $("#opt_confirm").val();
            $.ajax({
                method:"POST",
                url:"{{url('otp_submit_password')}}",
                dataType: 'json',
                data:{"otp":usermail,"otp_submit":opt_confirm,"_token": "{{ csrf_token() }}"},
                success:function(data){
                    if(data.status == '1'){
                        $("form#confirm_otp_form").submit();
                    }else{
                        alert(data.msg);
                    }
                }
            });
        }else{
            Swal.fire({
                position: 'top-end',
                icon: 'error',
                title: 'หมดเวลาการยืนยัน OTP',
                showConfirmButton: false,
                timer: 1500
            })
        }
    }
</script>
@endsection

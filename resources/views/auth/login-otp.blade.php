@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login-otp') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="mobile" class="col-md-4 col-form-label text-md-right">{{ __('Mobile Number') }}</label>

                            <div class="col-md-6">
                                <input id="mobile" type="text" class="form-control" name="mobile" value="{{ old('mobile') }}" required autofocus>

                                @if ($errors->has('mobile'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('mobile') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>


                        <div class="form-group row otp-generated" style="display: none;">
                            <label for="otp" class="col-md-4 col-form-label text-md-right">{{ __('OTP') }}</label>

                            <div class="col-md-6">
                                <input id="otp" type="text" class="form-control" name="otp">

                                @if ($errors->has('otp'))
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('otp') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0 otp-generated" style="display: none;">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>
                            </div>
                            <span id="otpContainer">
                                
                            </span>
                        </div>

                        <div class="form-group row mb-0" id="otp-generate">
                            <div class="col-md-8 offset-md-4">
                                <a href="javascript:void(0);" class="btn btn-primary" id="generateOTP">
                                    {{ __('Generate OTP') }}
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer-scripts')
<script type="text/javascript">
    jQuery(document).ready(function()
    {
        bindGenerateOTPEvent();
    })

    function bindGenerateOTPEvent()
    {
        document.getElementById('generateOTP').onclick = function(e)
        {
            sendOtp();
        }
    }

    function sendOtp()
    {
        var mobileNumber = document.getElementById('mobile');

        if(mobileNumber.value == '' || mobileNumber.value.length < 2)
        {
           // mobileNumber.focus();
            alert("Please Provide Mobile Number");
            return;
        }

        //generateOTP();

        jQuery.ajax({
            url: '{!! route('generate-otp') !!}',
            method: 'POST',
            dataType: 'JSON',
            data: {
                "_token": "{{ csrf_token() }}",
                mobileNumber: mobileNumber.value
            },
            success: function(data)
            {
                console.log(data);
                if(data.status == false)
                {
                    alert('No User Found with Given Contact Number !');
                }
                else
                {
                    document.getElementById("otpContainer").innerHTML = 'Temp OTP : ' +  data.otp;

                    document.getElementById("otp-generate").style.display = 'none';

                    var elements = document.querySelectorAll('.otp-generated');

                    for(var i = 0; i < elements.length;  i++)
                    {
                        elements[i].style.display = '';
                    }

                    document.getElementById('otp').setAttribute('required', 'required');
                }
            },
            error: function(data)
            {

            }
        });
    }
</script>
@endsection

<!doctype html>
<html lang="en">
  <head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="css/stylesheet.css">

    
    <title>ragistration form</title>
  </head>
  <body class="bg-light">
    <div class="container">
        <form action="{{ route('user-login') }}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row justify-content-md-center">
                <div class="col-md-6 col-sm-8 form-ragister">
                    <div class="bg-dark text-white rounded-3">
                        <h1 class="text-center">Ragistration Here!</h1>
                        <div class="row m-0">
                            <div class="col-sm-12 mb-3">
                                <label>Email:</label>
                                <input type="text" name="email" class="form-control {{($errors->first('email') ? " form-error" : "")}}">
                                @error('email') <span class="error">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-sm-12 mb-3">
                                <label>Password:</label>
                                <input type="password" name="password" id="password" class="form-control {{($errors->first('password') ? " form-error" : "")}}">
                                @error('password') <span class="error">{{ $message }}</span> @enderror
                            </div>
                            <div class="mb-3 d-inline">
                                <a href="{{ url('signup') }}"><button class="btn-lg btn btn-light submit-form" type="button">Sign up</button></a>
                                <a href="{{ route('user-dashboard') }}"><button class="btn-lg btn btn-light submit-form float-end" type="submit">log in</button></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.0/jquery.validate.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.1/parsley.min.js"></script>
    <script src="js/ragistration.js"></script>
  </body>
</html>
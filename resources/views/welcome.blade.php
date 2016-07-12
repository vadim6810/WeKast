<!DOCTYPE html>
<html>
<head>
    <title>Test API</title>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
          integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <style>
        html, body {
            height: 100%;
        }

        body {
            margin: 0;
            padding: 0;
            width: 100%;
            display: table;
            font-weight: 400;
            font-size: 14pt;
            font-family: sans-serif;
        }

        .container {
            text-align: center;
            display: table-cell;
            vertical-align: top;
        }

        .content {
            text-align: center;
            display: inline-block;
        }

        .title {
            font-size: 30pt;
        }

        .form-group {
            background-color: lightgray;
            padding: 20px;
            display: inline-block;
        }

        #output {
            width: 500px;
            height: 400px;
            overflow: scroll;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="content">
        <div class="title">API tester</div>
        <form class="form-group">
            <h3>Register Tester</h3>
            <label for="login">Login:</label>
            <input id="login" type="text" class="form-control">
            <label for="email">Email:</label>
            <input id="email" type="text" class="form-control">
            <button id="register" class="btn btn-default">Register</button>
        </form>
        <form id="upload" class="form-group" method="post" enctype="multipart/form-data">
            <h3>Upload Tester</h3>
            <label for="phone">Login:</label>
            <input name="login" id="phone" type="text" class="form-control">
            <label for="password">Password:</label>
            <input name="pass" id="password" type="text" class="form-control">
            <label for="file">Password:</label>
            <input name="file" id="file" type="file" class="form-control">
            <button id="auth" class="btn btn-default">Auth</button>
        </form>
        <div id="output">Output...</div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-2.2.4.min.js"
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
<!-- Latest compiled and minified JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"
        integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS"
        crossorigin="anonymous"></script>
<script>
    $("#register").click(function (e) {
        e.preventDefault();
        var output = $("#output");
        var d = {
            login: $("#login").val(),
            email: $("#email").val()
        };

        $.post("register", d, function (data) {
            output.text(data);
        }, "text");
    });
    $("#auth").click(function (e) {
        e.preventDefault();
        var output = $("#output");
        var d = new FormData($('#upload')[0]);

        $.ajax({
            url: 'upload',  //Server script to process data
            type: 'POST',
            xhr: function() {  // Custom XMLHttpRequest
                var myXhr = $.ajaxSettings.xhr();
                if(myXhr.upload){ // Check if upload property exists
                    myXhr.upload.addEventListener('progress',function (e) {
                        console.log(e);
                    }, false); // For handling the progress of the upload
                }
                return myXhr;
            },
            //Ajax events
            success: function (data) {
                output.text(data);
            },
            error: function (data) {
                output.text(data);
            },
            // Form data
            data: d,
            //Options to tell jQuery not to process data or worry about content-type.
            cache: false,
            contentType: false,
            processData: false,
            dataType: text
        });
    });
</script>
</body>
</html>

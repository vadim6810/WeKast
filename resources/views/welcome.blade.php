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
            <label for="upload-login">Login:</label>
            <input name="login" id="upload-login" type="text" class="form-control">
            <label for="upload-password">Password:</label>
            <input name="password" id="upload-password" type="text" class="form-control">
            <label for="file">File:</label>
            <input name="file" id="file" type="file" class="form-control">
            <button id="auth" class="btn btn-default">Auth</button>
        </form>
        <form class="form-group">
            <h3>List Tester</h3>
            <label for="list-login">Login:</label>
            <input id="list-login" type="text" class="form-control">
            <label for="list-password">Password:</label>
            <input id="list-password" type="text" class="form-control">
            <button id="list" class="btn btn-default">Get list</button>
        </form>
        <form class="form-group">
            <h3>Download Tester</h3>
            <label for="download-login">Login:</label>
            <input id="download-login" type="text" class="form-control">
            <label for="download-password">Password:</label>
            <input id="download-password" type="text" class="form-control">
            <label for="download-id">File ID:</label>
            <input id="download-id" type="text" class="form-control">
            <button id="download" class="btn btn-default">download</button>
        </form>
        <form class="form-group">
            <h3>Delete Tester</h3>
            <label for="delete-login">Login:</label>
            <input id="delete-login" type="text" class="form-control">
            <label for="delete-password">Password:</label>
            <input id="delete-password" type="text" class="form-control">
            <label for="delete-id">File ID:</label>
            <input id="delete-id" type="text" class="form-control">
            <button id="delete" class="btn btn-default">delete</button>
        </form>
        <form class="form-group">
            <h3>Password reminder</h3>
            <label for="remind-email">email:</label>
            <input id="remind-email" type="text" class="form-control">
            <button id="remind" class="btn btn-default">Remind</button>
        </form>
        <form class="form-group">
            <h3>Phone confirm</h3>
            <label for="phone">phone:</label>
            <input id="phone" type="text" class="form-control">
            <label for="code">code:</label>
            <input id="code" type="text" class="form-control">
            <button id="confirm" class="btn btn-default">Confirm</button>
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

    $(function () {
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

        $("#list").click(function (e) {
            e.preventDefault();
            var output = $("#output");
            var d = {
                login: $("#list-login").val(),
                password: $("#list-password").val()
            };

            $.post("list", d, function (data) {
                output.text(data);
            }, "text");
        });

        $("#download").click(function (e) {
            e.preventDefault();
            var output = $("#output");
            var d = {
                login: $("#download-login").val(),
                password: $("#download-password").val()
            };
            var id = $("#download-id").val();
            $.post("download/" + id, d, function (data) {
                output.text(data);
            }, "text");
        });

        $("#delete").click(function (e) {
            e.preventDefault();
            var output = $("#output");
            var d = {
                login: $("#delete-login").val(),
                password: $("#delete-password").val()
            };
            var id = $("#delete-id").val();
            $.post("delete/" + id, d, function (data) {
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
                dataType: "text"
            });

        });

        $("#remind").click(function (e) {
            e.preventDefault();
            var output = $("#output");
            var d = {
                email: $("#remind-email").val()
            };

            console.log(d);

            $.post("reset", d, function (data) {
                output.text(data);
            }, "text");
        });

        $("#confirm").click(function (e) {
            e.preventDefault();
            var output = $("#output");
            var d = {
                login: $("#phone").val(),
                code: $("#code").val()
            };

            console.log(d);

            $.post("code", d, function (data) {
                output.text(data);
            }, "text");
        });

    });

</script>
</body>
</html>

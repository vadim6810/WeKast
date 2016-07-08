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
        <form  class="form-group">
            <h3>Auth Tester</h3>
            <label for="phone">Login:</label>
            <input id="phone" type="text" class="form-control">
            <label for="password">Password:</label>
            <input id="password" type="text" class="form-control">
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
        console.log(d);
        $.post("register", d, function (data) {
            output.text(data);
        }, "text");
    });
    $("#auth").click(function (e) {
        e.preventDefault();
        var output = $("#output");
        var d = {
            login: $("#phone").val(),
            password: $("#password").val()
        };
        console.log(d);
        $.post("auth", d, function (data) {
            output.text(data);
        }, "text");
    });
</script>
</body>
</html>

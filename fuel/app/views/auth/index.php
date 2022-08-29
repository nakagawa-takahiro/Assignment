<div data-bind="text: msg"></div>

<html>
    <body>
        
        <h1>ログイン画面</h1>
        <form method="POST" action="/auth/login">
    
            username:<input type="text" name='username'><br>
            password:<input type="text" name='password'><br>
            <input type="submit" value="送信">

        </form>
        <a href="./signup">新規登録はこちら</a>

    </body>

</html>

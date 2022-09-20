<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <?php echo Asset::css('style.css'); ?>
</head>

<body>
    <main class="flex flex__a-center" style="flex-direction: column; align-items: center">
        <div class="container pane__left">
            <div class="container center">
                <div class="container message">
                </div>
            <div class="container main">
                <div class="flex flex__wrap flex__j-center">
                    <div class="container top flex flex__j-center">
                        <!-- <img class="logo" src="/assets/img/header/logo_kanrikun+.png" alt="採用一括かんりくん"> -->
                    </div>
                    <div class="container bottom flex flex__wrap flex__j-center p__relative">
                        <h1>サインアップ画面</h1>
                        <form action="/auth/index" method="post" class="flex flex__wrap flex__j-center w__100">
                            <fieldset>
                                <div class="container flex flex__a-center">
                                    <legend>アカウントID</legend>
                                    <div class="container input__field">
                                        <input type="text" name="username" value="" placeholder="username" required>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="container flex flex__a-center">
                                    <legend>パスワード</legend>
                                    <div class="container input__field">
                                        <input type="password" name="password" value="" placeholder="************" required>
                                    </div>
                                </div>
                            </fieldset>
                            <fieldset>
                                <div class="container flex flex__a-center">
                                    <legend>Email</legend>
                                    <div class="container input__field">
                                        <input type="text" name="mail" value="" placeholder="abc@example.com" required>
                                    </div>
                                </div>
                            </fieldset>
                            <button class="btn outline submit w__100" type="submit">送信</button>
                            <div class="container flex flex__wrap flex__j-center">
                                <div class="w__100 align__center mb__20">
                                    <a href="./index">ログイン画面にもどる</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>
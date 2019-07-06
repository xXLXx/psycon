
<div class='content_area'>

    <form method="post" action="/popup/login_submit">

        <div align='center'>

            <h1>Sign In To Your Account</h1>
            <?

                if(!empty($this->error))
                {

                    echo "<p style='color:red;'>{$this->error}</p>";

                }
                else
                {

                    echo "<p>Use the form below to sign into your Psychic-Contact account. After you login, you will be redirected back.</p>";

                }

            ?>

            <hr />

            <table cellPadding='10'>

                <tr>
                    <td>Username:</td>
                    <td><input type="text" name="username" value='<?=set_value('username')?>'></td>
                </tr>

                <tr>
                    <td>Password:</td>
                    <td><input type="password" name="password" value='<?=set_value('password')?>'></td>
                </tr>

                <tr>
                    <td>&nbsp;</td>
                    <td><input type="submit" name="submit" value='Login' class='btn btn-primary'></td>
                </tr>

            </table>

        </div>

    </form>

</div>
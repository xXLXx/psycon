
    <div class='content_area'>

        <h1>Forgot Password?</h1>

        <form action='/register/forgot_password_submit' method='POST'>

            <div class="form-group">
                <label for="emailAddressInput">Enter your email address below and we will email you a link to reset your password:</label>
                <input name='email_address' type="email" class="form-control" id="emailAddressInput" placeholder="Your email address..." />
            </div>

            <div class="form-group">
                <input name='submit' type="submit" class="btn btn-default btn-success" value='Reset My Password'>
            </div>

        </form>

    </div>
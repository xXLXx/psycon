
    <div class='content_area'>

        <h1>Reset Your Password</h1>

        <form action='/register/reset_password_submit/<?=$id?>' method='POST'>

            <div class="form-group">
                <label>Choose a new password:</label>
                <input name='password1' type="password" class="form-control" value='<?=set_value('first_name')?>'>
            </div>

            <div class="form-group">
                <label>Re-type new password:</label>
                <input name='password2' type="password" class="form-control" value='<?=set_value('last_name')?>'>
            </div>

            <div class="form-group">
                <input name='submit' type="submit" class="btn btn-default btn-success" value='Reset My Password'>
            </div>

        </form>

    </div>
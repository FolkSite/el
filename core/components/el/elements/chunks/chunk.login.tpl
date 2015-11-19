<div class="el-view">
    <div data-type="user" data-id="0">
        <h4>[[%el_login_header]]</h4>

        <form class="el-login form-inline" method="post" action="">
            <input type="hidden" name="propkey" value="[[+propkey]]"/>

            <div class="form-group">
                <input type="text" name="email" placeholder="[[%el_login_email]]" class="form-control"/>
                <input type="button" class="btn btn-primary" name="login/login" data-confirm="false" data-type="error"
                       data-message="" value="[[%el_login]]"
                       title=""/>
            </div>

            [[+send:gt=`0`:then=`
            <p class="help-block">
                <small>[[%el_login_link_send]]</small>
            </p>
            `:else=`
            <p class="help-block">
                <small>[[%el_login_footer]]</small>
            </p>
            `]]

        </form>
    </div>
</div>

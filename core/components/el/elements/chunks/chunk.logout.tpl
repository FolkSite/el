<div class="el-view">
    <div data-type="user" data-id="[[+id]]">
        <h4>[[%el_logout_header]]</h4>

        <form class="el-logout form-inline" method="post" action="">
            <input type="hidden" name="propkey" value="[[+propkey]]"/>

            <div class="form-group">
                <input type="text" name="name" placeholder="[[+username]]" class="form-control" disabled/>
                <input type="button" class="btn btn-danger" name="login/logout" data-confirm="true" data-type="error"
                       data-message="[[%el_logout_action]]" value="[[%el_logout]]"
                       title=""/>
            </div>
            <p class="help-block">
                <small>[[%el_logout_footer]]</small>
            </p>
        </form>
    </div>
</div>

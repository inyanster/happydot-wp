<style>
.hd-without-login {
    display: flex;
    flex-wrap: nowrap;
    align-items: stretch;
    gap: 10px;
}
.hd-without-login .hd-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    line-height: 1.3;
    white-space: normal;
}
</style>

<div class="hd-without-login">
    <a class="hd-btn" href="<?php echo esc_url(get_permalink(get_option('flexcore_login_page'))); ?>" class="hd-btn">Login</a>
    <a class="hd-btn" href="<?php echo esc_url(get_permalink(get_option('flexcore_register_page'))); ?>" class="hd-btn">SIGNUP<br>via Singpass</a>
</div>

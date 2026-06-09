<!-- <style>
.hd-without-login {
    display: flex;
    gap: 10px;
}
.hd-btn {
    background-color: #d72027;
    color: #fff;
    text-transform: uppercase;
    font-weight: bold;
    border: none;
    border-radius: 25px;
    padding: 10px 20px;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    transition: background-color 0.3s ease;
}
.hd-btn:hover {
    background-color: #b51c22;
} -->
</style>

<div class="hd-without-login">
    <a class="hd-btn" href="<?php echo esc_url(get_permalink(get_option('flexcore_login_page'))); ?>" class="hd-btn">Login</a>
    <a class="hd-btn" href="<?php echo esc_url(get_permalink(get_option('flexcore_register_page'))); ?>" class="hd-btn">Signup</a>
</div>

<div class="login-13">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-6 col-md-8 bg-img">
                <div class="bg-img-inner">
                    <div class="info">
                        <div class="center">
                            <h1>Welcome To BIZADMIN</h1>
                            <p class="tagline"><b>BIZADMIN</b> is a one stop management tool to simplify your business processes.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-12 form-info">
                <div class="form-section">
                    <div class="form-section-innner">
                        <div class="logo clearfix">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <a href="#" class="btn_login_pin btn btn-outline-success w-100" data-id="2">Login using Pin</a>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-12">
                                    <a class="btn_login_password btn btn-outline-success w-100 active" data-id="1" href="#">Login using Username</a>
                                </div>
                            </div>
                        </div>
                        <h3>Sign Into Your Account</h3>
                        <?php if (isset($message) && $message != ''): ?>
                            <div id="infoMessage" class="alert alert-danger"><?php echo $message; ?></div>
                        <?php endif; ?>
                        <?php if ($this->session->flashdata('error')): ?>
                            <div class="alert alert-danger">
                                <?php echo $this->session->flashdata('error'); ?>
                            </div>
                        <?php endif; ?>
                        <div class="login-inner-form">
                            <form action="<?php echo base_url(); ?>index.php/auth/login" method="POST" id="login_form">
                                <div id="email_password_form" style="display: none;">
                                    <div class="form-group form-box clearfix">
                                        <input type="email" name="identity" id="identity" class="form-control" placeholder="Email Address" aria-label="Email Address">
                                        <i class="flaticon-mail-2"></i>
                                    </div>
                                    <div class="form-group form-box clearfix">
                                        <input name="password" id="password" type="password" class="form-control" autocomplete="off" placeholder="Password" aria-label="Password">
                                        <i class="flaticon-password"></i>
                                    </div>
                                    <div class="checkbox form-group clearfix">
                                        <a href="/auth/forgot_password" class="link-light float-end forgot-password">Forgot password?</a>
                                    </div>
                                </div>
                                
                                
                                <div id="pin_form">
                                    <input type="hidden" name="login_pin" id="login_pin">
                                    <!-- Added 4 input boxes to display PIN -->
                                    <div class="pin-inputs mb-3 d-flex justify-content-center">
                                        <input type="text" readonly class="pinlogin-field form-control mx-1 text-center" maxlength="1" >
                                        <input type="text" readonly class="pinlogin-field form-control mx-1 text-center" maxlength="1" >
                                        <input type="text" readonly class="pinlogin-field form-control mx-1 text-center" maxlength="1" >
                                        <input type="text" readonly class="pinlogin-field form-control mx-1 text-center" maxlength="1" >
                                    </div>
                                <div id="pinpad" class="pinpad mt-4">
                                    <div class="row">
                                        <div class="col-4"><button type="button" class="pin-button" data-value="1">1</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value="2">2</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value="3">3</button></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"><button type="button" class="pin-button" data-value="4">4</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value="5">5</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value="6">6</button></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"><button type="button" class="pin-button" data-value="7">7</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value="8">8</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value="9">9</button></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"><button type="button" class="pin-button" data-value="clear">C</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value="0">0</button></div>
                                        <div class="col-4"><button type="button" class="pin-button" data-value=""></button></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="submit_button">
                                <button type="submit" class="btn btn-primary btn-lg btn-theme submit_login">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pinpad {
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .pin-button {
        width: 50px;
        height: 50px;
        font-size: 24px;
        margin: 5px;
        cursor: pointer;
    }
    .btn-outline-success.active {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }
    .pin-inputs input {
        border: 1px solid #ccc;
        border-radius: 5px;
        font-size: 20px;
    }
    .login-13 .login-inner-form .pin-inputs .form-control{
          padding: 0px !important;
           width: 54px !important;
    }
</style>

<script>
$(document).ready(function() {
    // Show PIN form by default and hide submit button
    $('#pin_form').hide();
    $('#email_password_form').show();
    $('#submit_button').show();
    $('.btn_login_pin').removeClass('active');
    $('.btn_login_password').addClass('active');

    // Toggle forms on button click
    $('.btn_login_pin').on('click', function(e) {
        e.preventDefault();
        $('#pin_form').show();
        $('#email_password_form').hide();
        $('#submit_button').hide();
        $('.btn_login_pin').addClass('active');
        $('.btn_login_password').removeClass('active');
        // Clear email/password fields
        $('#identity').val('');
        $('#password').val('');
    });

    $('.btn_login_password').on('click', function(e) {
        e.preventDefault();
        $('#pin_form').hide();
        $('#email_password_form').show();
        $('#submit_button').show();
        $('.btn_login_password').addClass('active');
        $('.btn_login_pin').removeClass('active');
        // Clear PIN fields
        $('#login_pin').val('');
        $('.pinlogin-field').val('');
    });

    // PIN pad logic
    const pinFields = $('.pinlogin-field');
    let pinIndex = 0;

    $('.pin-button').on('click', function() {
        const value = $(this).data('value');

        if (value === 'clear') {
            pinFields.each(function() {
                $(this).val('');
            });
            pinIndex = 0;
            $('#login_pin').val('');
            return;
        }

        if (value !== '' && pinIndex < 4) {
            pinFields.eq(pinIndex).val(value);
            pinIndex++;
        }

        if (pinIndex === 4) {
            // Collect PIN values and set the hidden input
            const pin = pinFields.map(function() { return $(this).val(); }).get().join('');
            $('#login_pin').val(pin);
            // Auto-submit the form
            $('#login_form').submit();
        }
    });
});
</script>
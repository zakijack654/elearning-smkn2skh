<div class="page-wrapper">
    <div class="page-content--bge5">
        <div class="container">
            <div class="login-wrap">                
                <div class="login-content">
                    <img src="<?= base_url('assets/images/icon/logo.png') ?>" alt="SMK Negeri 2 Sukoharjo">

                    <div class="login-logo">
                        <p>MASUK<br>
                            E-Learning SMK Negeri 2 Sukoharjo</p>
                    </div>
                    <div class="login-form">
                        <form action="<?= base_url('user/prosesLogin') ?>" method="post">
                            <div class="form-group">
                                <label>Email </label>
                                <input class="au-input au-input--full" type="email" name="email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input class="au-input au-input--full" type="password" name="password" placeholder="Password">
                            </div>
                            <div class="login-checkbox">
                                <label>
                                    <input type="checkbox" name="remember">Ingat saya
                                </label>
                            </div>
                            <button class="au-btn au-btn--block au-btn--green m-b-20" type="submit">MASUK</button>

                        </form>
                        <div class="register-link">
                            <p>
                                Belum punya akun?
                                <a href="<?= base_url('user/registerSiswa') ?>">Daftar disini.</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
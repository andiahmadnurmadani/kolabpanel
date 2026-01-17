<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--============ FLATICON ==============-->
    <link rel='stylesheet'
        href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <!--============ SWIPER CSS ==============-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <!--============ CSS ==============-->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Nunito&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Forget Password | Baby World</title>
</head>

<body>
    <!--============ HEADER ==============-->
    <header class="header">
        <div class="header__top">
            <div class="header__container container">
                <div class="header__contact">
                    <span>0865-9712-0151</span>
                    <span><a href="location.php">Our Location</a></span>
                </div>

                <p class="header__alert-news">
                    <a href="voucher.php">Super Value Deals - Save more with coupons</a>
                </p>

                <div class="header__contact">
                    <span><a href="helpcenter.php"> Help Center</a></span>
                    <span><a href="login-register.php"> Log In / Sign Up</a></span>
                </div>
            </div>
        </div>

        <nav class="nav container">
            <a href="index.php" class="nav__logo">
                <img src="{{ asset('assets/img/feelin.png') }}" alt="Baby World Logo" class="nav__logo-img">
            </a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">
                    <li class="nav__item">
                        <a href="index.php" class="nav__link active">Home</a>
                    </li>

                    <li class="nav__item">
                        <a href="shop.php" class="nav__link">Shop</a>
                    </li>

                    <li class="nav__item">
                        <a href="accounts.php" class="nav__link">My Account</a>
                    </li>

                    <li class="nav__item">
                        <a href="compare.php" class="nav__link">Compare</a>
                    </li>

                    <li class="nav__item">
                        <a href="contact.php" class="nav__link">Contact</a>
                    </li>

                    <li class="nav__item">
                        <a href="login-register.php" class="nav__link">Login</a>
                    </li>
                </ul>

                <div class="header__search">
                    <input type="text" placeholder="Search for items..." class="form__input">

                    <button class="search__btn">
                        <i class='bx bx-search-alt'></i>
                    </button>
                </div>
            </div>

            <div class="header__user-actions">
                <a href="wishlist.php" class="header__action-btn">
                    <i class='bx bxs-heart'></i>
                    <span class="count">8</span>
                </a>

                <a href="cart.php" class="header__action-btn">
                    <i class='bx bxs-cart-alt'></i>
                    <span class="count">4</span>
                </a>

                <a href="chat.php" class="header__action-btn">
                    <i class='bx bxs-envelope'></i>
                </a>
            </div>
        </nav>
    </header>


    <!--============ BREADCRUMP ==============-->
    <section class="breadcrumb">
        <ul class="breadcrumb__list flex container">
            <li><a href="{{ route('login-register.page') }}" class="breadcrumb__link">Login & Register</a></li>
            <li><span class="breadcrumb__link">></span></li>
            <li><span class="breadcrumb__link">Forgot Password</span></li>
        </ul>
    </section>

    @if(session('error'))
    <div style="color: red; text-align:center; padding-top:50px;">
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div style="color: green; text-align:center; padding-top:50px;">
        {{ session('success') }}
    </div>
    @endif


    <!--============ FORM FORGET PASSWORDS  ==============-->
    <section class="accounts section--lg">
        <div class="accounts__container container" style="display: flex; justify-content: center;">

            <div class="tabs__content" style="max-width: 500px; width: 100%;">
                <div class="tab__content active-tab">
                    <h3 class="tab__header">Reset Password</h3>

                    <div class="tab__body">
                        <form method="POST" action="{{ route('reset.password') }}" class="form grid" style="font-family: 'Nunito', sans-serif;">
                            @csrf
                            <input type="text" name="name" placeholder="Nama Lengkap" class="form__input" required>
                            <input type="email" name="email" placeholder="Email Terdaftar" class="form__input" required>

                            <label for="security_question" style="font-size: 14px; margin-top: 10px;">Pertanyaan Keamanan</label>
                            <select name="security_question" id="security_question" class="form__input" style="font-family: 'Nunito', sans-serif;" required>
                                <option value="">Pilih Pertanyaan Keamanan</option>
                                <option value="Siapa nama ibu kandungmu?">Siapa nama ibu kandungmu?</option>
                                <option value="Apa nama hewan peliharaan pertamamu?">Apa nama hewan peliharaan pertamamu?</option>
                                <option value="Apa makanan favoritmu?">Apa makanan favoritmu?</option>
                            </select>

                            <input type="text" name="security_answer" placeholder="Jawaban Anda" class="form__input" required autocomplete="off">

                            <input type="password" name="new_password" placeholder="Password Baru" class="form__input" required>

                            <div class="form__btn">
                                <button type="submit" name="reset">Reset Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </section>


    <!--============ NEWSLETTER  ==============-->
    <section class="newsletter section home__newsletter">
        <div class="newsletter__container container grid">
            <h3 class="newsletter__title flex">
                <img src="{{ asset('assets/svg/mailbox-flag-up.svg') }}" alt="Newsletter Icon" class="newsletter__icon">
                Sign Up to Newsletter
            </h3>

            <p class="newsletter__description">
                ...and receive Rp100.000 coupon for first shopping.
            </p>

            <form action="" class="newsletter__form">
                <input type="text" placeholder="Enter your Email" class="newsletter__input">
                <button type="submit" class="newsletter__btn">Subscribe</button>
            </form>
        </div>
    </section>

    <script src="assets/js/accounts.js"></script>
</body>

<!--============ FOOTER ==============-->
<footer class="footer__container">
    <div class="footer__container grid">
        <div class="footer__content">
            <a href="index.html" class="footer__logo">
                <img src="{{ asset('assets/img/feelin.png') }}" alt="Baby World Logo" class="footer__logo-img">
            </a>

            <h4 class="footer__subtitle">Contact</h4>

            <p class="footer__description">
                <span>Address:</span> Jl. Telekomunikasi, No 73, Bandung, Jawa Barat.
            </p>

            <p class="footer__description">
                <span>Phone:</span> +62 856 9712 0151 / (+62) 897 4395 313
            </p>

            <p class="footer__description">
                <span>Hours:</span> 10:00 - 18:00, Mon - Sat
            </p>

            <div class="footer__social">
                <h4 class="footer__subtitle">Follow Us</h4>

                <div class="footer__social-links">
                    <a href="#">
                        <img src="{{ asset('assets/svg/facebook.svg') }}" alt="Facebook" class="footer__social-icon">
                    </a>
                    <a href="#">
                        <img src="{{ asset('assets/svg/instagram.svg') }}" alt="Instagram" class="footer__social-icon">
                    </a>
                    <a href="#">
                        <img src="{{ asset('assets/svg/pinterest.svg') }}" alt="Pinterest" class="footer__social-icon">
                    </a>
                    <a href="#">
                        <img src="{{ asset('assets/svg/youtube.svg') }}" alt="YouTube" class="footer__social-icon">
                    </a>
                </div>
            </div>
        </div>

        <div class="footer__content">
            <h3 class="footer__title">Address</h3>

            <ul class="footer__links">
                <li><a href="aboutus.html" class="footer__link">About Us</a></li>
                <li><a href="delivery.html" class="footer__link">Delivery Information</a></li>
                <li><a href="" class="footer__link">Privacy Policy</a></li>
                <li><a href="" class="footer__link">Terms & Conditions</a></li>
                <li><a href="contact.html" class="footer__link">Contact Us</a></li>
                <li><a href="review.html" class="footer__link">Customer Review</a></li>
            </ul>
        </div>

        <div class="footer__content">
            <h3 class="footer__title">My Account</h3>

            <ul class="footer__links">
                <li><a href="" class="footer__link">Sign In</a></li>
                <li><a href="" class="footer__link">View Cart</a></li>
                <li><a href="" class="footer__link">My Wishlist</a></li>
                <li><a href="" class="footer__link">Track My Order</a></li>
                <li><a href="" class="footer__link">Help</a></li>
                <li><a href="" class="footer__link">Order</a></li>
            </ul>
        </div>

        <div class="footer__content">
            <h3 class="footer__title">Secured Payment Gateways</h3>
            <img src="{{ asset('assets/img/card.png') }}" alt="" class="payment__img">
            <img src="{{ asset('assets/img/visa.png') }}" alt="" class="payment__img">
            <img src="{{ asset('assets/img/paypal.png') }}" alt="" class="payment__img">
            <img src="{{ asset('assets/img/jcb.png') }}" alt="" class="payment__img">
        </div>
    </div>

    <div class="footer__bottom">
        <p class="copyright">&copy; 2024 BabyWorld. All rights reserved </p>
        <span class="designer">Designed by DianNadineAkhtar</span>
    </div>
</footer>


</html>
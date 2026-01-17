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
    <title>Checkout | Baby World</title>
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
                    @auth
                    <a href="{{ route('logout') }}" class="nav__link">Logout</a>
                    @endauth

                    @guest
                    <a href="{{ route('login-register.page') }}" class="nav__link">Log In / Sign Up</a>
                    @endguest

                </div>
            </div>
        </div>

        <nav class="nav container">
            <a href="index.php" class="nav__logo">
                <img src="{{ asset('assets/img/feelin.png') }}" alt="Baby World Logo" class="footer__logo-img">
            </a>

            <div class="nav__menu" id="nav-menu">
                <ul class="nav__list">

                    <li class="nav__item">
                        <a href="{{ route('home') }}" class="nav__link">Home</a>
                    </li>

                    <li class="nav__item">
                        <a href="{{ route('shop') }}" class="nav__link">Shop</a>
                    </li>

                    @auth
                    <li class="nav__item">
                        <a href="{{ route('accounts') }}" class="nav__link">My Account</a>
                    </li>
                    @endauth

                    <li class="nav__item">
                        <a href="{{ route('compare') }}" class="nav__link">Compare</a>
                    </li>

                    <li class="nav__item">
                        <a href="{{ route('contact') }}" class="nav__link">Contact</a>
                    </li>

                    @auth
                    <li class="nav__item">
                        <a href="{{ route('logout') }}" class="nav__link">Logout</a>
                    </li>
                    @endauth

                    @guest
                    <li class="nav__item">
                        <a href="{{ route('login-register.page') }}" class="nav__link">Login</a>
                    </li>
                    @endguest

                </ul>

                <form action="shop.php" method="GET" class="header__search">
                    <input type="text" name="search" placeholder="Search for items..." class="form__input">
                    <button type="submit" class="search__btn">
                        <i class='bx bx-search-alt'></i>
                    </button>
                </form>

            </div>

            <div class="header__user-actions">
                <a href="{{ route('wishlist') }}" class="header__action-btn">
                    <i class='bx bxs-heart'></i>
                    <span class="count">8</span>
                </a>

                <a href="{{ route('cart.index') }}" class="header__action-btn">
                    <i class='bx bxs-cart-alt'></i>
                    <span class="count">{{ count(session('cart', [])) }}</span>
                </a>

                <a href="{{ route('chat') }}" class="header__action-btn">
                    <i class='bx bxs-envelope'></i>
                </a>
            </div>

        </nav>
    </header>

    <!--============ BREADCRUMP ==============-->
    <section class="breadcrumb">
        <ul class="breadcrumb__list flex container">
            <li><a href="index.php" class="breadcrumb__link">Home</a></li>
            <li><span class="breadcrumb__link">></span></li>
            <li><a href="shop.php" class="breadcrumb__link">Checkout</a></li>
        </ul>
    </section>>

    <!--============ CHECKOUT CONTENT ==============-->
    <section class="checkout section--lg">
        <div class="checkout__container container grid">

            <!-- LEFT SIDE -->
            <div class="checkout__group">
                <h3 class="section__title">Billing Details</h3>

                <form action="{{ route('checkout.place') }}" method="POST" class="form grid">
                    @csrf
                    <input type="text" name="name" placeholder="Name" class="form__input" required>
                    <input type="text" name="address" placeholder="Address" class="form__input" required>
                    <input type="text" name="city" placeholder="City" class="form__input" required>
                    <input type="text" name="country" placeholder="Country" class="form__input" required>
                    <input type="number" name="post_code" placeholder="Post Code" class="form__input" required>
                    <input type="text" name="phone" placeholder="Phone" class="form__input" required>
                    <input type="email" name="email" placeholder="Email" class="form__input" required>

                    <h3 class="checkout__title">Additional Information</h3>
                    <textarea name="order_note" placeholder="Order note" class="form__input textarea"></textarea>
            </div>

            <!-- RIGHT SIDE -->
            <div class="checkout__group">
                <h3 class="section__title">Cart Totals</h3>

                <table class="order__table">
                    <tr>
                        <th colspan="2">Products</th>
                        <th>Price</th>
                    </tr>

                    @foreach ($items as $item)
                    <tr>
                        <td><img src="{{ asset($item['foto']) }}" class="order__img"></td>
                        <td>
                            <h3 class="table__title">{{ $item['nama_produk'] }}</h3>
                            <p class="table__quantity">x {{ $item['qty'] }}</p>
                        </td>
                        <td>Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </table>

                <table class="order__table">
                    <tr>
                        <td>SubTotal</td>
                        <td colspan="2">Rp{{ number_format($subtotal, 0, ',', '.') }}</td>
                    </tr>

                    @if($discount > 0)
                    <tr>
                        <td><span class="order__subtitle">Discount</span></td>
                        <td colspan="2">
                            <span class="table__price" style="color: green">
                                -{{ $discount }}% ({{ $voucher_code }}) <br>
                                -Rp{{ number_format($discount_value, 0, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                    @endif

                    <tr>
                        <td>Shipping</td>
                        <td colspan="2">Free Shipping</td>
                    </tr>

                    <tr>
                        <td><strong>Total</strong></td>
                        <td colspan="2">
                            <strong class="order__grand-total" style="color:#ff2c6b;">
                                Rp{{ number_format($total, 0, ',', '.') }}
                            </strong>
                        </td>
                    </tr>
                </table>

                <!-- PAYMENT -->
                <div class="payment__methods">
                    <h3 class="checkout__title payment__title">Payment</h3>

                    <div class="payment__option flex">
                        <input type="radio" name="payment_method" value="bank" checked>
                        <label>Direct Bank Transfer</label>
                    </div>

                    <div class="payment__option flex">
                        <input type="radio" name="payment_method" value="check">
                        <label>Check Payment</label>
                    </div>

                    <div class="payment__option flex">
                        <input type="radio" name="payment_method" value="paypal">
                        <label>Paypal</label>
                    </div>
                </div>

                <div class="checkout__btn">
                    <button type="submit">Place Order</button>
                </div>

                </form>

                @if (session('success'))
                <script>
                    alert("{{ session('success') }}");
                </script>
                @endif

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

    <script src="{{ asset('assets/js/accounts.js') }}"></script>

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
</body>

</html>
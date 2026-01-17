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
    <title>Shop | Baby World</title>
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

            @if(session('success'))
                <div style="position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#d4edda;color:#155724;padding:15px 30px;margin:0;border-radius:8px;border:1px solid #c3e6cb;z-index:9999;text-align:center;min-width:300px;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div style="position:fixed;top:80px;left:50%;transform:translateX(-50%);background:#f8d7da;color:#721c24;padding:15px 30px;margin:0;border-radius:8px;border:1px solid #f5c6cb;z-index:9999;text-align:center;min-width:300px;box-shadow:0 4px 6px rgba(0,0,0,0.1);">
                    {{ session('error') }}
                </div>
            @endif

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
            <li><a href="shop.php" class="breadcrumb__link">Shop</a></li>
        </ul>
    </section>

    <!--============ SIDEBAR DAN SHOP ==============-->
    <div class="shop__container">
        <aside class="sidebar">
            <div class="sidebar-category">
                <div class="sidebar-top">
                    <h3 class="sidebar-title">Category</h3>
                </div>

                <ul class="sidebar-menu-category-list">
                    @php
                    $categories = ['Accessories', 'Bottoms', 'Denim', 'Leggings', 'Short', 'Sets'];
                    $currentKategori = request('kategori');
                    @endphp

                    @foreach($categories as $cat)
                    <li class="sidebar-menu-category">
                        <a
                            href="{{ route('shop', ['kategori' => $cat]) }}"
                            class="category-link {{ $currentKategori == $cat ? 'active' : '' }}">
                            {{ $cat }}
                        </a>
                    </li>
                    @endforeach


                </ul>


            </div>

            <div class="sidebar-bestsellers">
                <h3 class="sidebar-title">Best Sellers</h3>
                <ul class="bestseller-list">
                    <li class="bestseller-item">
                        <img src="{{ asset('assets/img/10.jpg') }}" alt="Strip Legging" class="bestseller-img" />
                        <div class="bestseller-info">
                            <p class="bestseller-name">Strip Legging</p>
                            <div class="bestseller-rating">★★★★★</div>
                            <div class="bestseller-price">
                                <span class="old-price">Rp 350.000</span>
                                <span class="new-price">Rp 250.000</span>
                            </div>
                        </div>
                    </li>

                    <li class="bestseller-item">
                        <img src="{{ asset('assets/img/2.jpg') }}" alt="Cute Pink Skirt" class="bestseller-img" />
                        <div class="bestseller-info">
                            <p class="bestseller-name">Cute Pink Skirt</p>
                            <div class="bestseller-rating">★★★★★</div>
                            <div class="bestseller-price">
                                <span class="old-price">Rp 105.000</span>
                                <span class="new-price">Rp 99.000</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </aside>

        <!--============ PRODUCT ==============-->
        <section class="products section container">
            <div class="tab__items">
                <div class="tab__item active-tab" id="featured">
                    <div class="products_shop__container grid">
                        @if(isset($products) && count($products) > 0)
                        @foreach($products as $product)
                        <div class="product__item">
                            <div class="product__banner" style="position: relative;">
                                <a href="#" class="product__images" style="position: relative; z-index: 1;">
                                    @php
                                    $foto = $product['foto'];
                                    @endphp

                                    <img
                                        src="{{ Str::startsWith($foto, ['http://', 'https://']) 
        ? $foto 
        : asset('assets/img/' . $foto) 
    }}"
                                        alt="{{ $product['nama_produk'] }}"
                                        class="product__img default" />

                                    <img
                                        src="{{ Str::startsWith($foto, ['http://', 'https://']) 
        ? $foto 
        : asset('assets/img/' . $foto) 
    }}"
                                        alt="{{ $product['nama_produk'] }}"
                                        class="product__img hover" />

                                </a>

                                <!-- Badge Hot -->
                                <div class="product__badge light-pink" style="position: absolute; top: 25px; left: 25px; z-index: 15; background-color:rgb(249, 93, 121); color: #fff; padding: 4px 10px; border-radius: 25px; font-size: 0.8rem; font-weight: 600;">
                                    Hot
                                </div>

                                <!-- Action Buttons -->
                                <div class="product__actions" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); display: flex; gap: 8px; z-index: 20;">
                                    <!-- Quick View -->
                                    <form action="quick_view.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="id_produk" value="{{ $product['id_produk'] }}">
                                        <button type="submit" class="action__btn" aria-label="Quick View" title="Quick View" style="background-color: #F6EAF0; border-color: #f8c8d1;">
                                            <i class="fi fi-rs-eye" style="color: #465D53;"></i>
                                        </button>
                                    </form>

                                    <!-- Wishlist -->
                                    <form action="add_to_wishlist.php" method="POST" style="display:inline;">
                                        <input type="hidden" name="id_produk" value="{{ $product['id_produk'] }}">
                                        <button type="submit" class="action__btn" aria-label="Add to Wishlist" title="Add to Wishlist" style="background-color: #F6EAF0; border-color: #f8c8d1;">
                                            <i class="fi fi-rs-heart" style="color: #465D53;"></i>
                                        </button>
                                    </form>

                                    <!-- Compare -->
                                    <form action="compare.php" method="GET" style="display:inline;">
                                        <input type="hidden" name="id_produk" value="{{ $product['id_produk'] }}">
                                        <button type="submit" class="action__btn" aria-label="Compare" title="Compare" style="background-color: #F6EAF0; border-color: #f8c8d1;">
                                            <i class="fi fi-rs-shuffle" style="color: #465D53;"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="product__content">
                                <span class="product__category" style="margin-top: 12px; display: block;">
    {{ $product['kategori'] }}
</span>


                                <h3 class="product__title">{{ $product['nama_produk'] }}</h3>

                                <div class="product__rating">
                                    @for($i = 0; $i < 5; $i++)
                                        <i class='fi fi-rs-star'></i>
                                        @endfor
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp{{ number_format($product['harga'], 0, ',', '.') }}</span>
                                </div>

                                <!-- Add to Cart -->
                                <form action="{{ route('cart.add') }}" method="POST" style="margin-top: 10px;">
                                    @csrf
                                    <input type="hidden" name="id_produk" value="{{ $product['id_produk'] }}">
                                    <button type="submit" class="action__btn cart__btn" aria-label="Add to Cart" style="background-color:#F6EAF0; border-color:#f8c8d1;">
                                        <i class="fi fi-rs-shopping-bag-add" style="color:#465D53;"></i>
                                    </button>
                                </form>
                            </div>
                        </div> 
                        @endforeach
                        @else
                        <p>No products found.</p>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>

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
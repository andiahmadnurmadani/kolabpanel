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

    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <title>Baby World </title>
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

    <!--============ MAIN ==============-->
    <main class="main">
        <!--============ HOME ==============-->
        <section class="home section--lg">
            <div class="home__container container grid">
                <div class="home__content">
                    <span class="home__subtitle">Newest lovely shirt!!</span>
                    <h1 class="home__title">
                        Cuties Trending <span>Great Collection</span>
                    </h1>
                    <p class="home__description">
                        Save more with coupons & up to 50% off
                    </p>
                    <a href="shop.php" class="btn" style="display: inline-block; width: auto; max-width: 200px; padding: 12px 30px; text-align: center; line-height: 1.4;"><span>Shop Now</span></a>
                </div>

                <img src="assets/img/home.jpg" alt="" class="home__img" />
            </div>
        </section>


        <!--============ CATEGORIES ==============-->
        <section class="categoriys container section">
            <h3 class="section__title">Popular Categories</h3>

            <div class="categories__container swiper">
                <div class="swiper-wrapper">
                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/shirt1.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Shirt</h3>
                    </a>

                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/shoes2.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Shoes</h3>
                    </a>

                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/bodysuit3.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Bodysuit</h3>
                    </a>

                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/socks4.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Socks</h3>
                    </a>

                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/tempattidur5.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Stroller</h3>
                    </a>

                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/pita6.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Ribbon</h3>
                    </a>

                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/babyshirt7.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Shirt</h3>
                    </a>

                    <a href="shop.php" class="category__item swiper-slide">
                        <img src="assets/img/babyshirt8.jpg" alt="" class="category__img">

                        <h3 class="category__title">Baby Shirt</h3>
                    </a>

                </div>

                <div class="swiper-button-next">
                    <img src="assets/img/angle-right.png" alt="">
                </div>
                <div class="swiper-button-prev">
                    <img src="assets/img/angle-left.png" alt="">
                    <i class="fi fi-br-angle-left"></i>
                </div>
            </div>
        </section>

        <!--============ PRODUCTS ==============-->
        <section class="products section container">
            <div class="tab__btns">
                <span class="tab__btn active-tab" data-target="#featured">Featured</span>
                <span class="tab__btn" data-target="#popular">Popular</span>
                <span class="tab__btn" data-target="#new-added">New Added</span>
            </div>

            <div class="tab__items">
                <div class="tab__item active-tab" content id="featured">
                    <div class="products__container grid">
                        <!-- Product 1 -->
                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/1.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/1.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-pink">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Ice Cream Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp238.000</span>
                                    <span class="old__price">Rp138.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                        <!-- Product 2 -->
                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/2.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/2.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-green">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Circle Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp138.000</span>
                                    <span class="old__price">Rp88.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/3.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/3.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-green">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Circle Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp138.000</span>
                                    <span class="old__price">Rp88.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/4.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/4.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-green">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Circle Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp138.000</span>
                                    <span class="old__price">Rp88.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/5.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/5.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-green">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Circle Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp138.000</span>
                                    <span class="old__price">Rp88.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/6.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/6.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-green">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Circle Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp138.000</span>
                                    <span class="old__price">Rp88.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/7.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/7.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-green">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Circle Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp138.000</span>
                                    <span class="old__price">Rp88.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                        <div class="product__item">
                            <div class="product__banner">
                                <a href="details.php" class="product__images">
                                    <img src="{{ asset('assets/img/8.jpg') }}" alt="" class="product__img default" />
                                    <img src="{{ asset('assets/img/8.jpg') }}" alt="" class="product__img hover" />
                                </a>

                                <div class="product__actions">
                                    <a href="#" class="action__btn" aria-label="Quick View">
                                        <i class="fi fi-rs-eye"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                        <i class="fi fi-rs-heart"></i>
                                    </a>
                                    <a href="#" class="action__btn" aria-label="Compare">
                                        <i class="fi fi-rs-shuffle"></i>
                                    </a>
                                </div>

                                <div class="product__badge light-green">Hot</div>
                            </div>

                            <div class="product__content">
                                <span class="product__category">Clothing</span>
                                <a href="details.php">
                                    <h3 class="product__title">Circle Shirt</h3>
                                </a>
                                <div class="product__rating">
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                    <i class='fi fi-rs-star'></i>
                                </div>

                                <div class="product__price flex">
                                    <span class="new__price">Rp138.000</span>
                                    <span class="old__price">Rp88.000</span>
                                </div>

                                <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                    <i class="fi fi-rs-shopping-bag-add"></i>
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="tab__item" content id="popular">
                <div class="products__container grid">
                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/10.jpg" alt="" class="product__img default" />

                                <img src="assets/img/10.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-pink">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/9.jpg" alt="" class="product__img default" />

                                <img src="assets/img/9.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Elephant Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp138.000</span>
                                <span class="old__price">Rp100.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/8.jpg" alt="" class="product__img default" />

                                <img src="assets/img/8.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-orange">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Bear Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp100.000</span>
                                <span class="old__price">Rp85.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/7.jpg" alt="" class="product__img default" />

                                <img src="assets/img/7.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-blue">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Bear Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp85.000</span>
                                <span class="old__price">Rp50.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/6.jpg" alt="" class="product__img default" />

                                <img src="assets/img/6.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">-30%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Buy 1 Get 1</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/5.jpg" alt="" class="product__img default" />

                                <img src="assets/img/5.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">-22%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Green Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp138.000</span>
                                <span class="old__price">Rp115.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/4.jpg" alt="" class="product__img default" />

                                <img src="assets/img/4.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">-22%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Buy 2 Get 1</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp250.000</span>
                                <span class="old__price">Rp150.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/3.jpg" alt="" class="product__img default" />

                                <img src="assets/img/3.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Buy 2 Get 1</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp245.000</span>
                                <span class="old__price">Rp145.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="tab__item" content id="new-added">
                <div class="products__container grid">
                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/1.jpg" alt="" class="product__img default" />

                                <img src="assets/img/1.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-pink">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/2.jpg" alt="" class="product__img default" />

                                <img src="assets/img/2.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/3.jpg" alt="" class="product__img default" />

                                <img src="assets/img/3.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-orange">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/4.jpg" alt="" class="product__img default" />

                                <img src="assets/img/4.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-blue">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/5.jpg" alt="" class="product__img default" />

                                <img src="assets/img/5.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">-30%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.php">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.php" class="product__images">
                                <img src="assets/img/6.jpg" alt="" class="product__img default" />

                                <img src="assets/img/6.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">-22%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/7.jpg" alt="" class="product__img default" />

                                <img src="assets/img/7.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">-22%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/8.jpg" alt="" class="product__img default" />

                                <img src="assets/img/8.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </section>

        <!--============ DEALS  ==============-->
        <section class="deals section">
            <div class="deals__container container grid">
                <div class="deals__item">
                    <div class="deals__group">
                        <h3 class="deals__brand">Deal of the Day</h3>
                        <span class="deals__category">Limited for this cuties. </span>
                    </div>

                    <h4 class="deals__title">Summer Collection for Your Babies</h4>

                    <div class="deals__price flex">
                        <span class="new__price">Rp120.000</span>
                        <span class="old__price">Rp220.000</span>
                    </div>

                    <div class="deals__group">
                        <p class="deal__countdown-text">Hurry Up! Offer End In: </p>

                        <div class="countdown">
                            <div class="countdown__amount">
                                <p class="countdown__period">02</p>
                                <span class="unit">Days</span>
                            </div>
                            <div class="countdown__amount">
                                <p class="countdown__period">22</p>
                                <span class="unit">Hours</span>
                            </div>
                            <div class="countdown__amount">
                                <p class="countdown__period">57</p>
                                <span class="unit">Mins</span>
                            </div>
                            <div class="countdown__amount">
                                <p class="countdown__period">24</p>
                                <span class="unit">Sec</span>
                            </div>
                        </div>
                    </div>

                    <div class="deals__btn">
                        <a href="details.php" class="btn btn--md" style="display: inline-block; width: auto; max-width: 200px; padding: 12px 30px; text-align: center; line-height: 1.2;">Shop Now</a>
                    </div>
                </div>

                <div class="deals__item">
                    <div class="deals__group">
                        <h3 class="deals__brand">Baby Clothing</h3>
                        <span class="deals__category">Limited for this cuties. </span>
                    </div>

                    <h4 class="deals__title">Try Something New for Your Babies</h4>

                    <div class="deals__price flex">
                        <span class="new__price">Rp120.000</span>
                        <span class="old__price">Rp220.000</span>
                    </div>

                    <div class="deals__group">
                        <p class="deal__countdown-text">Hurry Up! Offer End In: </p>

                        <div class="countdown">
                            <div class="countdown__amount">
                                <p class="countdown__period">02</p>
                                <span class="unit">Days</span>
                            </div>
                            <div class="countdown__amount">
                                <p class="countdown__period">22</p>
                                <span class="unit">Hours</span>
                            </div>
                            <div class="countdown__amount">
                                <p class="countdown__period">57</p>
                                <span class="unit">Mins</span>
                            </div>
                            <div class="countdown__amount">
                                <p class="countdown__period">24</p>
                                <span class="unit">Sec</span>
                            </div>
                        </div>
                    </div>

                    <div class="deals__btn">
                        <a href="details.php" class="btn btn--md" style="display: inline-block; width: auto; max-width: 200px; padding: 12px 30px; text-align: center; line-height: 1.2;">Shop Now</a>
                    </div>
                </div>
            </div>
        </section>

        <!--============ NEW ARRIVALS  ==============-->
        <!--============ 
         <section class="new__arrivals container section">
            <h3 class="section__title"><span>New</span> Arrivals</h3>

            <div class="new__container swiper">
                <div class="swiper-wrapper">
                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/1.jpg" alt="" class="product__img default" />

                                <img src="assets/img/2.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-pink">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/2.jpg" alt="" class="product__img default" />

                                <img src="assets/img/2.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-green">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/3.jpg" alt="" class="product__img default" />

                                <img src="assets/img/3.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-orange">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/4.jpg" alt="" class="product__img default" />

                                <img src="assets/img/4.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-blue">Hot</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/5.jpg" alt="" class="product__img default" />

                                <img src="assets/img/5.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-orange">-30%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/6.jpg" alt="" class="product__img default" />

                                <img src="assets/img/6.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-pink">-22%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/7.jpg" alt="" class="product__img default" />

                                <img src="assets/img/7.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                            <div class="product__badge light-pink">-22%</div>
                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>

                    <div class="product__item swiper-slide">
                        <div class="product__banner">
                            <a href="details.html" class="product__images">
                                <img src="assets/img/8.jpg" alt="" class="product__img default" />

                                <img src="assets/img/8.jpg" alt="" class="product__img hover" />
                            </a>

                            <div class="product__actions">
                                <a href="#" class="action__btn" aria-label="Quick View">
                                    <i class="fi fi-rs-eye"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Add to Wishlist">
                                    <i class="fi fi-rs-heart"></i>
                                </a>

                                <a href="#" class="action__btn" aria-label="Compare">
                                    <i class="fi fi-rs-shuffle"></i>
                                </a>
                            </div>

                        </div>

                        <div class="product__content">
                            <span class="product__category">Clothing</span>
                            <a href="details.html">
                                <h3 class="product__title">Colorful Pattern Shirt</h3>
                            </a>
                            <div class="product__rating">
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                                <i class='fi fi-rs-star'></i>
                            </div>

                            <div class="product__price flex">
                                <span class="new__price">Rp238.000</span>
                                <span class="old__price">Rp138.000</span>
                            </div>

                            <a href="#" class="action__btn cart__btn" aria-label="Add to Cart">
                                <i class="fi fi-rs-shopping-bag-add"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="swiper-button-next">
                    <img src="assets/img/angle-right.png" alt="">
                </div>
                <div class="swiper-button-prev">
                    <img src="assets/img/angle-left.png" alt="">
                    <i class="fi fi-br-angle-left"></i>
                </div>
            </div>
        </section> 
        ==============-->

        <!--============ SHOWCASE  ==============-->
        <!--============     <section class="showcase section">
            <div class="showcase__container container grid">
                <div class="showcase__wrapper">
                    <h3 class="section__title">Cutest Releases</h3>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/9.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/10.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/1.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="showcase__wrapper">
                    <h3 class="section__title">Deals & Outlet</h3>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/9.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/10.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/1.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="showcase__wrapper">
                    <h3 class="section__title">Top Selling</h3>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/9.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/10.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/1.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="showcase__wrapper">
                    <h3 class="section__title">Trendy</h3>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/9.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/10.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>

                    <div class="showcase__item">
                        <a href="details.html" class="showcase__img-box ">
                            <img src="assets/img/1.jpg" alt="" class="showcase__img">
                        </a>

                        <div class="showcase__content">
                            <a href="details.html">
                                <h4 class="showcase__title">Floral Print Casual Cotton Dress</h4>
                            </a>

                            <div class="showcase__price flex">
                                <span class="new__price">Rp50.000</span>
                                <span class="old__price">Rp250.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section> ==============-->

        <!--============ NEWSLETTER  ==============-->
        <section class="newsletter section home__newsletter">
            <div class="newsletter__container container grid">
                <h3 class="newsletter__title flex">
                    <img src="assets/svg/mailbox-flag-up.svg" alt="" class="newsletter__icon">
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
    </main>

    <!--============ FOOTER ==============-->
    <footer class="footer__container">
        <div class="footer__container grid">
            <div class="footer__content">
                <a href="index.php" class="footer__logo">
                    <img src="assets/img/feelin'.png" alt="" class="footer__logo-img">
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
                        <a href="">
                            <img src="assets/svg/facebook.svg" alt="" class="footer__social-icon">
                        </a>

                        <a href="">
                            <img src="assets/svg/instagram.svg" alt="" class="footer__social-icon">
                        </a>

                        <a href="">
                            <img src="assets/svg/pinterest.svg" alt="" class="footer__social-icon">
                        </a>

                        <a href="">
                            <img src="assets/svg/youtube.svg" alt="" class="footer__social-icon">
                        </a>
                    </div>
                </div>
            </div>

            <div class="footer__content">
                <h3 class="footer__title">Address</h3>

                <ul class="footer__links">
                    <li><a href="" class="footer__link">About Us</a></li>
                    <li><a href="" class="footer__link">Delivery Information</a></li>
                    <li><a href="" class="footer__link">Privacy Policy</a></li>
                    <li><a href="" class="footer__link">Terms & Conditions</a></li>
                    <li><a href="" class="footer__link">Contact Us</a></li>
                    <li><a href="" class="footer__link">Support Center</a></li>
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
                <div class="footer__contents">
                    <img src="assets/img/card.png" alt="" class="payment__img">
                    <img src="assets/img/visa.png" alt="" class="payment__img">
                    <img src="assets/img/paypal.png" alt="" class="payment__img">
                    <img src="assets/img/jcb.png" alt="" class="payment__img">
                </div>
            </div>
        </div>

        <div class="footer__bottom">
            <p class="copyright">&copy; 2024 BabyWorld. All rights reserved </p>
            <span class="designer">Designed by DianNadineAkhtar</span>
        </div>
    </footer>

    <!--============ SWIPER JS ==============-->
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!--============ SWIPER JS ==============-->
    <script src="assets/js/main.js"></script>
</body>

</html>
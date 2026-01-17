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
    <title>Accounts | Baby World</title>
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
            <li><a href="shop.php" class="breadcrumb__link">Accounts</a></li>
        </ul>
    </section>

    <!--============ ACCOUNTS  ==============-->
    <section class="accounts section--lg">

        <div class="accounts__container container grid">

            <div class="account__tabs">
                <p class="account__tab active-tab" data-target="#dashboard">
                    <i class="fi fi-rs-settings-sliders"></i> Dashboards
                </p>
                <p class="account__tab" data-target="#orders">
                    <i class="fi fi-rs-shopping-bag"></i> Orders
                </p>
                <p class="account__tab" data-target="#update-profile">
                    <i class="fi fi-rs-user"></i> Update Profile
                </p>
                <p class="account__tab" data-target="#address">
                    <i class="fi fi-rs-marker"></i> My Address
                </p>
                <p class="account__tab" data-target="#change-password">
                    <i class="fi fi-rs-user"></i> Change Passwords
                </p>
                <p class="account__tab" data-target="#delete-account">
                    <i class="fi fi-rs-trash"></i> Delete Account
                </p>
                <a href="{{ route('logout') }}" class="account__tab">
                    <i class="fi fi-rs-exit"></i> Logout
                </a>
            </div>

            <div class="tabs__content">
                {{-- Dashboard --}}
                {{-- NOTIFICATION --}}
                @if (session('success'))
                <div class="alert alert-success"
                    style="padding: 12px; background: #d4edda; color: #155724; 
               margin-bottom: 20px; border-radius: 6px;">
                    {{ session('success') }}
                </div>
                @endif

                @if (session('error'))
                <div class="alert alert-error"
                    style="padding: 12px; background: #f8d7da; color: #721c24; 
               margin-bottom: 20px; border-radius: 6px;">
                    {{ session('error') }}
                </div>
                @endif
                <div class="tab__content active-tab" id="dashboard">
                    <h3 class="tab__header">Hello {{ $user->name }}!</h3>
                    <div class="tab__body">
                        <p class="tab__description">
                            From your account dashboard, you can view recent orders, manage addresses, and update your account details.
                        </p>
                    </div>
                </div>

                {{-- Orders --}}
                <div class="tab__content" id="orders">
                    <h3 class="tab__header">Your Orders</h3>
                    <div class="tab__body">
                        <table class="placed__order-table">
                            <tr>
                                <th>Orders</th>
                                <th>Dates</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Actions</th>
                            </tr>

                            @forelse ($orders as $order)
                            <tr>
                                <td>#{{ $order->kode_transaksi }}</td>
                                <td>{{ $order->created_at->format('F d, Y') }}</td>
                                <td>{{ ucfirst($order->status) }}</td>
                                <td>Rp{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                <td><a href="#" class="view__order">View</a></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5">Belum ada transaksi.</td>
                            </tr>
                            @endforelse
                        </table>
                    </div>
                </div>

                {{-- Update Profile Form --}}
                <div class="tab__content" id="update-profile">
                    <h3 class="tab__header">Update Profile</h3>
                    <div class="tab__body">
                        <form action="{{ route('accounts.updateProfile') }}" method="POST" class="form grid" enctype="multipart/form-data">
                            @csrf

                            <input type="text"
                                name="name"
                                placeholder="Your Name"
                                class="form__input"
                                value="{{ old('name', $user->name) }}"
                                required>

                            <input type="email"
                                name="email"
                                placeholder="Your Email"
                                class="form__input"
                                value="{{ old('email', $user->email) }}"
                                required>

                            <input type="file"
                                name="picture"
                                class="form__input"
                                accept="image/*">

                            {{-- Preview Foto --}}
                            @if($user->picture)
                            <div style="margin: 10px 0;">
                                <p style="margin-bottom: 5px; font-weight: 600;">Current Photo:</p>
                                <img src="{{ asset('storage/' . $user->picture) }}"
                                    alt="Profile"
                                    style="max-width: 150px; border-radius: 8px; border: 2px solid #ddd;">
                            </div>
                            @endif

                            <div class="form__btn">
                                <button type="submit">Save</button>
                            </div>
                        </form>

                        {{-- Success/Error Messages --}}
                        @if(session('success'))
                        <div style="color: green; margin-top: 1rem; padding: 10px; background: #d4edda; border-radius: 5px;">
                            {{ session('success') }}
                        </div>
                        @endif

                        @if(session('error'))
                        <div style="color: red; margin-top: 1rem; padding: 10px; background: #f8d7da; border-radius: 5px;">
                            {{ session('error') }}
                        </div>
                        @endif

                        @if($errors->any())
                        <div style="color: red; margin-top: 1rem; padding: 10px; background: #f8d7da; border-radius: 5px;">
                            @foreach($errors->all() as $error)
                            <p style="margin: 5px 0;">• {{ $error }}</p>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Address --}}
                <div class="tab__content" id="address">
                    <h3 class="tab__header">Shipping Address</h3>
                    <div class="tab__body">
                        <form action="{{ route('accounts.updateAddress') }}" method="POST" class="form grid">
                            @csrf
                            <textarea name="address" class="form__input" rows="4">{{ $user->address }}</textarea>
                            <div class="form__btn">
                                <button type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Change Password --}}
                <div class="tab__content" id="change-password">
                    <h3 class="tab__header">Change Password</h3>
                    <div class="tab__body">
                        <form action="{{ route('accounts.changePassword') }}" method="POST" class="form grid">
                            @csrf
                            <input type="password" name="current_password" placeholder="Current Password" class="form__input" required>
                            <input type="password" name="new_password" placeholder="New Password" class="form__input" required>
                            <input type="password" name="confirm_password" placeholder="Confirm Password" class="form__input" required>
                            <div class="form__btn">
                                <button type="submit">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                {{-- Delete Account --}}
                <div class="tab__content" id="delete-account">
                    <h3 class="tab__header">Delete Account</h3>
                    <div class="tab__body">
                        <p style="margin-bottom: 20px; color:rgb(255, 35, 57);">
                            Hapus akun secara permanen. Tindakan ini tidak dapat dibatalkan.
                        </p>

                        <form id="deleteAccountForm" action="{{ route('accounts.delete') }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <div class="form__btn">
                                <button type="button" id="deleteAccountBtn">Delete My Account</button>
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

    <script src="{{ asset('assets/js/accounts.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            document.addEventListener("click", function(e) {

                // jika tombol yg diklik adalah deleteAccountBtn
                if (e.target && e.target.id === "deleteAccountBtn") {

                    Swal.fire({
                        title: "Yakin ingin menghapus akun?",
                        text: "Akun kamu akan dihapus permanen dan tidak dapat dikembalikan.",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, hapus akun",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById("deleteAccountForm").submit();
                        }
                    });

                }

            });

        });
    </script>

    <<style>
        .swal2-confirm {
        background-color: #F72D73 !important;
        border-radius: 8px !important;
        padding: 10px 22px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        }

        .swal2-cancel {
        background-color: #ccc !important;
        border-radius: 8px !important;
        padding: 10px 22px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
        color: #333 !important;
        }

        .swal2-actions {
        gap: 12px !important;
        }

        .swal2-popup {
        border-radius: 18px !important;
        padding: 30px !important;
        }

        .swal2-title {
        font-size: 26px !important;
        font-weight: 700 !important;
        }

        .swal2-html-container {
        font-size: 17px !important;
        margin-top: 10px !important;
        }
        </style>



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
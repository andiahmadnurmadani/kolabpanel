<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Cart | Baby World</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
  <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-solid-rounded/css/uicons-solid-rounded.css">

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

  <!--============ BREADCRUMB ==============-->
  <section class="breadcrumb">
    <ul class="breadcrumb__list flex container">
      <li><a href="#" class="breadcrumb__link">Home</a></li>
      <li><span class="breadcrumb__link">></span></li>
      <li><span class="breadcrumb__link">Cart</span></li>
    </ul>
  </section>


  <!--============ CART TABLE ==============-->
  <section id="cart" class="section-p1">
    @if(session('success'))
    <div style="color: #2e7d32; font-weight: 600; margin-bottom: 10px;">{{ session('success') }}</div>
    @endif
    @if(session('error'))
    <div style="color: #c62828; font-weight: 600; margin-bottom: 10px;">{{ session('error') }}</div>
    @endif

    <table width="100%">
      <thead>
        <tr>
          <td>Remove</td>
          <td>Item</td>
          <td>Product</td>
          <td>Price</td>
          <td>Quantity</td>
          <td>Subtotal</td>
        </tr>
      </thead>
      <tbody id="cart-body">
        @forelse($items as $item)
        <tr>

          <!-- REMOVE BUTTON (Styled like cart.php version) -->
          <td style="text-align:center;">
            <form action="{{ route('cart.remove') }}" method="POST" onsubmit="return confirm('Yakin hapus produk ini?')">
              @csrf
              <input type="hidden" name="id_produk" value="{{ $item['id_produk'] }}">

              <button type="submit" class="remove-action" style="
                    background:none;
                    border:none;
                    padding:0;
                    cursor:pointer;">
                <i class="fi fi-sr-circle-xmark" style="
                        font-size:20px;
                        color:#ff4b77;"></i>
              </button>
            </form>
          </td>

          <!-- ITEM IMAGE -->
          <td style="text-align:center;">
            <img src="{{ asset($item['foto']) }}"
              alt="" width="60"
              style="display:block;margin:0 auto;">
          </td>

          <!-- PRODUCT NAME -->
          <td>{{ $item['nama_produk'] }}</td>

          <!-- PRICE -->
          <td>Rp{{ number_format($item['harga'], 0, ',', '.') }}</td>

          <!-- QUANTITY (match old PHP layout: centered input only) -->
          <td style="text-align:center; vertical-align:middle;">
            <div style="display:flex; align-items:center; justify-content:center; height:100%;">
              <input type="number"
                class="qty"
                value="{{ $item['quantity'] }}"
                min="1"
                readonly
                style="text-align:center; height:36px; line-height:36px;">
            </div>
          </td>

          <!-- SUBTOTAL -->
          <td>Rp{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
        </tr>
        @empty
        <tr>
          <td colspan="6">Keranjang Anda kosong.</td>
        </tr>
        @endforelse
      </tbody>

    </table>
  </section>

  <!--============ COUPON & TOTALS ==============-->
  <section id="cart-add" class="section-p1">

    <div id="coupon">
      <h3>Apply Coupon</h3>

      <form action="{{ route('cart.applyVoucher') }}" method="POST">
        @csrf
        <input type="text" name="kode_voucher" placeholder="Enter Your Coupon" class="form__input">
        <button type="submit" class="normal">Apply</button>
      </form>

      {{-- message --}}
      @if(session('success'))
      <p style="color:green; font-weight:bold; margin-top:10px;">
        {{ session('success') }}
      </p>
      @endif

      @if(session('error'))
      <p style="color:red; font-weight:bold; margin-top:10px;">
        {{ session('error') }}
      </p>
      @endif
    </div>

    <div id="subtotal">
      <h3>Cart Totals</h3>

      <table>
        <tr>
          <td>Cart Total</td>
          <td id="cart-total">
            Rp{{ number_format($total, 0, ',', '.') }}

            @if(!empty($voucher))
            <br>
            <span style="font-size:13px; color:green;">
              -{{ $voucher['diskon'] }}% ({{ $voucher['kode'] }})
            </span>
            @endif
          </td>
        </tr>

        <tr>
          <td>Shipping</td>
          <td>Free</td>
        </tr>

        <tr>
          <td><strong>Total</strong></td>
          <td><strong id="total-all">
              Rp{{ number_format($final_total, 0, ',', '.') }}
            </strong></td>
        </tr>
      </table>

      <div class="button__co">
        <a href="{{ route('checkout.index') }}" class="normal">Checkout</a>
      </div>
    </div>
  </section>


  <!--============ FOOTER (ringkas) ==============-->
  <footer class="footer__container">
    <div class="footer__bottom">
      <p class="copyright">&copy; 2024 BabyWorld. All rights reserved</p>
      <span class="designer">Designed by DianNadineAkhtar</span>
    </div>
  </footer>

  <script>
    localStorage.removeItem('cartItems');
  </script>
</body>

</html>
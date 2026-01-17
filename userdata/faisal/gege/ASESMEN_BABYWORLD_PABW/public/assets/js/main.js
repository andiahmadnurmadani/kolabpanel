/* ============= SHOW MENU ============= */

/* ============= Menu Show ============= */
/**Validate if constant exist */

/* ============= Hide Show  ============= */
/**Validate if constant exist */



/* ============= IMAGE GALLERY ============= */
function imgGallery() {
  const mainImg = document.querySelector('.details__main-img'),
  smallImg= document.querySelectorAll('.details__small-img');

  smallImg.forEach((img) => {
    img.addEventListener('click', function() {
      mainImg.src = this.src;
    })
  })
}

imgGallery();

/*============= DETAILS INFO & REVIEWS =============== */
document.addEventListener("DOMContentLoaded", () => {
  const detailTabs = document.querySelectorAll(".detail__tab");
  const detailContents = document.querySelectorAll(".details__tab-content");

  detailTabs.forEach(tab => {
    tab.addEventListener("click", () => {
      // Hapus active-tab di semua tab dan konten
      detailTabs.forEach(t => t.classList.remove("active-tab"));
      detailContents.forEach(c => c.classList.remove("active-tab"));

      // Tambahkan active-tab pada tab yang diklik
      tab.classList.add("active-tab");

      // Ambil target dari data-target (misal #info, #reviews)
      const target = tab.getAttribute("data-target");
      const contentToShow = document.querySelector(target);

      // Tampilkan konten yang sesuai
      if (contentToShow) {
        contentToShow.classList.add("active-tab");
      }
    });
  });
});


/* ============= SWIPER CATEGORIES ============= */
var swiperCategories = new Swiper('.categories__container', {
  spaceBetween: 24,
  loop: true,

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },

  breakpoints: {
    640: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 4,
      spaceBetween: 40,
    },
    1400: {
      slidesPerView: 6,
      spaceBetween: 24,
    },
  },
});

/* ============= SWIPER PRODUCTS ============= */
var swiperProducts = new Swiper('.new__container', {
  spaceBetween: 24,
  loop: true,

  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },

  breakpoints: {
    640: {
      slidesPerView: 2,
      spaceBetween: 20,
    },
    768: {
      slidesPerView: 4,
      spaceBetween: 40,
    },
    1400: {
      slidesPerView: 4,
      spaceBetween: 24,
    },
  },
});

/* ============= PRODUCTS TABS ============= */

const tabs = document.querySelectorAll('[data-target]'),
  tabContents = document.querySelectorAll('[content]');

tabs.forEach((tab) => {
  tab.addEventListener('click', () => {
    const target = document.querySelector(tab.dataset.target);
    tabContents.forEach((tabContent) => {
      tabContent.classList.remove('active-tab');
    });

    target.classList.add('active-tab');

    tabs.forEach((tab) => {
      tab.classList.remove('active-tab');
    });

    tab.classList.add('active-tab');
  });
});

/* ============= ACCOUNTS ============= */

document.addEventListener("DOMContentLoaded", () => {
  const tabs = document.querySelectorAll(".account__tab");
  const contents = document.querySelectorAll(".tab__content");

  tabs.forEach(tab => {
      tab.addEventListener("click", () => {
          // Hapus class 'active-tab' dari semua tab
          tabs.forEach(item => item.classList.remove("active-tab"));
          // Hapus class 'active-tab' dari semua konten
          contents.forEach(content => content.classList.remove("active-tab"));

          // Tambahkan class 'active-tab' ke tab yang diklik
          tab.classList.add("active-tab");

          // Dapatkan target tab dan tampilkan konten terkait
          const target = document.querySelector(tab.getAttribute("data-target"));
          if (target) {
              target.classList.add("active-tab");
          }
      });
  });
});

// Function to handle color selection
document.querySelectorAll('.color__link').forEach(function (colorElement) {
    colorElement.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent page refresh on link click
        let selectedColor = e.target.getAttribute('data-color'); // Get selected color

        // Set active color and change main image or other properties if needed
        document.querySelectorAll('.color__link').forEach(function (item) {
            item.classList.remove('color-active');
        });
        e.target.classList.add('color-active');
        
        // Optional: Change main image or other things based on selected color
        console.log('Selected color:', selectedColor);
    });
});

// Function to handle size selection
document.querySelectorAll('.size__link').forEach(function (sizeElement) {
    sizeElement.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent page refresh on link click
        let selectedSize = e.target.getAttribute('data-size'); // Get selected size

        // Set active size
        document.querySelectorAll('.size__link').forEach(function (item) {
            item.classList.remove('size-active');
        });
        e.target.classList.add('size-active');

        console.log('Selected size:', selectedSize);
    });
});

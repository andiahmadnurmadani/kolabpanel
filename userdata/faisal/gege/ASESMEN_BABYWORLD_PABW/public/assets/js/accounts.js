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
  
import './bootstrap';
// --- Logika Modal Interaktif ---
function openModal(action) {
    const modal = document.getElementById('app-modal');
    const title = document.getElementById('modal-title');
    const content = document.getElementById('modal-content');

    title.textContent = `Aksi: ${action}`;
    
    let message = '';
    switch(action) {
        case 'Masuk':
            message = 'Ini adalah simulasi! Dalam aplikasi nyata, Anda akan diarahkan ke halaman login untuk otentikasi.';
            break;
        case 'Daftar':
        case 'Mulai':
        case 'Daftar Gratis':
            message = 'Selamat datang di NotezQue! Anda akan diarahkan ke halaman registrasi.';
            break;
        case 'Upgrade Pro':
            message = 'Siap untuk menjadi Mahasiswa Pro? Halaman ini akan memuat formulir pembayaran yang aman.';
            break;
        case 'Kontak Sales':
            message = 'Terima kasih atas minat Institusi Anda. Silakan hubungi kami via email: sales@notezque.id';
            break;
        case 'Lihat Demo':
            message = 'Simulasi demo: Anda akan masuk ke lingkungan aplikasi yang dapat diuji coba.';
            break;
        default:
            message = 'Aksi tidak dikenali.';
    }
    content.textContent = message;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function closeModal() {
    const modal = document.getElementById('app-modal');
    modal.classList.remove('flex');
    modal.classList.add('hidden');
}

// --- Logika Testimoni Carousel ---
const testimonials = [
    {
        name: "Risa A.",
        role: "Mahasiswa Tingkat Akhir",
        avatar: "https://placehold.co/400x400/0AA0F6/ffffff?text=RA",
        text: "NotezQue adalah *game changer*. Saya tidak pernah lagi telat mengumpulkan tugas. Fitur kolaborasinya sangat membantu skripsi kelompok!",
        source: "Universitas Indonesia"
    },
    {
        name: "Kevin B.",
        role: "Mahasiswa Aktif Organisasi",
        avatar: "https://placehold.co/400x400/0E7C7F4/ffffff?text=KB",
        text: "Kalender yang terintegrasi memungkinkan saya menyeimbangkan antara kuliah dan kegiatan organisasi tanpa merasa kewalahan. Sangat direkomendasikan!",
        source: "Institut Teknologi Bandung"
    },
    {
        name: "Dr. Nadia H.",
        role: "Dosen Pembimbing",
        avatar: "https://placehold.co/400x400/94A3B8/ffffff?text=NH",
        text: "Saya melihat peningkatan signifikan dalam kedisiplinan mahasiswa sejak mereka menggunakan NotezQue untuk mengelola tugas dan deadline.",
        source: "Universitas Gajah Mada"
    }
];

let currentIndex = 0;

function renderTestimoni() {
    const container = document.getElementById('testimoni-content');
    const dotsContainer = document.getElementById('testimoni-dots');
    const t = testimonials[currentIndex];

    // Render Konten
    container.innerHTML = `
        <div class="flex flex-col md:flex-row items-start md:space-x-8 w-full p-4">
            <img src="${t.avatar}" alt="${t.name}" class="w-16 h-16 rounded-full border-2 border-notezque-primary object-cover mb-4 md:mb-0 flex-shrink-0">
            <div>
                <p class="text-gray-300 text-lg italic leading-relaxed mb-4">
                    <i data-lucide="quote" class="inline w-5 h-5 text-notezque-primary mr-2 align-top"></i>
                    "${t.text}"
                </p>
                <p class="text-xl font-bold">${t.name}</p>
                <p class="text-sm text-notezque-primary font-medium">${t.role}, ${t.source}</p>
            </div>
        </div>
    `;
    
    // Render Dots
    dotsContainer.innerHTML = testimonials.map((_, index) => `
        <span class="dot block w-3 h-3 rounded-full cursor-pointer transition duration-300 ${index === currentIndex ? 'bg-notezque-primary' : 'bg-gray-600 hover:bg-gray-400'}" onclick="goToTestimoni(${index})"></span>
    `).join('');

    // Pastikan ikon di konten dinamis juga dibuat
    if (typeof lucide !== 'undefined' && typeof lucide.createIcons === 'function') {
        lucide.createIcons(); 
    }
}

function nextTestimoni() {
    currentIndex = (currentIndex + 1) % testimonials.length;
    renderTestimoni();
}

function prevTestimoni() {
    currentIndex = (currentIndex - 1 + testimonials.length) % testimonials.length;
    renderTestimoni();
}

function goToTestimoni(index) {
    currentIndex = index;
    renderTestimoni();
}

// Auto-play Testimoni (setiap 7 detik)
setInterval(nextTestimoni, 7000);

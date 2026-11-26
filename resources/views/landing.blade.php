<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kurikulum UKRI</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blade-animations.css') }}">
</head>
<body>
    <header>
        <div class="header-left">
            <img src="{{ asset('storage/logo ukri.png') }}" alt="Logo UKRI" class="university-logo">
            <div class="header-text">
                <h1>UNIVERSITAS KEBANGSAAN</h1>
                <p>REPUBLIK INDONESIA</p>
            </div>
        </div>
        <div class="header-right">
            <nav>
                <a href="#">Beranda</a>
                <div class="dropdown">
                    <a href="#">
                        Fakultas
                        <span style="margin-left:3px; display:inline-block; vertical-align:middle;">
                            <svg width="15" height="15" viewBox="0 0 15 20" fill="none" style="display:inline;vertical-align:middle;">
                                <path d="M6 8l4 4 4-4" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </a>
                    <div class="dropdown-menu dropdown-content">
                        <a href="https://fti.ukri.ac.id" target="_blank" rel="noopener noreferrer">Fakultas Teknologi Industri</a>
                        <a href="https://fiksi.ukri.ac.id" target="_blank" rel="noopener noreferrer">Fakultas Ilmu Komputer Dan Sistem Informasi</a>
                        <a href="https://ftsp.ukri.ac.id" target="_blank" rel="noopener noreferrer">Fakultas Tekni Sipil Dan Perencanaan</a>
                        <a href="https://fasos.ukri.ac.id" target="_blank" rel="noopener noreferrer">Fakultas Ilmu Sosial Dan Sastra</a>
                        <a href="https://fe.ukri.ac.id" target="_blank" rel="noopener noreferrer">Fakultas Ekonomi</a>
                        <a href="https://mipa.ukri.ac.id" target="_blank" rel="noopener noreferrer">Fakultas Matematika Dan Ilmu Pengetahuan Alam</a>
                        <a href="https://faperta.ukri.ac.id" target="_blank" rel="noopener noreferrer">Fakultas Pertanian</a>
                    </div>
                </div>
                <a href="#">Kontak</a>
            </nav>
            <a href="/login" class="btn-masuk">Masuk</a>
        </div>
    </header>

    <main class="hero">
        <div class="hero-text">
            <p class="hero-tag">Kurikulum OBE UKRI</p>
            <h2>Belajar terarah, terukur, dan relevan dengan kebutuhan industri.</h2>
            <p class="hero-desc">
                Kurikulum berbasis capaian (Outcome Based Education) membantu mahasiswa fokus pada kompetensi inti,
                portofolio yang nyata, dan kesiapan karier sejak awal perkuliahan.
            </p>
            <div class="hero-actions"></div>
            <div class="hero-meta">
                <div class="meta-item">
                    <span class="meta-value">7</span>
                    <span class="meta-label">Fakultas</span>
                </div>
                <div class="meta-item">
                    <span class="meta-value">120+</span>
                    <span class="meta-label">Mata Kuliah</span>
                </div>
                <div class="meta-item">
                    <span class="meta-value">4.5★</span>
                    <span class="meta-label">Kepuasan</span>
                </div>
            </div>
        </div>
        <div class="hero-media">
            <div class="hero-slider" aria-label="Media kampus dengan tombol dot">
                <div class="hero-slides">
                    <img src="{{ asset('storage/ukri.jpg') }}" alt="Gedung UKRI" class="hero-image">
                    <img src="{{ asset('storage/ukri.jpg') }}" alt="Suasana kampus UKRI" class="hero-image">
                    <img src="{{ asset('storage/ukri.jpg') }}" alt="Aktivitas mahasiswa" class="hero-image">
                    <img src="{{ asset('storage/ukri.jpg') }}" alt="Laboratorium kampus" class="hero-image">
                </div>
                <div class="hero-dots" role="tablist" aria-label="Pilih gambar">
                    <button class="hero-dot is-active" type="button" aria-label="Gambar 1"></button>
                    <button class="hero-dot" type="button" aria-label="Gambar 2"></button>
                    <button class="hero-dot" type="button" aria-label="Gambar 3"></button>
                    <button class="hero-dot" type="button" aria-label="Gambar 4"></button>
                </div>
            </div>
        </div>
    </main>

    <section id="galeri" class="gallery">
        <div class="gallery-head">
            <h3>Galeri Kampus</h3>
            <br>
        </div>
        <div class="scroll-row" aria-label="Galeri kampus dengan scrollbar horizontal">
            <article class="gallery-card">
                <img src="{{ asset('storage/ukri.jpg') }}" alt="Suasana kampus UKRI">
                <div class="card-body">
                    <h4>Suasana Kampus</h4>
                    <p>Lingkungan belajar yang nyaman dan hijau.</p>
                </div>
            </article>
            <article class="gallery-card">
                <img src="{{ asset('storage/ukri.jpg') }}" alt="Gedung utama UKRI">
                <div class="card-body">
                    <h4>Gedung Utama</h4>
                    <p>Fasilitas modern untuk mendukung perkuliahan.</p>
                </div>
            </article>
            <article class="gallery-card">
                <img src="{{ asset('storage/ukri.jpg') }}" alt="Aktivitas mahasiswa">
                <div class="card-body">
                    <h4>Aktivitas Mahasiswa</h4>
                    <p>Kolaborasi lintas program studi dan komunitas.</p>
                </div>
            </article>
            <article class="gallery-card">
                <img src="{{ asset('storage/ukri.jpg') }}" alt="Laboratorium kampus">
                <div class="card-body">
                    <h4>Laboratorium</h4>
                    <p>Praktikum terstruktur dengan peralatan lengkap.</p>
                </div>
            </article>
            <article class="gallery-card">
                <img src="{{ asset('storage/ukri.jpg') }}" alt="Kegiatan lapangan">
                <div class="card-body">
                    <h4>Kegiatan Lapangan</h4>
                    <p>Project nyata yang terhubung dengan industri.</p>
                </div>
            </article>
        </div>
    </section>

    @include('partials.page-footer', ['footerClass' => 'page-footer--public'])

    <script>
        const slider = document.querySelector('.hero-slider');
        if (slider) {
            const slides = slider.querySelector('.hero-slides');
            const dots = Array.from(slider.querySelectorAll('.hero-dot'));
            let index = 0;

            const update = (nextIndex) => {
                index = nextIndex;
                slides.style.transform = `translateX(-${index * 100}%)`;
                dots.forEach((dot, i) => dot.classList.toggle('is-active', i === index));
            };

            dots.forEach((dot, i) => {
                dot.addEventListener('click', () => update(i));
            });

            update(0);
        }
    </script>
    <script src="{{ asset('js/blade-animations.js') }}" defer></script>
</body>
</html>

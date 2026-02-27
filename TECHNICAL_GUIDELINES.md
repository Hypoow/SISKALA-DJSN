# Petunjuk Teknis Proyek

## 1. Deskripsi Singkat

Proyek ini bertujuan untuk mengintegrasikan proses pengadaan barang dan jasa di lingkungan pemerintah (Satker) dengan sistem Katalog Elektronik (INAPROC) dan sistem pembayaran SAKTI agar berjalan secara terpadu, efisien, dan transparan layaknya platform e-commerce modern.

Sistem ini memfasilitasi interaksi **end-to-end** antara Satker (sebagai pembeli) dan Penyedia Barang/Jasa secara digital. Alur utama mencakup pemesanan melalui Katalog Elektronik, pembayaran melalui SAKTI yang terintegrasi dengan Payment Gateway (PG), serta distribusi dana otomatis (split payment) ke berbagai pihak terkait (Penyedia, Telkom, Kurir, dan Kas Negara).

Dokumen ini menjelaskan poin-poin krusial yang harus diperhatikan dalam implementasi dan operasional sistem tersebut.

---

## 2. Beberapa Hal Yang Perlu Diperhatikan

Berikut adalah aspek-aspek kunci dalam teknis pelaksanaan dan alur sistem:

### A. Platform Pengadaan Terpusat
Pengadaan barang/jasa dilakukan melalui **Katalog Elektronik (INAPROC)**. Proses ini dilakukan oleh pembeli (Satker) dan penyedia dalam satu platform yang terintegrasi, mengadopsi mekanisme seperti *e-commerce* pada umumnya untuk kemudahan transaksi.

### B. Pembayaran Melalui SAKTI
Pembayaran atas pengadaan barang/jasa oleh Satker sebagai pembeli wajib dilakukan melalui aplikasi **SAKTI** (Sistem Aplikasi Keuangan Tingkat Instansi).

### C. Interaksi Digital Penuh
Seluruh interaksi antara Penyedia dan Satker dilakukan sepenuhnya di dalam sistem (*full system interaction*), meminimalkan proses manual untuk akurasi dan kecepatan.

### D. Alur Pembayaran via Payment Gateway (PG)
Berdasarkan desain INAPROC oleh PT Telkom, alur pembayaran adalah sebagai berikut:
1.  Pembayaran dari rekening Satker/BUN (Bendahara Umum Negara) tidak langsung ke penyedia.
2.  Dana ditujukan terlebih dahulu ke **rekening penampungan Payment Gateway (PG)**.

### E. Distribusi Dana Otomatis (Split Payment)
Dari rekening penampungan PG, pembayaran kemudian dipisahkan dan didistribusikan secara otomatis ke berbagai pos penerima, yaitu:
*   Rekening Penyedia (untuk nilai barang/jasa)
*   Fee Transaksi
*   PNBP Telkom
*   Biaya Kurir
*   Penyetoran Pajak ke Kas Negara

### F. Komponen Invoice/Tagihan
Setiap invoice atau tagihan yang diterbitkan harus memuat rincian lengkap:
*   Nilai Barang/Jasa
*   Biaya-biaya yang timbul dalam PBJ (Pengadaan Barang/Jasa)
*   Kewajiban Perpajakan yang berlaku

### G. Pembayaran LS Kontraktual
Khusus untuk mekanisme Pembayaran LS (Langsung) Kontraktual:
*   Wajib melakukan perekaman kontrak pada aplikasi INAPROC dan aplikasi SAKTI.
*   **Penting:** Nomor kontrak yang direkam harus sesuai dengan **Nomor Pesanan** yang tertera dalam sistem.

# DESIGN.md — Tarbiyah Tracker Design System Specification

> **Version:** 1.0.0  
> **Brand / Design System Name:** Tarbiyah Spirit  
> **Primary Target:** Web Desktop & Responsive Mobile (Laravel 11 + Livewire 3 + Tailwind CSS + Alpine.js)  
> **Status:** Production Ready / Canonical Reference  

---

## 1. Visual Identity & Brand Philosophy

### 1.1 Brand Philosophy
**Tarbiyah Tracker** adalah aplikasi monitoring karakter, ibadah yaumi, dan adab keseharian siswa multi-sekolah (*multi-tenant*). Desainnya menganut prinsip:
1. **Honesty by Architecture:** Poin resmi (*verified points*) secara transparan dipisah dari estimasi poin pending (*estimated points*). Siswa terbiasa jujur dan guru memvalidasi dengan objektif.
2. **Zero-Ambiguity Status:** Setiap status memiliki kode warna, ikon baku, dan microcopy teks yang seragam di seluruh dashboard.
3. **Calm & Dignified Islamic Aesthetic:** Menghadirkan nuansa spiritual kontemporer yang tenang, hangat, rapi, dan berwibawa melalui kombinasi zamrud (*Emerald Green*), keemasan (*Warm Amber*), serta netral (*Slate*).

---

## 2. Design Tokens

### 2.1 Color Palette (Tailwind CSS Mapping)

| Token Name | Hex Code | Tailwind Utility | Semantic Usage |
|---|---|---|---|
| **Primary Brand** | `#059669` | `bg-emerald-600` / `text-emerald-600` | Tombol CTA primer, tab aktif, badge poin, sholat berjamaah |
| **Primary Hover / Dark** | `#047857` | `bg-emerald-700` / `hover:bg-emerald-700` | State hover & active tombol primer |
| **Primary Light Surface**| `#ECFDF5` | `bg-emerald-50` / `border-emerald-200` | Latar kartu checklist aktif, aksen badge |
| **Primary Border** | `#A7F3D0` | `border-emerald-300` | Border state hover & status terverifikasi |
| **Warning / Pending** | `#F59E0B` | `bg-amber-500` / `text-amber-500` | Menunggu verifikasi guru, sholat munfarid, timer |
| **Warning Surface** | `#FFFBEB` | `bg-amber-50` / `border-amber-200` | Box catatan ustadz/guru, banner reminder |
| **Danger / Rejection** | `#E11D48` | `bg-rose-600` / `text-rose-600` | Status ditolak, peringatan terkunci, tolak laporan |
| **Danger Surface** | `#FFF1F2` | `bg-rose-50` / `border-rose-200` | Badge perlu perbaikan, error alert |
| **Neutral Canvas** | `#F8FAFC` | `bg-slate-50` | Background halaman aplikasi utama |
| **Neutral Surface/Card**| `#FFFFFF` | `bg-white` | Kontainer kartu, modal pop-up, form inputs |
| **Neutral Border** | `#E2E8F0` | `border-slate-200` | Border divider & card outline |
| **Text Heading** | `#0F172A` | `text-slate-900` | Judul layar (H1, H2), teks angka metrik display |
| **Text Body** | `#334155` | `text-slate-700` | Teks paragraf, nama kegiatan, isi tabel |
| **Text Secondary** | `#64748B` | `text-slate-500` | Subtitle, helper text, label deskripsi metrik |
| **Text Muted** | `#94A3B8` | `text-slate-400` | NISN, timestamp, placeholder input |
| **Dark High-Contrast** | `#0F172A` | `bg-slate-900` / `text-white` | Floating bulk action toolbar guru |

---

### 2.2 Typography Hierarchy

- **Primary Font Family:** `'Plus Jakarta Sans', system-ui, -apple-system, sans-serif`
- **Fallback Font:** `'Inter', sans-serif`

| Level | Size (Desktop / Mobile) | Weight | Line Height | Tracking | Tailwind Class |
|---|---|---|---|---|---|
| **Display Metric** | 36px / 30px | 800 (ExtraBold) | 1.1 | -0.02em | `text-3xl sm:text-4xl font-extrabold tracking-tight` |
| **Heading 1 (H1)** | 24px / 20px | 800 (ExtraBold) | 1.25 | -0.02em | `text-xl sm:text-2xl font-extrabold text-slate-900` |
| **Heading 2 (H2)** | 18px / 16px | 700 (Bold) | 1.3 | -0.01em | `text-base sm:text-lg font-bold text-slate-900` |
| **Subheading (H3)** | 16px / 15px | 600 (SemiBold) | 1.4 | normal | `text-sm sm:text-base font-semibold text-slate-800` |
| **Body Standard** | 14px / 14px | 400 & 500 (Normal/Medium) | 1.5 | normal | `text-sm text-slate-600` |
| **Caption / Badge** | 12px / 11px | 600 (SemiBold) | 1.4 | +0.01em | `text-xs font-semibold` |
| **Micro / Overline**| 11px / 10px | 700 (Bold) | 1.2 | +0.05em | `text-[11px] uppercase tracking-wider font-bold text-slate-500` |

---

### 2.3 Spacing, Borders, & Elevation System

- **Base Rhythm:** 4px & 8px grid (`gap-2`, `gap-4`, `gap-6`, `p-4`, `p-6`)
- **Border Radius:**
  - `rounded-2xl` (16px) — Kartu utama, section dashboard, kontainer tabel
  - `rounded-xl` (12px) — Input form, tombol aksi, item kartu mutaba'ah
  - `rounded-lg` (8px) — Kotak checkbox indikator, sub-tags
  - `rounded-full` (9999px) — Pill badges, tab navigation, avatar indikator
- **Box Shadow / Elevation:**
  - *Card Default:* `shadow-xs border border-slate-200/80`
  - *Hover Lift:* `hover:shadow-md hover:border-emerald-300 transition-all duration-200`
  - *Floating Toolbar:* `shadow-2xl shadow-slate-900/25`
  - *Modal Sheet:* `shadow-2xl shadow-slate-950/20`

---

## 3. Universal Component Specifications

### 3.1 Status Badge Registry

| Status | Background & Text | Ikon | Label Baku |
|---|---|---|---|
| **Terverifikasi** | `bg-emerald-100 text-emerald-800` | Check circle / double-check | `"Diverifikasi"` |
| **Menunggu Review** | `bg-amber-100 text-amber-800` | Pulse amber dot / clock | `"Perlu Dicek"` / `"Menunggu Guru"` |
| **Perlu Perbaikan**| `bg-rose-100 text-rose-800` | Alert triangle / exclamation | `"Perlu Perbaikan"` |
| **Belum Dilaporkan**| `bg-slate-100 text-slate-500` | Dash / empty dot | `"Belum Dicintang"` |
| **Poin Tag** | `bg-emerald-50 text-emerald-700 border border-emerald-200` | Plus `+` | `"+10 Pts"` / `"+15 Poin"` |

---

### 3.2 Key Reusable Patterns

#### A. Interactive Activity Checklist Card
- **Normal State:** Background putih, border slate-200.
- **Checked State:** Background `bg-emerald-50/50`, border `border-emerald-200`, checkbox terisi `bg-emerald-600` dengan SVG checkmark putih.
- **Locked State:** Atribut `disabled cursor-not-allowed opacity-75 bg-slate-50` ketika waktu input hari berjalan berakhir.
- **Micro-Input:** Input teks atau nominal dengan debounce Livewire 600ms + micro-spinner indikator simpan.

#### B. Floating Bulk Toolbar (Guru Verifier)
- Hanya tampil saat `selectedCount > 0`.
- Tampil *sticky* melayang di bawah layar (`bg-slate-900 text-white rounded-2xl shadow-2xl p-3 sm:px-6`).
- Menyediakan informasi jumlah laporan terpilih, tombol *Batal*, *Tolak dengan Catatan*, dan *Setujui & Terbitkan Poin Sekaligus*.

#### C. Matriks Sholat 5 Waktu Sepekan (Heatmap Wali Murid)
- Format tabel 7 hari (Senin s.d. Ahad) x 5 waktu sholat.
- **Berjamaah:** Hijau zamrud pekat (`bg-emerald-600 text-white`).
- **Munfarid:** Oranye keemasan (`bg-amber-500 text-white`).
- **Udzur Syar'i:** Abu-abu lembut (`bg-slate-200 text-slate-600`).
- **Tidak Dilakukan:** Merah muda (`bg-rose-100 text-rose-700`).

---

## 4. Accessibility (WCAG 2.1 AA) Checklist

1. **Color Contrast:**
   - Slate-900 on White: 15.8:1 (Passes AAA)
   - Emerald-600 on White: 4.6:1 (Passes AA for large text & UI elements)
   - Emerald-800 on Emerald-100: 7.2:1 (Passes AAA)
2. **Keyboard Navigation:**
   - Semua elemen interaktif wajib menyertakan `focus-visible:ring-2 focus-visible:ring-emerald-500 focus-visible:ring-offset-2`.
3. **Screen Readers:**
   - Input checklist menyertakan `role="checkbox"`, `aria-checked`, dan `aria-describedby`.
   - Counter live verifikasi menggunakan `aria-live="polite"`.
4. **Responsive Mobile Strategy:**
   - Tabel konsol guru beralih menjadi kartu vertikal *stacked* pada viewport `< 640px`.
   - Matriks sholat wali murid dilengkapi horizontal scroll snapping yang mulus.

---

## 5. File Download & Export Guide

Dokumen ini disimpan dalam format Markdown standard (`.md`).
- **Nama File Rekomendasi:** `DESIGN.md`
- **Penyimpanan Project:** Taruh file ini di root folder repositori aplikasi Anda (`/DESIGN.md` atau `docs/DESIGN.md`).
- **Integrasi Framework:** Jadikan konfigurasi tema di file ini sebagai acuan pada `tailwind.config.js`.

<?php

use Livewire\Component;

new class extends Component
{
    public string $activeGroup = 'all';
    public bool $submitted = false;

    /** @var array<int, array<string, string|bool>> */
    public array $activities = [
        ['group' => 'ibadah', 'title' => 'Shalat Subuh', 'detail' => 'Berjamaah di masjid', 'points' => '+10 Poin', 'checked' => true],
        ['group' => 'ibadah', 'title' => 'Shalat Dzuhur', 'detail' => 'Berjamaah di sekolah', 'points' => '+10 Poin', 'checked' => true],
        ['group' => 'ibadah', 'title' => 'Tilawah Al-Qur’an', 'detail' => 'Isi jumlah halaman yang dibaca', 'points' => '+5 Poin', 'checked' => true, 'input' => '2 halaman'],
        ['group' => 'ibadah', 'title' => 'Dzikir Pagi', 'detail' => 'Al-Ma’tsurat atau dzikir pagi', 'points' => '+5 Poin', 'checked' => false],
        ['group' => 'akhlak', 'title' => 'Berkata Santun', 'detail' => 'Menjaga ucapan kepada siapa pun', 'points' => '+10 Poin', 'checked' => true],
        ['group' => 'akhlak', 'title' => 'Merapikan Kamar', 'detail' => 'Dilakukan sebelum berangkat sekolah', 'points' => '+5 Poin', 'checked' => true],
        ['group' => 'sosial', 'title' => 'Membantu Orang Tua', 'detail' => 'Membantu pekerjaan rumah', 'points' => '+10 Poin', 'checked' => false],
        ['group' => 'sosial', 'title' => 'Infaq', 'detail' => 'Isi nominal yang diinfaqkan (opsional)', 'points' => '+5 Poin', 'checked' => false, 'input' => 'Rp 0'],
    ];

    public function selectGroup(string $group): void
    {
        $this->activeGroup = $group;
    }

    public function submit(): void
    {
        $this->submitted = true;
    }

    public function getCompletedCountProperty(): int
    {
        return count(array_filter($this->activities, fn (array $activity): bool => $activity['checked']));
    }

    public function getProgressProperty(): int
    {
        return (int) round(($this->completedCount / count($this->activities)) * 100);
    }
};
?>

<div class="app-shell" x-data="{ menuOpen: false }">
    <div class="sidebar-backdrop" :class="{ 'active': menuOpen }" @click="menuOpen = false"></div>
    <aside class="sidebar" :class="{ 'open': menuOpen }">
        <div class="sidebar-header">
            <a class="brand" href="{{ route('dashboard') }}"><span class="brand-mark">✦</span><span><b>AMALIN</b></span></a>
            <button type="button" class="sidebar-close-btn" @click="menuOpen = false" aria-label="Tutup menu">✕</button>
        </div>
        <div class="school"><span class="school-icon">⌂</span><div><b>SDIT Al-Hikmah</b><small>2026/2027 · Ganjil</small></div><span class="swap">⌄</span></div>
        <nav class="nav" aria-label="Navigasi utama"><a class="active" href="#today"><span>▦</span> Beranda</a><a href="#today"><span>✓</span> Mutaba'ah Hari Ini <i>{{ count($activities) - $this->completedCount }}</i></a><a href="#history"><span>◴</span> Riwayat Kegiatan</a><a href="#points"><span>✦</span> Poin & Ranking</a><a href="#reports"><span>▤</span> Laporan</a></nav>
        <div class="sidebar-footer">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()?->name ?? 'NA', 0, 2)) }}</div>
            <div><b>{{ auth()->user()?->name ?? 'Naila Azzahra' }}</b><small>{{ auth()->user()?->email ?? 'Siswa · Kelas 5A' }}</small></div>
            <form method="POST" action="{{ route('logout') }}" style="display: inline; margin: 0;">
                @csrf
                <button type="submit" aria-label="Keluar" title="Keluar" style="cursor: pointer; border: 0; background: transparent; color: #94a3b8; font-size: 18px;">↗</button>
            </form>
        </div>
    </aside>
    <main>
        <header class="topbar"><button class="menu-btn" @click="menuOpen = !menuOpen" aria-label="Buka menu">☰</button><div class="breadcrumb"><span>Beranda</span><strong> / </strong><b>Mutaba'ah Harian</b></div><div class="top-actions"><button class="notification" aria-label="Notifikasi">♧<em></em></button><div class="avatar">NA</div></div></header>
        <div class="page" id="today">
            <section class="welcome"><div><p class="eyebrow">Senin, 21 September 2026 <span>•</span> 8 Rabiul Akhir 1448 H</p><h1>Assalamu'alaikum, Naila <span>🌿</span></h1><p class="subtle">Yuk, isi mutaba'ah hari ini dengan jujur dan penuh semangat.</p></div><div class="deadline"><div class="clock">◷</div><div><small>Batas pengisian hari ini</small><b>07:42:18</b><span>Berakhir pukul 23.59 WIB</span></div></div></section>
            <section class="metrics" id="points"><article class="metric points"><div class="metric-icon">✦</div><div><span>Poin Terverifikasi</span><strong>1.240</strong><small><b>+85</b> poin minggu ini</small></div></article><article class="metric rank"><div class="metric-icon">♛</div><div><span>Ranking Kelas</span><strong>#3 <i>dari 32 siswa</i></strong><small><b>↑ 1</b> posisi dari pekan lalu</small></div></article><article class="metric progress"><div class="metric-ring" style="background: conic-gradient(#059669 {{ $this->progress }}%, #d1fae5 0)"><b>{{ $this->progress }}%</b></div><div><span>Progres Hari Ini</span><strong>{{ $this->completedCount }} <i>dari {{ count($activities) }} kegiatan</i></strong><small>Teruskan, kamu hampir selesai!</small></div></article></section>
            <section class="content-grid"><div class="checklist-card"><div class="section-head"><div><p class="eyebrow">MUTABA'AH HARI INI</p><h2>Checklist Kegiatan</h2></div><div class="save-status" wire:loading>◌ Menyimpan perubahan...</div><div class="save-status" wire:loading.remove>✓ Semua perubahan tersimpan</div></div><div class="tabs">@foreach (['all' => 'Semua', 'ibadah' => 'Ibadah', 'akhlak' => 'Akhlak', 'sosial' => 'Sosial'] as $key => $label)<button class="tab {{ $activeGroup === $key ? 'active' : '' }}" wire:click="selectGroup('{{ $key }}')">{{ $label }} <b>{{ $key === 'all' ? count($activities) : count(array_filter($activities, fn ($item) => $item['group'] === $key)) }}</b></button>@endforeach</div><div class="activity-list">@foreach ($activities as $index => $activity)@if ($activeGroup === 'all' || $activity['group'] === $activeGroup)<label class="activity" wire:key="activity-{{ $index }}"><input class="activity-check" type="checkbox" wire:model.live="activities.{{ $index }}.checked" aria-label="{{ $activity['title'] }}"><span class="activity-info"><strong>{{ $activity['title'] }}</strong><p>{{ $activity['detail'] }}</p></span>@isset($activity['input'])<input class="text-input" wire:model.live.debounce.600ms="activities.{{ $index }}.input" aria-label="Isian {{ $activity['title'] }}">@endisset<span class="point-tag">{{ $activity['points'] }}</span></label>@endif@endforeach</div><button class="finish-btn" wire:click="submit">Selesaikan Mutaba'ah Hari Ini <span>→</span></button>@if ($submitted)<p class="save-status" style="margin-top:12px;text-align:center">✓ Mutaba'ah hari ini telah dikirim untuk diverifikasi guru.</p>@endif</div>
                <aside class="right-rail"><section class="quote-card"><span class="quote-symbol">“</span><p>Amal yang paling dicintai Allah adalah amal yang dilakukan terus-menerus walaupun sedikit.</p><small>HR. Bukhari & Muslim</small></section><section class="teacher-card"><div class="teacher-head"><div class="teacher-avatar">UA</div><div><span>Pesan dari Guru</span><b>Ustadz Ahmad F.</b></div><span class="verify">✓</span></div><p>“Masya Allah, pertahankan kedisiplinan shalat Subuh berjamaahmu ya, Naila!”</p><small>Untuk laporan kemarin · 20 Sep, 14.30</small></section><section class="streak-card"><div><span>🔥</span><p><b>7 hari</b><small>Streak mutaba'ah</small></p></div><div class="week"><i class="on">S</i><i class="on">S</i><i class="on">R</i><i class="on">K</i><i class="on">J</i><i class="on">S</i><i class="today">S</i></div></section></aside></section>
            <section class="bottom-grid" id="history"><article class="history-card"><div class="section-head"><div><p class="eyebrow">7 HARI TERAKHIR</p><h2>Ringkasan Aktivitas</h2></div><a href="#reports">Lihat riwayat →</a></div><div class="chart"><div class="bars"><span style="height:55%"><i>70</i></span><span style="height:72%"><i>90</i></span><span style="height:62%"><i>80</i></span><span style="height:88%"><i>110</i></span><span style="height:75%"><i>95</i></span><span style="height:90%"><i>115</i></span><span class="today-bar" style="height:48%"><i>60</i></span></div><div class="days"><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Hari ini</span></div></div></article><article class="leaderboard"><div class="section-head"><div><p class="eyebrow">KELAS 5A · SEMESTER GANJIL</p><h2>Ranking Teratas</h2></div><a href="#points">Lihat semua →</a></div><ol><li><b class="medal gold">1</b><span class="tiny-avatar violet">RA</span><strong>Rafa Alfarizi</strong><em>1.420 pts</em></li><li><b class="medal silver">2</b><span class="tiny-avatar orange">KA</span><strong>Khadijah Aulia</strong><em>1.325 pts</em></li><li class="me"><b class="medal bronze">3</b><span class="tiny-avatar green">NA</span><strong>Naila Azzahra <small>Kamu</small></strong><em>1.240 pts</em></li></ol></article></section>
        </div>
    </main>
</div>

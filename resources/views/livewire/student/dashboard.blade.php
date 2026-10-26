<div>
    <section class="welcome">
        <div>
            <p class="eyebrow">{{ now()->translatedFormat('l, d F Y') }} <span>•</span> SIPENDEKARKU</p>
            <h1>Assalamu'alaikum, {{ auth()->user()->name }} <span>🌿</span></h1>
            <p class="subtle">Yuk, isi mutaba'ah hari ini dengan jujur, istiqomah, dan penuh semangat.</p>
        </div>
        <div class="deadline">
            <div class="clock">◷</div>
            <div>
                <small>Batas pengisian hari ini</small>
                <b>23:59 WIB</b>
                <span>Otomatis terkunci saat berganti tanggal</span>
            </div>
        </div>
    </section>

    <!-- Metrics Cards -->
    <section class="metrics" id="points">
        <article class="metric points">
            <div class="metric-icon">✦</div>
            <div>
                <span>Poin Terverifikasi</span>
                <strong>{{ number_format($verifiedPoints, 0, ',', '.') }}</strong>
                <small><b>Semester Ini</b></small>
            </div>
        </article>

        <article class="metric rank">
            <div class="metric-icon">♛</div>
            <div>
                <span>Ranking Kelas</span>
                <strong>#{{ $classRank }} <i>dari {{ $totalClassStudents }} siswa</i></strong>
                <small>Kelas {{ auth()->user()->currentClass()?->name ?? '5A' }}</small>
            </div>
        </article>

        <article class="metric progress">
            <div class="metric-ring" style="background: conic-gradient(var(--emerald) {{ $this->progress }}%, var(--emerald-border) 0)">
                <b>{{ $this->progress }}%</b>
            </div>
            <div>
                <span>Progres Hari Ini</span>
                <strong><i>{{ $this->completedCount }}</i> dari {{ count($activities) }} kegiatan</strong>
                <small>{{ $this->progress === 100 ? 'Alhamdulillah, semua terisi!' : 'Teruskan, kamu hampir selesai!' }}</small>
            </div>
        </article>
    </section>

    <!-- Main Content Grid -->
    <section class="content-grid">
        <div class="checklist-card">
            <div class="section-head">
                <div>
                    <p class="eyebrow">MUTABA'AH HARI INI</p>
                    <h2>Checklist & Isian Kegiatan</h2>
                </div>
                <div class="save-status" wire:loading>◌ Menyimpan otomatis...</div>
                <div class="save-status" wire:loading.remove>✓ Tersimpan di sistem</div>
            </div>

            <!-- Category Tabs -->
            <div class="tabs" role="tablist">
                <button type="button" class="tab {{ $activeGroup === 'all' ? 'active' : '' }}" wire:click="selectGroup('all')">
                    Semua <b>{{ count($activities) }}</b>
                </button>
                @foreach ($groups as $grp)
                    @php
                        $grpSlug = strtolower($grp->name);
                        $countGrp = count(array_filter($activities, fn($a) => $a['group_name'] === $grpSlug));
                    @endphp
                    <button type="button" class="tab {{ $activeGroup === $grpSlug ? 'active' : '' }}" wire:click="selectGroup('{{ $grpSlug }}')">
                        {{ $grp->name }} <b>{{ $countGrp }}</b>
                    </button>
                @endforeach
            </div>

            <!-- Activity Items List -->
            <div class="activity-list">
                @forelse ($activities as $index => $activity)
                    @if ($activeGroup === 'all' || $activity['group_name'] === $activeGroup)
                        <div class="activity {{ $activity['checked'] ? 'checked' : '' }}" wire:key="act-{{ $activity['item_id'] }}">
                            @if ($activity['input_type'] === 'checklist')
                                <input 
                                    class="activity-check" 
                                    type="checkbox" 
                                    {{ $activity['checked'] ? 'checked' : '' }}
                                    wire:click="toggleCheck({{ $index }})"
                                    id="act-check-{{ $index }}"
                                    aria-label="{{ $activity['title'] }}"
                                >
                            @else
                                <span class="metric-icon" style="width:24px;height:24px;font-size:12px;background:#e0e7ff;color:#4338ca;border-radius:6px;">✎</span>
                            @endif

                            <div class="activity-info">
                                <label for="act-check-{{ $index }}" style="cursor:pointer;font-weight:700;color:var(--dark);">
                                    {{ $activity['title'] }}
                                </label>
                                <p>{{ $activity['detail'] }}</p>
                            </div>

                            @if ($activity['input_type'] === 'text')
                                <input 
                                    type="text" 
                                    class="text-input" 
                                    placeholder="Isi capaian (misal: 2 lembar / Rp 5.000)"
                                    value="{{ $activity['value'] }}"
                                    wire:change="updateTextValue({{ $index }}, $event.target.value)"
                                    aria-label="Isian {{ $activity['title'] }}"
                                >
                            @endif

                            <span class="point-tag">+{{ $activity['point_weight'] }} Poin</span>
                        </div>
                    @endif
                @empty
                    <div style="padding: 30px; text-align: center; color: var(--slate-400);">
                        Belum ada kegiatan yang dikonfigurasi untuk saat ini.
                    </div>
                @endforelse
            </div>

            <button type="button" class="finish-btn" wire:click="submitMutabaah">
                Selesaikan Mutaba'ah Hari Ini <span>→</span>
            </button>

            @if ($submitted)
                <div class="alert-box alert-success" style="margin-top: 16px;">
                    ✓ Mutaba'ah hari ini telah tersimpan dan siap diverifikasi oleh Ustadz/Ustadzah pengampu.
                </div>
            @endif
        </div>

        <!-- Right Rail -->
        <aside class="right-rail">
            <section class="quote-card">
                <span class="quote-symbol">“</span>
                <p>Amal yang paling dicintai Allah adalah amal yang dilakukan secara terus-menerus (istiqomah) walaupun sedikit.</p>
                <small>HR. Bukhari & Muslim</small>
            </section>

            @if ($latestTeacherNote)
                <section class="teacher-card">
                    <div class="teacher-head">
                        <div class="teacher-avatar">UA</div>
                        <div>
                            <span>Pesan Verifikasi</span>
                            <b>{{ $latestTeacherName }}</b>
                        </div>
                        <span class="verify">✓</span>
                    </div>
                    <p>“{{ $latestTeacherNote }}”</p>
                    <small>{{ $latestTeacherDate ? 'Diverifikasi pada ' . $latestTeacherDate : 'Laporan terverifikasi' }}</small>
                </section>
            @endif

            <section class="streak-card">
                <div>
                    <span>🔥</span>
                    <p>
                        <b>{{ $streakDays }} hari</b>
                        <small>Streak Mutaba'ah</small>
                    </p>
                </div>
                <div class="week">
                    @foreach ($recentWeeklyProgress as $dayItem)
                        <i class="{{ $dayItem['percent'] >= 50 ? 'on' : '' }} {{ $dayItem['is_today'] ? 'today' : '' }}">
                            {{ substr($dayItem['day'], 0, 1) }}
                        </i>
                    @endforeach
                </div>
            </section>
        </aside>
    </section>

    <!-- Bottom History & Leaderboard -->
    <section class="bottom-grid" id="history">
        <article class="history-card">
            <div class="section-head">
                <div>
                    <p class="eyebrow">7 HARI TERAKHIR</p>
                    <h2>Ringkasan Aktivitas</h2>
                </div>
                <a href="{{ route('student.history') }}">Lihat riwayat lengkap →</a>
            </div>
            <div class="chart">
                <div class="bars">
                    @foreach ($recentWeeklyProgress as $bar)
                        <span style="height: {{ max(15, $bar['percent']) }}%" class="{{ $bar['is_today'] ? 'today-bar' : '' }}">
                            <i>{{ $bar['percent'] }}%</i>
                        </span>
                    @endforeach
                </div>
                <div class="days">
                    @foreach ($recentWeeklyProgress as $bar)
                        <span>{{ $bar['day'] }}</span>
                    @endforeach
                </div>
            </div>
        </article>

        <article class="leaderboard">
            <div class="section-head">
                <div>
                    <p class="eyebrow">LEADERBOARD KELAS</p>
                    <h2>Peringkat Teratas</h2>
                </div>
                <a href="{{ route('student.leaderboard') }}">Lihat semua →</a>
            </div>
            <ol>
                @forelse ($topLeaderboard as $studentRow)
                    <li class="{{ $studentRow['is_me'] ? 'me' : '' }}">
                        <b class="medal {{ $studentRow['rank'] === 1 ? 'gold' : ($studentRow['rank'] === 2 ? 'silver' : 'bronze') }}">
                            {{ $studentRow['rank'] }}
                        </b>
                        <span class="tiny-avatar {{ $studentRow['rank'] === 1 ? 'violet' : ($studentRow['rank'] === 2 ? 'orange' : 'green') }}">
                            {{ $studentRow['initials'] }}
                        </span>
                        <strong>
                            {{ $studentRow['name'] }}
                            @if ($studentRow['is_me'])
                                <small>Kamu</small>
                            @endif
                        </strong>
                        <em>{{ number_format($studentRow['points'], 0, ',', '.') }} pts</em>
                    </li>
                @empty
                    <li style="justify-content:center;color:var(--slate-400);font-size:12px;">Belum ada skor poin kelas.</li>
                @endforelse
            </ol>
        </article>
    </section>
</div>

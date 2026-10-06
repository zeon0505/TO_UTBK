<div>
    <!-- ══════════════ TOKEN MODAL ══════════════ -->
    @if($showTokenModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.85); backdrop-filter: blur(5px);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-shield-lock-fill me-2"></i>Verifikasi Token UTS</h5>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <span class="badge bg-primary-subtle text-primary fw-bold fs-6 mb-2">{{ $exam->course->code ?? 'MK' }} - {{ $exam->course->name ?? 'Mata Kuliah' }}</span>
                        <h4 class="fw-bold mb-1 text-dark">{{ $exam->title }}</h4>
                        <p class="text-muted small">Durasi: <strong>{{ $exam->duration_minutes }} Menit</strong> | Dosen: <strong>{{ $exam->lecturer->name ?? 'Dosen Pengampu' }}</strong></p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label fw-bold text-dark text-uppercase small">Kode Token Akses UTS</label>
                        <input type="text" class="form-control form-control-lg text-center font-monospace fw-bold text-uppercase border-primary"
                               wire:model="tokenInput" placeholder="Contoh: UTS123" autofocus>
                        @if($tokenError)
                            <small class="text-danger mt-1 d-block fw-semibold">{{ $tokenError }}</small>
                        @endif
                    </div>
                    <button type="button" class="btn btn-primary btn-lg w-100 fw-bold rounded-3 shadow-sm" wire:click="verifyToken">
                        <i class="bi bi-play-circle-fill me-2"></i>Mulai Kerjakan UTS Sekarang
                    </button>
                    <div class="text-center mt-3">
                        <a href="/dashboard" class="text-muted text-decoration-none small"><i class="bi bi-arrow-left me-1"></i> Batal &amp; Kembali ke Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ══════════════ FINISHED VIEW ══════════════ -->
    @elseif($isFinished)
    <div class="row justify-content-center py-5">
        <div class="col-md-6 text-center">
            <div class="card border-0 shadow-lg p-5 rounded-4">
                @if($violationsCount >= 3)
                    <i class="bi bi-shield-slash-fill text-danger display-1 mb-3"></i>
                    <h3 class="fw-bold text-danger mb-2">UTS Dihentikan Otomatis!</h3>
                    <p class="text-muted mb-4">Sistem mendeteksi <strong>3 kali pelanggaran keamanan</strong> (Pindah Tab / Screenshot / Kombinasi Tombol Dilarang). Lembar jawaban Anda telah dikumpulkan secara otomatis oleh sistem.</p>
                @else
                    <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
                    <h3 class="fw-bold text-dark mb-2">UTS Berhasil Dikumpulkan!</h3>
                    <p class="text-muted mb-4">Jawaban Anda untuk UTS <strong>{{ $exam->title }}</strong> telah berhasil tersimpan di sistem.</p>
                @endif
                <a href="/exam/{{ $exam->id }}/result" class="btn btn-primary btn-lg fw-bold rounded-3 shadow-sm">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>Lihat Rincian Jawaban &amp; Nilai
                </a>
            </div>
        </div>
    </div>

    <!-- ══════════════ ACTIVE EXAM VIEW ══════════════ -->
    @else

    <!-- Top bar -->
    <div class="bg-dark text-white p-3 rounded-3 shadow-sm mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <span class="badge bg-primary text-white mb-1">{{ $exam->course->code ?? 'MK' }} - {{ $exam->course->name ?? 'Mata Kuliah' }}</span>
                <h5 class="fw-bold text-white mb-0">{{ $exam->title }}</h5>
            </div>
            <div class="col-md-6 text-md-end mt-2 mt-md-0 d-flex align-items-center justify-content-md-end gap-3">
                <!-- Security Badge -->
                <div class="text-center bg-danger px-3 rounded-3 me-1" style="padding-top:6px;padding-bottom:6px;">
                    <small class="text-white-50 d-block text-uppercase" style="font-size:10px;"><i class="bi bi-shield-exclamation me-1"></i>Keamanan</small>
                    <span id="violation-badge" class="font-monospace fw-bold fs-6 text-white">{{ $violationsCount }} / 3 Pelanggaran</span>
                </div>
                <!-- Timer Badge -->
                <div class="text-center bg-secondary px-3 rounded-3" style="padding-top:6px;padding-bottom:6px;">
                    <small class="text-white-50 d-block text-uppercase" style="font-size:10px;"><i class="bi bi-clock-history me-1"></i>Sisa Waktu UTS</small>
                    <span id="exam-timer" class="font-monospace fw-bold fs-5 text-warning">--:--:--</span>
                </div>
                <button class="btn btn-danger fw-bold"
                    onclick="confirm('Yakin ingin mengumpulkan lembar jawaban UTS sekarang?') || event.stopImmediatePropagation()"
                    wire:click="finishExam">
                    <i class="bi bi-send-fill me-1"></i> Selesaikan UTS
                </button>
            </div>
        </div>
    </div>

    <!-- ══ Security Warning Modal (pure JS, no Bootstrap) ══ -->
    <div id="securityWarningModal"
         style="display:none; position:fixed; inset:0; z-index:99999;
                background:rgba(0,0,0,0.92); align-items:center; justify-content:center;
                backdrop-filter:blur(6px);">
        <div style="max-width:460px; width:92%;">
            <div class="card border-danger shadow-lg rounded-4 overflow-hidden" style="border-width:3px!important;">
                <div class="card-header bg-danger text-white text-center py-3 border-0">
                    <h5 class="fw-bold mb-0">⚠️ PERINGATAN KEAMANAN UJIAN</h5>
                </div>
                <div class="card-body p-4 text-center">
                    <div id="alarmIcon" style="font-size:4rem; display:inline-block;">🚨</div>
                    <h4 class="fw-bold text-danger mt-2 mb-2">Tindakan Mencurigakan Terdeteksi!</h4>
                    <p class="text-dark mb-3">
                        Sistem mendeteksi Anda mencoba <strong id="violationReason">melakukan pelanggaran</strong>!
                    </p>
                    <div class="alert alert-danger fw-bold fs-5 py-2 mb-3">
                        ⛔ Pelanggaran Ke-<span id="modalViolationCount">0</span> dari 3
                    </div>
                    <p class="text-muted small mb-4">
                        Jika melanggar <strong>3 kali</strong>, ujian akan
                        <strong class="text-danger">OTOMATIS DIKUMPULKAN</strong>!
                    </p>
                    <button id="modalDismissBtn" type="button"
                            class="btn btn-danger btn-lg w-100 fw-bold rounded-3"
                            onclick="window.__examCloseModal()">
                        <i class="bi bi-check-circle me-1"></i> Saya Mengerti &amp; Lanjutkan Ujian
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes alarmPulse {
            0%   { transform: scale(1) rotate(0deg); }
            25%  { transform: scale(1.2) rotate(-10deg); }
            50%  { transform: scale(1) rotate(10deg); }
            75%  { transform: scale(1.2) rotate(-5deg); }
            100% { transform: scale(1) rotate(0deg); }
        }
        .alarm-shake { animation: alarmPulse 0.5s ease infinite; }
    </style>

    <!-- ══════════ Question Area ══════════ -->
    <div class="row g-4">
        <div class="col-lg-8 col-xl-9">
            @if($currentQuestion)
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <span class="badge bg-dark fs-6 me-2">Soal No. {{ $currentQuestionIndex + 1 }} dari {{ count($questions) }}</span>
                        @if($currentQuestion->type === 'multiple_choice')
                            <span class="badge bg-primary">Pilihan Ganda</span>
                        @else
                            <span class="badge bg-warning text-dark">Essay / Uraian</span>
                        @endif
                    </div>
                    <span class="badge bg-success fs-6">Bobot: {{ number_format($currentQuestion->weight, 1) }} Poin</span>
                </div>
                <div class="card-body p-4" unselectable="on" onselectstart="return false;" oncopy="return false;" oncut="return false;">
                    <div class="fs-5 text-dark fw-semibold mb-4" style="white-space:pre-line; user-select:none;">{{ $currentQuestion->question_text }}</div>

                    @if($currentQuestion->type === 'multiple_choice')
                    <div class="d-flex flex-column gap-3 mb-4" style="user-select:none;">
                        @foreach($currentQuestion->options as $oIdx => $opt)
                        <div class="p-3 border rounded-3 {{ $selectedOptionId == $opt->id ? 'border-primary bg-primary-subtle text-primary fw-bold shadow-sm' : 'bg-light text-dark' }}"
                             wire:click="selectOption({{ $opt->id }})" style="cursor:pointer;">
                            <div class="d-flex align-items-center">
                                <span class="badge {{ $selectedOptionId == $opt->id ? 'bg-primary' : 'bg-secondary' }} font-monospace fs-6 me-3">{{ chr(65 + $oIdx) }}</span>
                                <span class="fs-6">{{ $opt->option_text }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2"><i class="bi bi-pencil-square me-1"></i> Ketikkan Jawaban Uraian / Essay Anda di Sini:</label>
                        <textarea class="form-control border-primary shadow-sm" wire:model.blur="essayAnswer" rows="8"
                                  placeholder="Tuliskan penjelasan lengkap, langkah-langkah, atau kode program sesuai instruksi soal..."></textarea>
                    </div>
                    @endif

                    <div class="form-check mb-4 bg-light p-3 rounded-3 border">
                        <input class="form-check-input" type="checkbox" wire:model.live="isDoubtful" id="doubtCheck">
                        <label class="form-check-label fw-bold text-warning" for="doubtCheck">
                            <i class="bi bi-flag-fill me-1"></i> Tandai Ragu-Ragu untuk soal ini
                        </label>
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                        <button class="btn btn-outline-secondary fw-bold px-4" wire:click="previousQuestion" @if($currentQuestionIndex == 0) disabled @endif>
                            <i class="bi bi-chevron-left me-1"></i> Sebelumnya
                        </button>
                        @if($currentQuestionIndex < count($questions) - 1)
                        <button class="btn btn-primary fw-bold px-4" wire:click="nextQuestion">
                            Selanjutnya <i class="bi bi-chevron-right ms-1"></i>
                        </button>
                        @else
                        <button class="btn btn-success fw-bold px-4"
                                onclick="confirm('Ini adalah soal terakhir. Selesaikan UTS sekarang?') || event.stopImmediatePropagation()"
                                wire:click="finishExam">
                            <i class="bi bi-check-all me-1"></i> Kumpulkan Ujian
                        </button>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Question Grid Nav -->
        <div class="col-lg-4 col-xl-3">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="card-title mb-0 fw-bold"><i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i>Navigasi Nomor Soal</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        @foreach($questions as $qIdx => $qItem)
                        @php
                            $isCurrent = ($currentQuestionIndex == $qIdx);
                            $isDoubt   = isset($userAnswers[$qItem->id]) && $userAnswers[$qItem->id] == true;
                        @endphp
                        <button type="button"
                                class="btn btn-sm fw-bold rounded-3 {{ $isCurrent ? 'btn-primary' : ($isDoubt ? 'btn-warning text-dark' : 'btn-outline-secondary') }}"
                                style="width:44px;height:44px;"
                                wire:click="goToQuestion({{ $qIdx }})">
                            {{ $qIdx + 1 }}
                        </button>
                        @endforeach
                    </div>
                    <div class="p-3 bg-light rounded-3 small">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary" style="width:14px;height:14px;display:inline-block;"></span><span>Soal Sedang Aktif</span>
                        </div>
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-warning" style="width:14px;height:14px;display:inline-block;"></span><span>Ditandai Ragu-Ragu</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge border" style="width:14px;height:14px;display:inline-block;"></span><span>Belum Dikerjakan</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        // Guard: only boot once per exam (survives Livewire re-renders)
        var BOOT_KEY = '__examBoot_' + '{{ $exam->id }}';
        if (window[BOOT_KEY]) return;
        window[BOOT_KEY] = true;

        var EXAM_KEY      = 'exam_dl_' + '{{ $exam->id }}' + '_' + '{{ auth()->id() }}';
        var MAX_VIOLATIONS = 3;
        window.__examV    = Number('{{ (int)($violationsCount ?? 0) }}');
        window.__examEnd  = false;
        var lastViol      = 0;  // debounce timestamp

        // ── AUDIO ──────────────────────────────────────────────────
        var __ac = null;
        function getAC() {
            if (!__ac) {
                try { __ac = new (window.AudioContext || window.webkitAudioContext)(); } catch(e){}
            }
            return __ac;
        }
        // Unlock audio context on any user interaction
        ['click','keydown','touchstart'].forEach(function(ev) {
            document.addEventListener(ev, function() {
                var c = getAC();
                if (c && c.state === 'suspended') c.resume();
            }, { passive: true });
        });

        function playAlarm() {
            var c = getAC(); if (!c) return;
            function beeps() {
                [[880,0.00],[880,0.22],[1320,0.45]].forEach(function(p) {
                    try {
                        var o = c.createOscillator(), g = c.createGain();
                        o.connect(g); g.connect(c.destination);
                        o.type = 'sawtooth'; o.frequency.value = p[0];
                        g.gain.setValueAtTime(1.3, c.currentTime + p[1]);
                        g.gain.exponentialRampToValueAtTime(0.001, c.currentTime + p[1] + 0.28);
                        o.start(c.currentTime + p[1]);
                        o.stop(c.currentTime  + p[1] + 0.32);
                    } catch(e){}
                });
            }
            c.state === 'suspended' ? c.resume().then(beeps) : beeps();
        }

        // ── TIMER ──────────────────────────────────────────────────
        function getDeadline() {
            var serverSecs = Number('{{ (int)($timeLeftSeconds ?? 0) }}');
            var s = localStorage.getItem(EXAM_KEY);
            if (s) {
                var storedDl = parseInt(s, 10);
                var rem = Math.floor((storedDl - Date.now()) / 1000);
                // If stored deadline is expired/ended but server says we still have time (> 10s),
                // the exam was reset by Admin! Clear stale localStorage deadline.
                if (rem <= 0 && serverSecs > 10) {
                    localStorage.removeItem(EXAM_KEY);
                    s = null;
                }
            }
            if (!s) {
                if (serverSecs <= 0) return Date.now();
                var dl = Date.now() + serverSecs * 1000;
                localStorage.setItem(EXAM_KEY, String(dl));
                return dl;
            }
            return parseInt(s, 10);
        }
        function clearDL() { localStorage.removeItem(EXAM_KEY); }

        function tick() {
            if (window.__examEnd) return;
            var el  = document.getElementById('exam-timer');
            var rem = Math.floor((getDeadline() - Date.now()) / 1000);
            if (rem <= 0) {
                if (el) { el.innerText = '00:00:00'; el.style.color = '#ff4d4d'; }
                clearDL(); doEnd(); return;
            }
            var h  = String(Math.floor(rem / 3600)).padStart(2,'0');
            var mi = String(Math.floor((rem % 3600) / 60)).padStart(2,'0');
            var sc = String(rem % 60).padStart(2,'0');
            if (el) { el.innerText = h+':'+mi+':'+sc; el.style.color = rem <= 300 ? '#ff4d4d' : '#ffc107'; }
        }
        tick();
        setInterval(tick, 1000);

        // ── END EXAM ───────────────────────────────────────────────
        function doEnd() {
            if (window.__examEnd) return;
            window.__examEnd = true;
            clearDL();
            $wire.call('finishExam');
        }

        // ── MODAL ──────────────────────────────────────────────────
        function showModal(reason, count) {
            var m   = document.getElementById('securityWarningModal');
            var mc  = document.getElementById('modalViolationCount');
            var mr  = document.getElementById('violationReason');
            var b   = document.getElementById('violation-badge');
            var ico = document.getElementById('alarmIcon');
            var btn = document.getElementById('modalDismissBtn');
            if (mc)  mc.innerText  = count;
            if (mr)  mr.innerText  = reason;
            if (b)   b.innerText   = count + ' / 3 Pelanggaran';
            if (ico) ico.className = 'alarm-shake';
            if (m)   m.style.display = 'flex';
            if (count >= MAX_VIOLATIONS && btn) {
                btn.disabled  = true;
                btn.innerText = '⛔ Ujian dihentikan otomatis...';
            }
        }
        window.__examCloseModal = window.closeSecurityModal = function() {
            var m   = document.getElementById('securityWarningModal');
            var ico = document.getElementById('alarmIcon');
            if (m)   m.style.display = 'none';
            if (ico) ico.className   = '';
        };

        // ── VIOLATION ──────────────────────────────────────────────
        function triggerViol(reason) {
            if (window.__examEnd) return;
            var now = Date.now();
            if (now - lastViol < 2000) return;   // debounce 2s
            lastViol = now;

            window.__examV++;
            var cnt = window.__examV;
            console.warn('[UJIAN SECURITY] Pelanggaran #'+cnt+': '+reason);

            playAlarm();
            $wire.call('recordViolation');
            showModal(reason, cnt);

            if (cnt >= MAX_VIOLATIONS) { setTimeout(doEnd, 3000); }
        }

        // ── LISTENERS ──────────────────────────────────────────────

        // 1. Tab switch / app switch (semua browser termasuk mobile)
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) triggerViol('berpindah tab / meminimalkan browser');
        });

        // 1b. pagehide — lebih reliable di iOS Safari saat keluar halaman
        window.addEventListener('pagehide', function(e) {
            if (!window.__examEnd) triggerViol('meninggalkan / menutup halaman ujian');
        });

        // 1c. Page Lifecycle API — saat browser "freeze" tab (Android/Desktop Chrome)
        window.addEventListener('freeze', function() {
            if (!window.__examEnd) triggerViol('browser membekukan halaman (pindah app)');
        });

        // 2. Focus lost to other app
        window.addEventListener('blur', function() {
            if (document.hidden) return;
            triggerViol('meninggalkan jendela ujian (Alt+Tab / klik luar)');
        });

        // 3. Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (window.__examEnd) return;
            if (e.key==='PrintScreen'||e.code==='PrintScreen') {
                e.preventDefault(); triggerViol('screenshot (PrintScreen)'); return;
            }
            if (e.shiftKey&&(e.metaKey||e.ctrlKey)&&'sS'.includes(e.key)) {
                e.preventDefault(); triggerViol('screenshot (Snipping Tool)'); return;
            }
            if (e.key==='F12') { e.preventDefault(); triggerViol('DevTools F12'); return; }
            if (e.ctrlKey&&e.shiftKey&&'ijcIJC'.includes(e.key)) {
                e.preventDefault(); triggerViol('DevTools Ctrl+Shift+'+e.key); return;
            }
            if (e.ctrlKey&&'uU'.includes(e.key)) {
                e.preventDefault(); triggerViol('View Source Ctrl+U'); return;
            }
            if ((e.ctrlKey||e.metaKey)&&'pP'.includes(e.key)) {
                e.preventDefault(); triggerViol('Print Ctrl+P'); return;
            }
            // Block volume down keys
            if (e.code==='VolumeDown' || e.key==='AudioVolumeDown' || e.key==='VolumeDown') {
                e.preventDefault(); return;
            }
        });

        // 3b. Block volume down on keyup too
        document.addEventListener('keyup', function(e) {
            if (e.code==='VolumeDown' || e.key==='AudioVolumeDown' || e.key==='VolumeDown') {
                e.preventDefault();
            }
        });

        // 3c. Lock all media elements to volume 1.0 and watch for changes
        function lockMediaVolume() {
            document.querySelectorAll('audio, video').forEach(function(el) {
                if (el.volume < 1) el.volume = 1.0;
                el.muted = false;
            });
        }
        lockMediaVolume();
        setInterval(lockMediaVolume, 1000);

        // Observe new media elements added to DOM
        var _volObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                m.addedNodes.forEach(function(node) {
                    if (node.nodeName === 'AUDIO' || node.nodeName === 'VIDEO') {
                        node.volume = 1.0;
                        node.muted = false;
                    }
                });
            });
            lockMediaVolume();
        });
        _volObserver.observe(document.body, { childList: true, subtree: true });


        // 4. Block copy/cut/paste/right-click/drag/text-selection
        document.addEventListener('copy',        function(e){ e.preventDefault(); triggerViol('mencoba menyalin teks (Copy)'); });
        document.addEventListener('cut',         function(e){ e.preventDefault(); triggerViol('mencoba memotong teks (Cut)'); });
        document.addEventListener('paste',       function(e){ e.preventDefault(); triggerViol('mencoba menempelkan teks (Paste / Copas)'); });
        document.addEventListener('contextmenu', function(e){ e.preventDefault(); });
        document.addEventListener('dragstart',   function(e){ e.preventDefault(); });
        document.addEventListener('selectstart', function(e){
            if (!['INPUT','TEXTAREA'].includes(e.target.tagName)) e.preventDefault();
        });

        // 5. DevTools & Window Unmaximize / Shrink / Split Screen inspection
        function checkWindowSize() {
            if (window.__examEnd) return;

            // Check DevTools side dock
            var widthDiff = window.outerWidth - window.innerWidth;
            var heightDiff = window.outerHeight - window.innerHeight;
            if (widthDiff > 200 || heightDiff > 200) {
                triggerViol('membuka Developer Tools / Inspector');
                return;
            }

            // Check if window is unmaximized, resized down, or in split screen
            var screenAvailW = window.screen.availWidth || window.screen.width;
            var screenAvailH = window.screen.availHeight || window.screen.height;

            if (screenAvailW && screenAvailH) {
                if (window.outerWidth < screenAvailW - 80 || window.outerHeight < screenAvailH - 120) {
                    triggerViol('mengecilkan / mengubah ukuran jendela browser (Harus Layar Maksimal)');
                }
            }
        }

        window.addEventListener('resize', checkWindowSize);
        setInterval(checkWindowSize, 1500);

        // 6. Fullscreen enforcement & exit monitoring
        function requestExamFullscreen() {
            var elem = document.documentElement;
            if (elem.requestFullscreen) { elem.requestFullscreen().catch(function(){}); }
            else if (elem.webkitRequestFullscreen) { elem.webkitRequestFullscreen(); }
        }
        document.addEventListener('click', function _fsOnce() {
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                requestExamFullscreen();
            }
        }, { once: true });

        document.addEventListener('fullscreenchange', function() {
            if (window.__examEnd) return;
            if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                triggerViol('keluar dari mode Layar Penuh (Fullscreen)');
            }
        });
        document.addEventListener('webkitfullscreenchange', function() {
            if (window.__examEnd) return;
            if (!document.webkitFullscreenElement && !document.fullscreenElement) {
                triggerViol('keluar dari mode Layar Penuh (Fullscreen)');
            }
        });

        // 7. Back button trap
        history.pushState({examActive:true}, '', window.location.href);
        window.addEventListener('popstate', function() {
            if (window.__examEnd) return;
            history.pushState({examActive:true}, '', window.location.href);
            var n = document.getElementById('__backNotice');
            if (!n) {
                n = document.createElement('div'); n.id = '__backNotice';
                n.style.cssText = 'position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:100000;background:#1a1a2e;color:#fff;padding:12px 24px;border-radius:10px;font-weight:700;font-size:15px;border:2px solid #e74c3c;box-shadow:0 4px 20px rgba(0,0,0,.6);transition:opacity .4s;pointer-events:none';
                document.body.appendChild(n);
            }
            n.innerText = '🚫 Tidak bisa kembali saat ujian berlangsung!';
            n.style.opacity = '1';
            clearTimeout(n._t);
            n._t = setTimeout(function(){ n.style.opacity='0'; }, 2500);
        });

        // 8. Before unload
        window.addEventListener('beforeunload', function(e) {
            if (window.__examEnd) return;
            e.preventDefault();
            e.returnValue = 'Ujian masih berlangsung!';
        });

        console.log('[UJIAN] Engine perketat aktif. Violations: '+window.__examV+', TimeLeft: '+Math.floor((getDeadline()-Date.now())/1000)+'s');
    </script>
    @endscript

    @endif
</div>

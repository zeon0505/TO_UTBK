<div x-data="{ 
    sectionTimeLeft: 0,
    questionTimeLeft: 0,
    sectionTimer: null,
    questionTimer: null,
    violationCount: 0,
    maxViolations: 3,
    warningPlayed: false,
    showScratchpad: false,
    isDrawing: false,
    ctx: null,
    playSFX(type) {
        setTimeout(() => {
            const sounds = {
                start: 'https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3',
                click: 'https://assets.mixkit.co/active_storage/sfx/2571/2571-preview.mp3',
                warning: 'https://assets.mixkit.co/active_storage/sfx/1003/1003-preview.mp3',
                finish: 'https://assets.mixkit.co/active_storage/sfx/1435/1435-preview.mp3'
            };
            try {
                const audio = new Audio(sounds[type]);
                audio.play().catch(() => {});
            } catch (e) {}
        }, 0);
    },
    initCanvas() {
        const canvas = this.$refs.scratchCanvas;
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        this.ctx = canvas.getContext('2d');
        this.ctx.lineWidth = 2;
        this.ctx.lineCap = 'round';
        this.ctx.strokeStyle = '#2d3436';
    },
    startDrawing(e) {
        this.isDrawing = true;
        this.draw(e);
    },
    stopDrawing() {
        this.isDrawing = false;
        this.ctx.beginPath();
    },
    draw(e) {
        if (!this.isDrawing) return;
        const rect = this.$refs.scratchCanvas.getBoundingClientRect();
        const x = (e.clientX || (e.touches ? e.touches[0].clientX : 0)) - rect.left;
        const y = (e.clientY || (e.touches ? e.touches[0].clientY : 0)) - rect.top;
        this.ctx.lineTo(x, y);
        this.ctx.stroke();
        this.ctx.beginPath();
        this.ctx.moveTo(x, y);
    },
    clearCanvas() {
        this.ctx.clearRect(0, 0, this.$refs.scratchCanvas.width, this.$refs.scratchCanvas.height);
    },
    startSectionTimer(duration) {
        this.sectionTimeLeft = duration;
        if (this.sectionTimer) clearInterval(this.sectionTimer);
        this.sectionTimer = setInterval(() => {
            if (this.sectionTimeLeft > 0) {
                this.sectionTimeLeft--;
            } else {
                clearInterval(this.sectionTimer);
                clearInterval(this.questionTimer);
                Swal.fire({
                    icon: 'warning',
                    title: 'Waktu Bab Habis!',
                    text: 'Sistem akan memindahkan Anda ke sub-tes berikutnya.',
                    timer: 3000,
                    showConfirmButton: false
                }).then(() => {
                    $wire.moveToNextSection();
                });
            }
        }, 1000);
    },
    startQuestionTimer(duration) {
        this.questionTimeLeft = duration;
        if (this.questionTimer) clearInterval(this.questionTimer);
        this.questionTimer = setInterval(() => {
            if (this.questionTimeLeft > 1) {
                this.questionTimeLeft--;
            } else {
                clearInterval(this.questionTimer);
                this.playSFX('warning');
                $wire.nextQuestion();
            }
        }, 1000);
    },
    initAntiCheat() {
        // Anti-Double Tab
        const channel = new BroadcastChannel('exam_channel');
        channel.postMessage({ type: 'NEW_TAB', examId: '{{ $exam->id }}' });
        channel.onmessage = (e) => {
            if (e.data.type === 'NEW_TAB' && e.data.examId === '{{ $exam->id }}') {
                Swal.fire({
                    icon: 'error',
                    title: 'Akses Ditolak!',
                    text: 'Anda sudah membuka halaman ujian ini di tab lain. Silakan gunakan satu tab saja.',
                    confirmButtonText: 'Tutup Tab Ini'
                }).then(() => {
                    window.close();
                });
            }
        };

        // Hapus listener lama jika ada (mencegah duplikasi)
        if (window.cheatHandler) {
            window.removeEventListener('blur', window.cheatHandler);
        }

        // Simpan handler ke variabel global agar bisa dihapus nanti
        window.cheatHandler = () => {
            if (this.showInstructions || this.isFinished) return;
            
            this.violationCount++;
            this.playSFX('warning');

            if (this.violationCount >= this.maxViolations) {
                Swal.fire({
                    icon: 'error',
                    title: 'Pelanggaran Kritikal!',
                    text: 'Anda terlalu sering meninggalkan halaman. Ujian dikumpulkan otomatis.',
                    confirmButtonText: 'OK'
                }).then(() => {
                    this.stopAntiCheat();
                    $wire.finishExam();
                });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Peringatan Keamanan',
                    html: 'Dilarang pindah tab atau meninggalkan halaman ujian!<br>Pelanggaran dicatat: <b>' + this.violationCount + '/' + this.maxViolations + '</b>',
                    confirmButtonText: 'Saya Mengerti',
                    confirmButtonColor: '#435ebe',
                });
            }
        };

        window.addEventListener('blur', window.cheatHandler);
        
        // Disable Right Click & Copy
        document.oncontextmenu = () => !this.isFinished;
        document.oncopy = () => !this.isFinished;
    },
    stopAntiCheat() {
        if (window.cheatHandler) {
            window.removeEventListener('blur', window.cheatHandler);
            window.cheatHandler = null;
        }
        document.oncontextmenu = null;
        document.oncopy = null;
    }
}
" 
x-on:start-section-timer.window="startSectionTimer($event.detail.duration); initAntiCheat()"
x-on:question-loaded.window="startQuestionTimer($event.detail.duration)"
x-on:play-sfx.window="playSFX($event.detail.type)"
class="container-fluid no-select">

<style>
    .no-select {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }
</style>

    @if($showInstructions)
    <div class="row justify-content-center py-5 text-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-5">
                    <div class="stats-icon purple mx-auto mb-4" style="width: 80px; height: 80px;">
                        <i class="bi bi-info-circle fs-1"></i>
                    </div>
                    <h2 class="fw-bold mb-3">Instruksi Simulasi UTBK</h2>
                    <p class="text-muted mb-4">Anda akan mengerjakan <strong>{{ $exam->title }}</strong>. Silakan baca aturan pengerjaan di bawah ini:</p>
                    
                    <div class="row text-start mb-5 bg-light p-4 rounded-4">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Pengerjaan per Soal.</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Waktu berjalan <b>60 Detik</b> per Soal.</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Akan <b>otomatis pindah</b> jika waktu habis.</li>
                                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i> Skor akhir dihitung otomatis.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-info py-3 rounded-4 mb-4">
                        Materi Uji: <strong>{{ $exam->subTests[0]->title }}</strong>
                    </div>

                    <button class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg" wire:click="startExam">
                        Mulai Simulasi Sekarang
                    </button>
                </div>
            </div>
        </div>
    </div>

    @elseif($isFinished)
    <div class="row justify-content-center py-5 text-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 p-5 rounded-5">
                <div class="stats-icon green mx-auto mb-4" style="width: 100px; height: 100px;">
                    <i class="bi bi-check-all fs-1"></i>
                </div>
                <h1 class="fw-bold">Selamat!</h1>
                <p class="text-muted fs-5">Anda telah menyelesaikan seluruh rangkaian simulasi.</p>
                
                <div class="my-4">
                    <h4 class="text-muted mb-1">Skor Akhir Anda</h4>
                    <h1 class="display-3 text-primary fw-extrabold">{{ number_format($result->total_score, 2) }}</h1>
                </div>

                <div class="d-flex gap-2 justify-content-center">
                    <a href="/tryouts" class="btn btn-secondary rounded-pill px-4" @click="stopAntiCheat()" wire:navigate>Lihat Riwayat</a>
                    <a href="/dashboard" class="btn btn-primary rounded-pill px-4" @click="stopAntiCheat()" wire:navigate>Ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    @else
    <!-- Exam Layout -->
    <div class="page-heading">
        <div class="row align-items-center">
            <div class="col-md-4">
                <h4 class="mb-0 fw-bold">{{ $exam->title }}</h4>
                <p class="text-muted small mb-0">{{ $currentSubTest->title }}</p>
            </div>
            <div class="col-md-4 text-center d-flex justify-content-center">
                <div class="d-flex align-items-center bg-white shadow-sm rounded-pill p-1 border">
                    <!-- Timer Bab -->
                    <div class="px-3 border-end d-flex align-items-center">
                        <div class="me-2 text-start">
                            <div class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">SISA WAKTU BAB</div>
                            <div class="fs-5 fw-extrabold text-primary" style="font-family: 'Courier New', monospace; line-height: 1;">
                                <span x-text="Math.floor(sectionTimeLeft / 60).toString().padStart(2, '0')"></span>:<span x-text="(sectionTimeLeft % 60).toString().padStart(2, '0')"></span>
                            </div>
                        </div>
                        <i class="bi bi-clock-history fs-4 text-primary opacity-50"></i>
                    </div>
                    <!-- Timer Soal -->
                    <div class="px-3 d-flex align-items-center">
                        <i class="bi bi-hourglass-split fs-4 text-warning opacity-75 me-2"></i>
                        <div class="text-start">
                            <div class="text-muted fw-bold" style="font-size: 0.55rem; letter-spacing: 0.5px;">WAKTU SOAL</div>
                            <div class="fs-5 fw-extrabold text-warning" style="font-family: 'Courier New', monospace; line-height: 1;">
                                <span x-text="questionTimeLeft"></span><span style="font-size: 0.7rem">s</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-danger btn-sm rounded-pill px-3" onclick="confirm('Kumpulkan sekarang?') ? @this.finishExam() : null">
                    Kumpulkan
                </button>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <!-- Question Area -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm min-vh-50">
                <div class="card-header bg-transparent d-flex justify-content-between align-items-center p-4">
                    <h4 class="mb-0 fw-bold">Soal No. {{ $currentQuestionIndex + 1 }}</h4>
                    <span class="badge bg-primary px-3 rounded-pill">{{ $currentQuestion->type }}</span>
                </div>
                <div class="card-body p-4">
                    <div class="question-text fs-5 mb-5 lh-base">
                        {!! $currentQuestion->text !!}
                    </div>

                    <div class="options space-y-3">
                        @foreach($currentQuestion->options as $index => $option)
                            <div class="form-check option-glass border @if($selectedOptionId == $option->id) border-primary @endif" 
                                 x-on:click="playSFX('click'); $wire.set('selectedOptionId', {{ $option->id }})">
                                <input class="form-check-input mt-1" type="radio" name="option" id="option{{ $option->id }}" 
                                       value="{{ $option->id }}" wire:model.live="selectedOptionId">
                                <label class="form-check-label ms-2 d-block w-100 fs-6" for="option{{ $option->id }}" style="cursor: pointer;">
                                    <span class="fw-bold me-2">{{ chr(65 + $index) }}.</span> {{ $option->text }}
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-5 border-top pt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="doubtfulCheck" wire:click="toggleDoubtful" @if($isDoubtful) checked @endif>
                            <label class="form-check-label fw-bold @if($isDoubtful) text-warning @endif" for="doubtfulCheck">
                                <i class="bi bi-question-circle me-1"></i> Tandai Ragu-ragu
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 d-flex justify-content-between p-4">
                    <button class="btn btn-secondary px-4 {{ $currentQuestionIndex == 0 ? 'invisible' : '' }}" x-on:click="playSFX('click'); $wire.previousQuestion()">
                        <i class="bi bi-arrow-left me-2"></i> Sebelumnya
                    </button>
                    
                    <button class="btn btn-primary px-5 shadow-sm" x-on:click="playSFX('click'); $wire.nextQuestion()">
                        {{ $currentQuestionIndex == count($questions) - 1 ? 'Selesai / Materi Berikutnya' : 'Lanjut' }} <i class="bi bi-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Navigation -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent p-4">
                    <h4 class="mb-0 fw-bold">Navigasi Soal</h4>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-2">
                        @php 
                            $answers = \App\Models\UserAnswer::where('result_id', $result->id)->get()->keyBy('question_id');
                        @endphp
                        @foreach($questions as $index => $q)
                            @php
                                $ans = $answers->get($q->id);
                                $btnClass = 'btn-outline-secondary';
                                if ($currentQuestionIndex == $index) {
                                    $btnClass = 'btn-primary';
                                } elseif ($ans) {
                                    $btnClass = $ans->is_doubtful ? 'btn-warning text-white' : 'btn-outline-primary active';
                                }
                            @endphp
                            <button wire:click="goToQuestion({{ $index }})" 
                                    class="btn rounded-4 d-flex align-items-center justify-content-center p-0 {{ $btnClass }}"
                                    style="width: 42px; height: 42px; font-weight: 700;">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>

                    <div class="mt-4 pt-3 border-top small text-muted">
                        <div class="d-flex align-items-center mb-2">
                            <span class="btn btn-primary btn-sm rounded-3 me-2" style="width: 20px; height: 20px;"></span> Posisi Sekarang
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="btn btn-outline-primary active btn-sm rounded-3 me-2" style="width: 20px; height: 20px;"></span> Terjawab (Yakin)
                        </div>
                        <div class="d-flex align-items-center">
                            <span class="btn btn-warning btn-sm rounded-3 me-2" style="width: 20px; height: 20px;"></span> Ragu-ragu
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subject Progress -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body p-4">
                    <h6 class="text-muted small text-uppercase mb-3">Informasi Bagian</h6>
                    @foreach($exam->subTests as $idx => $st)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small @if($idx == $currentSubTestIndex) fw-bold text-primary @elseif($idx < $currentSubTestIndex) text-success @else text-muted @endif">
                                {{ $st->title }}
                            </span>
                            @if($idx < $currentSubTestIndex)
                                <i class="bi bi-check-circle-fill text-success small"></i>
                            @elseif($idx == $currentSubTestIndex)
                                <span class="badge bg-primary rounded-pill x-small" style="font-size: 0.6rem;">SEDANG DIKERJAKAN</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Floating Scratchpad Toggle -->
    @if(!$showInstructions && !$isFinished)
    <div class="position-fixed bottom-0 end-0 m-4 shadow-lg rounded-circle" style="z-index: 2000;">
        <button class="btn btn-primary rounded-circle p-3 d-flex align-items-center justify-content-center" 
                @click="showScratchpad = !showScratchpad; if(showScratchpad) $nextTick(() => initCanvas())"
                title="Coret-coret">
            <i class="bi bi-pencil-fill fs-4" x-show="!showScratchpad"></i>
            <i class="bi bi-x-lg fs-4" x-show="showScratchpad"></i>
        </button>
    </div>
    @endif

    <!-- Canvas Scratchpad Overlay -->
    <div x-show="showScratchpad" 
         x-transition.opacity
         class="position-fixed top-0 start-0 w-100 h-100" 
         style="z-index: 1000; background: rgba(255, 255, 255, 0.1); backdrop-filter: none;">
        
        <div class="d-flex justify-content-center gap-3 p-3 bg-white shadow-sm position-absolute top-0 start-50 translate-middle-x rounded-bottom-4 border">
            <h6 class="mb-0 fw-bold me-3 align-self-center"><i class="bi bi-brush me-2"></i> Papan Coret</h6>
            <button class="btn btn-sm btn-outline-danger px-3 py-1" @click="clearCanvas()">
                <i class="bi bi-eraser-fill me-1"></i> Hapus Semua
            </button>
            <button class="btn btn-sm btn-secondary px-3 py-1" @click="showScratchpad = false">
                <i class="bi bi-check2 me-1"></i> Selesai
            </button>
            <div class="ms-3 border-start ps-3 small text-muted align-self-center d-none d-md-block">
                Gunakan mouse/jari untuk mencoret
            </div>
        </div>

        <canvas x-ref="scratchCanvas"
                @mousedown="startDrawing"
                @mousemove="draw"
                @mouseup="stopDrawing"
                @mouseleave="stopDrawing"
                @touchstart="startDrawing"
                @touchmove="draw"
                @touchend="stopDrawing"
                class="w-100 h-100 cursor-crosshair"
                style="cursor: crosshair;">
        </canvas>
    </div>
</div>

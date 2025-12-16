<section class="py-5 position-relative overflow-hidden" style="min-height: 100vh; background: radial-gradient(circle at top, #e9f6ff 0%, #f5f7fb 45%, #ffffff 100%);">
    <div class="position-absolute top-0 end-0 pe-5 pt-4 opacity-75" style="z-index: 0;">
        <span class="badge bg-gradient-info text-white px-4 py-2 rounded-pill shadow">Hyoo</span>
    </div>
    <div class="position-absolute" style="bottom: -120px; left: -120px; width: 320px; height: 320px; background: linear-gradient(160deg, rgba(44,154,219,0.15), rgba(55,224,205,0.1)); filter: blur(20px); border-radius: 40%;"></div>

    <div class="container py-4 position-relative" style="z-index: 1;">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <div class="text-uppercase text-sm fw-semibold text-info mb-2">Platform Pemerintah · Gen Z Ready</div>
                <h1 class="display-5 fw-bold text-dark mb-3">Satu dashboard untuk seluruh agenda DJSN</h1>
                <p class="text-secondary pe-lg-5 mb-4">Schedulo menghadirkan pengalaman manajemen aktivitas yang segar, adaptif, dan cepat-dirancang untuk generasi penggerak birokrasi modern. Kolaborasi lintas divisi terasa senatural percakapan di ruang kerja digital.</p>

                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div class="p-3 rounded-4 shadow-sm bg-white h-100">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2 rounded-circle bg-gradient-info text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="ni ni-time-alarm text-white"></i>
                                </div>
                                <h6 class="mb-0 text-dark">Auto Reminder</h6>
                            </div>
                            <p class="text-secondary small mb-0">Push notif ke email & WhatsApp dengan nada resmi tapi santai.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 rounded-4 shadow-sm bg-white h-100">
                            <div class="d-flex align-items-center mb-2">
                                <div class="me-2 rounded-circle bg-gradient-success text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                    <i class="ni ni-spaceship text-white"></i>
                                </div>
                                <h6 class="mb-0 text-dark">Fast Track</h6>
                            </div>
                            <p class="text-secondary small mb-0">Lacak progres disposisi secara real-time dan kolaboratif.</p>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-3">
                    <div class="d-flex align-items-center px-3 py-2 rounded-pill bg-white shadow-sm border border-light-subtle">
                        <span class="badge bg-gradient-info me-2 rounded-pill">24/7</span>
                        <small class="text-secondary">Smart Scheduler</small>
                    </div>
                    <div class="d-flex align-items-center px-3 py-2 rounded-pill bg-white shadow-sm border border-light-subtle">
                        <span class="badge bg-gradient-success me-2 rounded-pill">ISO</span>
                        <small class="text-secondary">Compliance Ready</small>
                    </div>
                    <div class="d-flex align-items-center px-3 py-2 rounded-pill bg-white shadow-sm border border-light-subtle">
                        <span class="badge bg-dark text-white me-2 rounded-pill">AI</span>
                        <small class="text-secondary">Context-aware Insights</small>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 ms-auto">
                <div class="card border-0 shadow-xl rounded-5 overflow-hidden">
                    <div class="card-body p-4 p-md-5">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div>
                                <p class="text-uppercase text-xs text-secondary mb-1">{{ __('Supported by') }}</p>
                                <img src="{{ asset('assets/img/logo.svg') }}" alt="DJSN" height="56" class="rounded-logo">
                            </div>
                            <span class="badge bg-gradient-info text-white rounded-pill px-3 py-2">Schedulo DJSN</span>
                        </div>

                        <div class="mb-4">
                            <h4 class="fw-bold mb-1 text-dark">Masuk ke ruang kerja</h4>
                            <p class="text-secondary mb-0">Gunakan kredensial resmi untuk menjaga keamanan data lembaga.</p>
                        </div>

                        <form wire:submit="login" class="text-start">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold text-dark">{{ __('Email Dinas') }}</label>
                                <div class="input-group input-group-outline rounded-4 @error('email') border border-danger @else border border-light-subtle @enderror">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="ni ni-email-83 text-info"></i>
                                    </span>
                                    <input wire:model.live="email" id="email" type="email" class="form-control border-0"
                                        placeholder="admin@softui.com" aria-label="Email" aria-describedby="email-addon">
                                </div>
                                @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold text-dark">{{ __('Password') }}</label>
                                <div class="input-group input-group-outline rounded-4 @error('password') border border-danger @else border border-light-subtle @enderror">
                                    <span class="input-group-text bg-transparent border-0">
                                        <i class="ni ni-lock-circle-open text-info"></i>
                                    </span>
                                    <input wire:model.live="password" id="password" type="{{ $showPassword ? 'text' : 'password' }}" class="form-control border-0"
                                        placeholder="••••••••" aria-label="Password" aria-describedby="password-addon">
                                </div>
                                @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check">
                                    <input wire:model.live="showPassword" class="form-check-input" type="checkbox" id="togglePassword">
                                    <label class="form-check-label text-secondary" for="togglePassword">{{ __('Tampilkan Password') }}</label>
                                </div>
                            </div>

                            <button type="submit" class="btn bg-gradient-info w-100 py-3 rounded-4 shadow-sm text-white fw-semibold">
                                {{ __('Sign in') }}
                            </button>
                        </form>

                        <div class="mt-4 pt-3 border-top border-light-subtle">
                            <div class="d-flex gap-3 align-items-center">
                                <div class="rounded-circle bg-info-subtle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                    <i class="ni ni-single-copy-04 text-info"></i>
                                </div>
                                <div>
                                    <p class="fw-semibold mb-0 text-dark">Agenda hari ini</p>
                                    <small class="text-secondary">4 rapat, 2 disposisi, 1 kunjungan lapangan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
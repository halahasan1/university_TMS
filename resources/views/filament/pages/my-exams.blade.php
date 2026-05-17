<x-filament-panels::page>
    @php
        $exams = $this->getExams();
        $now = now();

        $availableExams = $exams->filter(function ($exam) use ($now) {
            $attempt = $exam->attempts->first();

            if ($attempt && $attempt->finished_at) {
                return false;
            }

            return (! $exam->start_time || $now->gte($exam->start_time))
                && (! $exam->end_time || $now->lte($exam->end_time));
        });

        $upcomingExams = $exams->filter(function ($exam) use ($now) {
            $attempt = $exam->attempts->first();

            return ! ($attempt && $attempt->finished_at)
                && $exam->start_time
                && $now->lt($exam->start_time);
        });

        $completedExams = $exams->filter(function ($exam) {
            $attempt = $exam->attempts->first();

            return $attempt && $attempt->finished_at;
        });

        $expiredExams = $exams->filter(function ($exam) use ($now) {
            $attempt = $exam->attempts->first();

            return ! ($attempt && $attempt->finished_at)
                && $exam->end_time
                && $now->gt($exam->end_time);
        });
    @endphp

    <style>
        .exams-page {
            --primary: #d97706;
            --primary-dark: #92400e;
            --primary-soft: #fff7ed;
            --success: #059669;
            --success-soft: #ecfdf5;
            --blue: #2563eb;
            --blue-soft: #eff6ff;
            --danger: #dc2626;
            --danger-soft: #fef2f2;
            --purple: #7c3aed;
            --purple-soft: #f5f3ff;
            --ink: #111827;
            --muted: #6b7280;
            --line: #e5e7eb;
        }

        .exams-shell {
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .hero-card {
            position: relative;
            overflow: hidden;
            border-radius: 30px;
            padding: 34px;
            background:
                radial-gradient(circle at 10% 20%, rgba(251, 191, 36, 0.22), transparent 32%),
                radial-gradient(circle at 85% 10%, rgba(249, 115, 22, 0.16), transparent 28%),
                linear-gradient(135deg, #ffffff 0%, #fff7ed 48%, #fffbeb 100%);
            border: 1px solid rgba(251, 146, 60, 0.22);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .hero-card::after {
            content: "";
            position: absolute;
            right: -90px;
            top: -90px;
            width: 250px;
            height: 250px;
            border-radius: 999px;
            background: rgba(251, 146, 60, 0.16);
        }

        .hero-content {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: space-between;
            gap: 24px;
            align-items: center;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.75);
            color: var(--primary-dark);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            box-shadow: 0 8px 18px rgba(15, 23, 42, 0.06);
        }

        .hero-title {
            margin-top: 14px;
            font-size: clamp(30px, 4vw, 48px);
            line-height: 1;
            font-weight: 900;
            color: var(--ink);
            letter-spacing: -0.04em;
        }

        .hero-subtitle {
            margin-top: 14px;
            max-width: 680px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.7;
        }

        .hero-time {
            min-width: 220px;
            border-radius: 24px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(18px);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
            text-align: right;
        }

        .hero-time-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .hero-time-value {
            margin-top: 6px;
            font-size: 26px;
            font-weight: 900;
            color: var(--ink);
        }

        .hero-date {
            margin-top: 2px;
            font-size: 13px;
            color: var(--muted);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            padding: 22px;
            background: #ffffff;
            border: 1px solid var(--line);
            box-shadow: 0 18px 36px rgba(15, 23, 42, 0.055);
            transition: 0.22s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 26px 50px rgba(15, 23, 42, 0.08);
        }

        .stat-card::before {
            content: "";
            position: absolute;
            inset: 0 auto 0 0;
            width: 5px;
            background: var(--accent);
        }

        .stat-label {
            color: var(--muted);
            font-size: 13px;
            font-weight: 800;
        }

        .stat-number {
            margin-top: 12px;
            font-size: 36px;
            line-height: 1;
            font-weight: 950;
            color: var(--ink);
            letter-spacing: -0.04em;
        }

        .stat-hint {
            margin-top: 8px;
            font-size: 12px;
            color: var(--muted);
        }

        .section-block {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .section-header {
            display: flex;
            align-items: end;
            justify-content: space-between;
            gap: 16px;
        }

        .section-title {
            font-size: 22px;
            font-weight: 900;
            color: var(--ink);
            letter-spacing: -0.03em;
        }

        .section-desc {
            margin-top: 4px;
            color: var(--muted);
            font-size: 14px;
        }

        .count-pill {
            border-radius: 999px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 850;
            background: #ffffff;
            border: 1px solid var(--line);
            color: var(--ink);
            box-shadow: 0 10px 22px rgba(15, 23, 42, 0.05);
        }

        .exam-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .exam-card {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 22px;
            background: rgba(255, 255, 255, 0.94);
            border: 1px solid rgba(229, 231, 235, 0.95);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.07);
            transition: 0.22s ease;
        }

        .exam-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 28px 65px rgba(15, 23, 42, 0.12);
        }

        .exam-card.available {
            background:
                linear-gradient(135deg, rgba(236, 253, 245, 0.95), rgba(255, 255, 255, 0.98));
            border-color: rgba(16, 185, 129, 0.22);
        }

        .exam-card.upcoming {
            background:
                linear-gradient(135deg, rgba(239, 246, 255, 0.95), rgba(255, 255, 255, 0.98));
            border-color: rgba(59, 130, 246, 0.20);
        }

        .exam-card.completed {
            background:
                linear-gradient(135deg, rgba(245, 243, 255, 0.95), rgba(255, 255, 255, 0.98));
            border-color: rgba(124, 58, 237, 0.18);
        }

        .exam-card.expired {
            background:
                linear-gradient(135deg, rgba(254, 242, 242, 0.95), rgba(255, 255, 255, 0.98));
            border-color: rgba(239, 68, 68, 0.16);
        }

        .exam-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
        }

        .exam-icon {
            width: 54px;
            height: 54px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            border-radius: 20px;
            background: #ffffff;
            border: 1px solid rgba(229, 231, 235, 0.9);
            box-shadow: 0 12px 24px rgba(15, 23, 42, 0.07);
            font-size: 24px;
        }

        .exam-main {
            min-width: 0;
            flex: 1;
        }

        .exam-title-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .exam-title {
            color: var(--ink);
            font-size: 18px;
            font-weight: 900;
            line-height: 1.25;
            letter-spacing: -0.02em;
        }

        .type-badge {
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 11px;
            font-weight: 850;
            background: rgba(255, 255, 255, 0.88);
            border: 1px solid rgba(229, 231, 235, 0.95);
            color: var(--muted);
        }

        .status-badge {
            border-radius: 999px;
            padding: 7px 11px;
            font-size: 12px;
            font-weight: 900;
            white-space: nowrap;
        }

        .status-available {
            color: #047857;
            background: #d1fae5;
        }

        .status-upcoming {
            color: #1d4ed8;
            background: #dbeafe;
        }

        .status-completed {
            color: #6d28d9;
            background: #ede9fe;
        }

        .status-expired {
            color: #b91c1c;
            background: #fee2e2;
        }

        .exam-course {
            margin-top: 8px;
            color: #374151;
            font-size: 14px;
            font-weight: 700;
        }

        .exam-description {
            margin-top: 9px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.65;
        }

        .time-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 18px;
        }

        .time-box {
            border-radius: 18px;
            padding: 12px;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(229, 231, 235, 0.85);
        }

        .time-label {
            display: block;
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .time-value {
            display: block;
            margin-top: 4px;
            color: var(--ink);
            font-size: 13px;
            font-weight: 850;
        }

        .exam-footer {
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .score-box {
            border-radius: 16px;
            padding: 10px 13px;
            background: #ffffff;
            border: 1px solid rgba(229, 231, 235, 0.9);
            color: var(--ink);
            font-size: 13px;
            font-weight: 900;
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
        }

        .btn-exam {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            border-radius: 16px;
            padding: 10px 16px;
            font-size: 13px;
            font-weight: 950;
            text-decoration: none;
            transition: 0.2s ease;
            border: 0;
        }

        .btn-start {
            color: #ffffff;
            background: linear-gradient(135deg, #f97316, #d97706);
            box-shadow: 0 14px 26px rgba(217, 119, 6, 0.25);
        }

        .btn-start:hover {
            transform: translateY(-1px);
            filter: brightness(0.98);
        }

        .btn-view {
            color: #374151;
            background: #ffffff;
            border: 1px solid rgba(209, 213, 219, 0.95);
            box-shadow: 0 10px 20px rgba(15, 23, 42, 0.05);
        }

        .btn-disabled {
            color: #6b7280;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(209, 213, 219, 0.95);
            cursor: not-allowed;
        }

        .empty-card {
            border-radius: 28px;
            padding: 36px;
            text-align: center;
            background:
                linear-gradient(135deg, #ffffff, #f9fafb);
            border: 1px dashed #d1d5db;
            color: var(--muted);
        }

        .empty-title {
            color: var(--ink);
            font-weight: 900;
            font-size: 16px;
        }

        .empty-text {
            margin-top: 6px;
            font-size: 13px;
        }

        .tabs-wrap {
            border-radius: 30px;
            background: #ffffff;
            border: 1px solid var(--line);
            box-shadow: 0 20px 45px rgba(15, 23, 42, 0.065);
            overflow: hidden;
        }

        .tabs-header {
            display: flex;
            gap: 10px;
            padding: 14px;
            background: #f9fafb;
            border-bottom: 1px solid var(--line);
            overflow-x: auto;
        }

        .tab-btn {
            border: 0;
            border-radius: 999px;
            padding: 11px 16px;
            font-size: 13px;
            font-weight: 950;
            color: #4b5563;
            background: transparent;
            white-space: nowrap;
            transition: 0.2s ease;
        }

        .tab-btn:hover {
            background: #ffffff;
        }

        .tab-btn.active {
            color: #ffffff;
            background: linear-gradient(135deg, #f97316, #d97706);
            box-shadow: 0 14px 26px rgba(217, 119, 6, 0.20);
        }

        .tab-body {
            padding: 22px;
        }

        @media (max-width: 1100px) {
            .stats-grid,
            .exam-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .hero-content {
                flex-direction: column;
                align-items: stretch;
            }

            .hero-time {
                text-align: left;
            }

            .stats-grid,
            .exam-grid,
            .time-row {
                grid-template-columns: 1fr;
            }

            .exam-top,
            .exam-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .status-badge,
            .btn-exam {
                width: 100%;
                text-align: center;
            }
        }
    </style>

    <div class="exams-page exams-shell" x-data="{ tab: 'current' }">

        {{-- Hero --}}
        <div class="hero-card">
            <div class="hero-content">
                <div>
                    <div class="eyebrow">
                        <span>Exam Portal</span>
                    </div>

                    <h1 class="hero-title">
                        My Exams
                    </h1>

                    <p class="hero-subtitle">
                        Track your current exams, upcoming schedules, submitted attempts, and closed exams from one clean student dashboard.
                    </p>
                </div>

                <div class="hero-time">
                    <div class="hero-time-label">
                        Current Time
                    </div>

                    <div class="hero-time-value">
                        {{ now()->format('h:i A') }}
                    </div>

                    <div class="hero-date">
                        {{ now()->format('l, M d Y') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-card" style="--accent: #059669;">
                <div class="stat-label">Available Now</div>
                <div class="stat-number">{{ $availableExams->count() }}</div>
                <div class="stat-hint">Ready to start</div>
            </div>

            <div class="stat-card" style="--accent: #2563eb;">
                <div class="stat-label">Upcoming</div>
                <div class="stat-number">{{ $upcomingExams->count() }}</div>
                <div class="stat-hint">Scheduled later</div>
            </div>

            <div class="stat-card" style="--accent: #7c3aed;">
                <div class="stat-label">Submitted</div>
                <div class="stat-number">{{ $completedExams->count() }}</div>
                <div class="stat-hint">Completed attempts</div>
            </div>

            <div class="stat-card" style="--accent: #dc2626;">
                <div class="stat-label">Closed</div>
                <div class="stat-number">{{ $expiredExams->count() }}</div>
                <div class="stat-hint">Missed or expired</div>
            </div>
        </div>

        {{-- Main Tabs --}}
        <div class="tabs-wrap">
            <div class="tabs-header">
                <button
                    type="button"
                    class="tab-btn"
                    x-bind:class="tab === 'current' ? 'active' : ''"
                    x-on:click="tab = 'current'"
                >
                    Current Exams
                </button>

                <button
                    type="button"
                    class="tab-btn"
                    x-bind:class="tab === 'upcoming' ? 'active' : ''"
                    x-on:click="tab = 'upcoming'"
                >
                    Upcoming
                </button>

                <button
                    type="button"
                    class="tab-btn"
                    x-bind:class="tab === 'completed' ? 'active' : ''"
                    x-on:click="tab = 'completed'"
                >
                    Submitted Results
                </button>

                <button
                    type="button"
                    class="tab-btn"
                    x-bind:class="tab === 'closed' ? 'active' : ''"
                    x-on:click="tab = 'closed'"
                >
                    Closed Exams
                </button>
            </div>

            <div class="tab-body">

                {{-- Current --}}
                <div x-show="tab === 'current'" class="section-block">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Available Now</h2>
                            <p class="section-desc">These exams are currently open for submission.</p>
                        </div>

                        <div class="count-pill">
                            {{ $availableExams->count() }} available
                        </div>
                    </div>

                    <div class="exam-grid">
                        @forelse ($availableExams as $exam)
                            <div class="exam-card available">
                                <div class="exam-top">
                                    <div class="exam-icon">📝</div>

                                    <div class="exam-main">
                                        <div class="exam-title-row">
                                            <h3 class="exam-title">{{ $exam->title }}</h3>

                                            @if ($exam->is_practice)
                                                <span class="type-badge">Practice</span>
                                            @else
                                                <span class="type-badge">Official</span>
                                            @endif
                                        </div>

                                        <div class="exam-course">
                                            {{ $exam->course?->name }}
                                        </div>

                                        @if ($exam->description)
                                            <div class="exam-description">
                                                {{ $exam->description }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="status-badge status-available">
                                        Open
                                    </div>
                                </div>

                                <div class="time-row">
                                    <div class="time-box">
                                        <span class="time-label">Start</span>
                                        <span class="time-value">
                                            {{ $exam->start_time ? $exam->start_time->format('M d, h:i A') : 'Any time' }}
                                        </span>
                                    </div>

                                    <div class="time-box">
                                        <span class="time-label">End</span>
                                        <span class="time-value">
                                            {{ $exam->end_time ? $exam->end_time->format('M d, h:i A') : 'No deadline' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="exam-footer">
                                    <div class="score-box">
                                        Status: Ready
                                    </div>

                                    <a
                                        href="{{ \App\Filament\Resources\ExamResource\Pages\TakeExam::getUrl(['exam' => $exam->id]) }}"
                                        class="btn-exam btn-start"
                                    >
                                        Start Exam
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="empty-card" style="grid-column: 1 / -1;">
                                <div class="empty-title">No exams available right now.</div>
                                <div class="empty-text">When an exam becomes active, it will appear here.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Upcoming --}}
                <div x-show="tab === 'upcoming'" class="section-block">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Upcoming Exams</h2>
                            <p class="section-desc">These exams are scheduled and will open at their start time.</p>
                        </div>

                        <div class="count-pill">
                            {{ $upcomingExams->count() }} upcoming
                        </div>
                    </div>

                    <div class="exam-grid">
                        @forelse ($upcomingExams as $exam)
                            <div class="exam-card upcoming">
                                <div class="exam-top">
                                    <div class="exam-icon">⏳</div>

                                    <div class="exam-main">
                                        <div class="exam-title-row">
                                            <h3 class="exam-title">{{ $exam->title }}</h3>

                                            @if ($exam->is_practice)
                                                <span class="type-badge">Practice</span>
                                            @else
                                                <span class="type-badge">Official</span>
                                            @endif
                                        </div>

                                        <div class="exam-course">
                                            {{ $exam->course?->name }}
                                        </div>

                                        @if ($exam->description)
                                            <div class="exam-description">
                                                {{ $exam->description }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="status-badge status-upcoming">
                                        Upcoming
                                    </div>
                                </div>

                                <div class="time-row">
                                    <div class="time-box">
                                        <span class="time-label">Starts</span>
                                        <span class="time-value">
                                            {{ $exam->start_time?->format('M d, h:i A') }}
                                        </span>
                                    </div>

                                    <div class="time-box">
                                        <span class="time-label">Ends</span>
                                        <span class="time-value">
                                            {{ $exam->end_time ? $exam->end_time->format('M d, h:i A') : 'No deadline' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="exam-footer">
                                    <div class="score-box">
                                        Opens later
                                    </div>

                                    <button type="button" class="btn-exam btn-disabled" disabled>
                                        Not Started Yet
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-card" style="grid-column: 1 / -1;">
                                <div class="empty-title">No upcoming exams.</div>
                                <div class="empty-text">Scheduled exams will appear here.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Completed --}}
                <div x-show="tab === 'completed'" class="section-block">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Submitted Results</h2>
                            <p class="section-desc">Your submitted attempts and scores.</p>
                        </div>

                        <div class="count-pill">
                            {{ $completedExams->count() }} submitted
                        </div>
                    </div>

                    <div class="exam-grid">
                        @forelse ($completedExams as $exam)
                            @php
                                $attempt = $exam->attempts->first();
                            @endphp

                            <div class="exam-card completed">
                                <div class="exam-top">
                                    <div class="exam-icon">✅</div>

                                    <div class="exam-main">
                                        <div class="exam-title-row">
                                            <h3 class="exam-title">{{ $exam->title }}</h3>

                                            @if ($exam->is_practice)
                                                <span class="type-badge">Practice</span>
                                            @else
                                                <span class="type-badge">Official</span>
                                            @endif
                                        </div>

                                        <div class="exam-course">
                                            {{ $exam->course?->name }}
                                        </div>

                                        @if ($exam->description)
                                            <div class="exam-description">
                                                {{ $exam->description }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="status-badge status-completed">
                                        Submitted
                                    </div>
                                </div>

                                <div class="time-row">
                                    <div class="time-box">
                                        <span class="time-label">Submitted At</span>
                                        <span class="time-value">
                                            {{ $attempt?->finished_at ? $attempt->finished_at->format('M d, h:i A') : '-' }}
                                        </span>
                                    </div>

                                    <div class="time-box">
                                        <span class="time-label">Score</span>
                                        <span class="time-value">
                                            {{ $attempt?->score ?? 0 }}%
                                        </span>
                                    </div>
                                </div>

                                <div class="exam-footer">
                                    <div class="score-box">
                                        Final Score: {{ $attempt?->score ?? 0 }}%
                                    </div>

                                    <a
                                        href="{{ \App\Filament\Resources\ExamResource\Pages\TakeExam::getUrl(['exam' => $exam->id]) }}"
                                        class="btn-exam btn-view"
                                    >
                                        View Result
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="empty-card" style="grid-column: 1 / -1;">
                                <div class="empty-title">No submitted exams yet.</div>
                                <div class="empty-text">After submitting an exam, your score will appear here.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Closed --}}
                <div x-show="tab === 'closed'" class="section-block">
                    <div class="section-header">
                        <div>
                            <h2 class="section-title">Closed Exams</h2>
                            <p class="section-desc">Exams that are no longer available for submission.</p>
                        </div>

                        <div class="count-pill">
                            {{ $expiredExams->count() }} closed
                        </div>
                    </div>

                    <div class="exam-grid">
                        @forelse ($expiredExams as $exam)
                            <div class="exam-card expired">
                                <div class="exam-top">
                                    <div class="exam-icon">🔒</div>

                                    <div class="exam-main">
                                        <div class="exam-title-row">
                                            <h3 class="exam-title">{{ $exam->title }}</h3>

                                            @if ($exam->is_practice)
                                                <span class="type-badge">Practice</span>
                                            @else
                                                <span class="type-badge">Official</span>
                                            @endif
                                        </div>

                                        <div class="exam-course">
                                            {{ $exam->course?->name }}
                                        </div>

                                        @if ($exam->description)
                                            <div class="exam-description">
                                                {{ $exam->description }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="status-badge status-expired">
                                        Closed
                                    </div>
                                </div>

                                <div class="time-row">
                                    <div class="time-box">
                                        <span class="time-label">Start</span>
                                        <span class="time-value">
                                            {{ $exam->start_time ? $exam->start_time->format('M d, h:i A') : '-' }}
                                        </span>
                                    </div>

                                    <div class="time-box">
                                        <span class="time-label">Ended</span>
                                        <span class="time-value">
                                            {{ $exam->end_time ? $exam->end_time->format('M d, h:i A') : '-' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="exam-footer">
                                    <div class="score-box">
                                        Submission closed
                                    </div>

                                    <button type="button" class="btn-exam btn-disabled" disabled>
                                        Exam Closed
                                    </button>
                                </div>
                            </div>
                        @empty
                            <div class="empty-card" style="grid-column: 1 / -1;">
                                <div class="empty-title">No closed exams.</div>
                                <div class="empty-text">Expired exams will appear here.</div>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-filament-panels::page>

<x-filament::page>
@php
    $lecture = $this->record;
    $text = $lecture->extracted_text;
    $characters = filled($text) ? strlen($text) : 0;
    $words = filled($text) ? str_word_count(strip_tags($text)) : 0;
@endphp

<div class="lecture-view-wrap">
    <section class="lecture-hero">
        <div>
            <div class="eyebrow">Lecture Workspace</div>
            <h1>{{ $lecture->title }}</h1>

            <div class="hero-meta">
                <span>{{ $lecture->course?->name ?? 'No course' }}</span>
                <span>{{ $lecture->created_at?->format('Y-m-d H:i') }}</span>
            </div>
        </div>
    </section>

    <section class="lecture-stats">
        <div class="stat-card">
            <span>Course</span>
            <strong>{{ $lecture->course?->name ?? '-' }}</strong>
        </div>

        <div class="stat-card">
            <span>Words</span>
            <strong>{{ $words }}</strong>
        </div>

        <div class="stat-card">
            <span>Characters</span>
            <strong>{{ $characters }}</strong>
        </div>
    </section>

    <section class="actions-card">
        <div>
            <h2>AI Preparation</h2>
            <p>This lecture is ready to be connected with the AI summarization service.</p>
        </div>

        <div class="actions-row">
            @if($lecture->file_path)
                <a href="{{ asset('storage/' . $lecture->file_path) }}" target="_blank" class="secondary-btn">
                    Open PDF
                </a>
            @endif

            <button
                type="button"
                wire:click="mountAction('summarize')"
                class="primary-btn"
            >
                Summarize Lecture
            </button>
        </div>
    </section>

    <section class="text-card">
        <div class="section-header">
            <div>
                <h2>Extracted Text</h2>
                <p>The extracted content that will be sent later to the AI model.</p>
            </div>

            @if(filled($text))
                <span class="ready-badge">Ready</span>
            @else
                <span class="pending-badge">Empty</span>
            @endif
        </div>

        @if(filled($text))
            <div class="extracted-box">
                {{ $text }}
            </div>
        @else
            <div class="empty-box">
                No extracted text found yet. Please go to Edit and extract the PDF text first.
            </div>
        @endif
    </section>
</div>

<style>
.lecture-view-wrap{
    max-width: 1200px;
    margin: 0 auto;
}

.lecture-hero{
    border: 1px solid #e9eef5;
    border-radius: 24px;
    padding: 1.7rem;
    margin-bottom: 1rem;
    background:
        radial-gradient(circle at top right, rgba(234, 121, 0, 0.13), transparent 30%),
        radial-gradient(circle at top left, rgba(59, 130, 246, 0.08), transparent 25%),
        #ffffff;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
}

.eyebrow{
    color: #ea7900;
    font-size: .8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.lecture-hero h1{
    margin: .45rem 0 .8rem;
    font-size: 2.1rem;
    font-weight: 900;
    color: #111827;
}

.hero-meta{
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.hero-meta span{
    background: #fff7ed;
    color: #9a3412;
    border-radius: 999px;
    padding: .35rem .75rem;
    font-size: .82rem;
    font-weight: 700;
}

.lecture-stats{
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1rem;
}

.stat-card{
    background: #ffffff;
    border: 1px solid #e9eef5;
    border-radius: 18px;
    padding: 1rem;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.04);
}

.stat-card span{
    display: block;
    color: #9ca3af;
    font-size: .8rem;
    font-weight: 800;
    text-transform: uppercase;
}

.stat-card strong{
    display: block;
    margin-top: .45rem;
    font-size: 1.15rem;
    color: #111827;
}

.actions-card,
.text-card{
    background: #ffffff;
    border: 1px solid #e9eef5;
    border-radius: 22px;
    padding: 1.2rem;
    margin-bottom: 1rem;
    box-shadow: 0 8px 30px rgba(15, 23, 42, 0.05);
}

.actions-card{
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

.actions-card h2,
.section-header h2{
    margin: 0;
    color: #111827;
    font-size: 1.3rem;
    font-weight: 900;
}

.actions-card p,
.section-header p{
    margin: .35rem 0 0;
    color: #6b7280;
}

.actions-row{
    display: flex;
    gap: .6rem;
    flex-wrap: wrap;
}

.primary-btn,
.secondary-btn{
    border: 0;
    text-decoration: none;
    border-radius: 13px;
    padding: .75rem 1rem;
    font-size: .88rem;
    font-weight: 900;
    cursor: pointer;
}

.primary-btn{
    background: #ea7900;
    color: #ffffff;
}

.primary-btn:hover{
    background: #c76000;
}

.secondary-btn{
    background: #f3f4f6;
    color: #374151;
}

.secondary-btn:hover{
    background: #e5e7eb;
    color: #111827;
}

.section-header{
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.ready-badge,
.pending-badge{
    border-radius: 999px;
    padding: .4rem .75rem;
    font-size: .78rem;
    font-weight: 900;
}

.ready-badge{
    background: #dcfce7;
    color: #166534;
}

.pending-badge{
    background: #fef3c7;
    color: #92400e;
}

.extracted-box{
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 1rem;
    color: #374151;
    line-height: 1.9;
    white-space: pre-wrap;
    max-height: 520px;
    overflow-y: auto;
}

.empty-box{
    background: #f8fafc;
    border: 1px dashed #cbd5e1;
    border-radius: 18px;
    padding: 1.2rem;
    color: #94a3b8;
    text-align: center;
}

@media(max-width: 900px){
    .lecture-stats{
        grid-template-columns: 1fr;
    }

    .actions-card{
        align-items: flex-start;
        flex-direction: column;
    }
}
</style>
</x-filament::page>

<x-guest-layout>
    <style>
        .page-content h1, .page-content h2, .page-content h3 {
            color: #22d3ee; /* Cyan-400 */
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.05em;
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.875rem; /* text-3xl */
        }
        .page-content h2 { font-size: 1.5rem; }
        .page-content h3 { font-size: 1.25rem; }
        
        .page-content p {
            color: #9ca3af; /* Gray-400 */
            line-height: 1.625;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .page-content strong {
            color: #22d3ee;
            font-weight: 700;
        }
        
        .page-content ul {
            list-style-type: none;
            padding: 0;
            margin-bottom: 1.5rem;
        }
        
        .page-content li {
            color: #9ca3af;
            margin-bottom: 0.75rem;
            position: relative;
            padding-left: 1.5rem;
        }
        
        .page-content li::before {
            content: "•";
            color: #22d3ee;
            position: absolute;
            left: 0;
            font-weight: bold;
        }

        .page-content h1:first-child, 
        .page-content h2:first-child, 
        .page-content h3:first-child {
            margin-top: 0;
        }
    </style>

    <div class="max-w-7xl mx-auto p-6 text-gray-300">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="/" class="text-cyan-400 hover:text-cyan-300 flex items-center gap-2 font-bold text-sm transition-colors">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="bg-black/60 border border-gray-800 rounded-2xl p-10 shadow-2xl backdrop-blur-md min-h-[500px]">
            @if($content)
                <div class="page-content">
                    {!! $content !!}
                </div>
            @else
                <div class="text-center py-24">
                    <i class="fa-solid fa-file-circle-exclamation text-5xl text-gray-800 mb-6"></i>
                    <p class="text-gray-500 italic text-lg uppercase tracking-widest font-black">Konten ({{ $title }}) Kosong</p>
                </div>
            @endif

        </div>
    </div>
</x-guest-layout>

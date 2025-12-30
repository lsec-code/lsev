<x-app-layout>
<div class="max-w-7xl mx-auto px-6 py-8">
<div class="max-w-4xl mx-auto">
    <div class="bg-[#0a0a0a] border border-[#333] rounded-xl p-8">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-white">
                <i class="fa-solid fa-bullhorn text-pink-500 mr-2"></i>
                Pengumuman
            </h1>
            <a href="{{ route('admin.dashboard') }}" 
                class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition">
                <i class="fa-solid fa-arrow-left mr-2"></i>
                Kembali ke Panel Admin
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 bg-green-500/10 border border-green-500/20 rounded-lg p-4">
                <p class="text-green-500 font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.announcements.update') }}" class="space-y-6" x-data="{
            color: '{{ $announcement->color ?? '#00ff00' }}',
            isRainbow: {{ ($announcement->color ?? '#00ff00') === 'rainbow' ? 'true' : 'false' }},
            content: '{{ $announcement->content ?? '' }}'
        }">
            @csrf
            @method('PUT')

            {{-- Content --}}
            <div>
                <label class="block text-gray-400 text-sm font-bold mb-2">Isi Pengumuman</label>
                <textarea name="content" x-model="content" rows="4" required
                    class="w-full bg-[#222] border border-[#333] rounded-lg px-4 py-3 text-white focus:border-pink-500 focus:outline-none"
                    placeholder="Contoh: Website ini masih dalam tahap beta testing..."></textarea>
            </div>

            {{-- Color Presets --}}
            <div>
                <label class="block text-gray-400 text-sm font-bold mb-2">Warna Neon</label>
                <input type="hidden" name="color" x-model="color">
                <div class="grid grid-cols-3 gap-2">
                    <button type="button" @click="color = '#ff0000'; isRainbow = false"
                        class="px-4 py-3 rounded-lg border-2 transition font-bold text-sm"
                        :class="color === '#ff0000' && !isRainbow ? 'border-red-500 bg-red-500/20 text-red-500' : 'border-[#333] bg-[#222] text-gray-400 hover:border-red-500/50'">
                        🔴 Red
                    </button>
                    <button type="button" @click="color = '#ff1493'; isRainbow = false"
                        class="px-4 py-3 rounded-lg border-2 transition font-bold text-sm"
                        :class="color === '#ff1493' && !isRainbow ? 'border-pink-500 bg-pink-500/20 text-pink-500' : 'border-[#333] bg-[#222] text-gray-400 hover:border-pink-500/50'">
                        💗 Pink
                    </button>
                    <button type="button" @click="color = '#00ff00'; isRainbow = false"
                        class="px-4 py-3 rounded-lg border-2 transition font-bold text-sm"
                        :class="color === '#00ff00' && !isRainbow ? 'border-green-500 bg-green-500/20 text-green-500' : 'border-[#333] bg-[#222] text-gray-400 hover:border-green-500/50'">
                        🟢 Hijau
                    </button>
                    <button type="button" @click="color = '#ffffff'; isRainbow = false"
                        class="px-4 py-3 rounded-lg border-2 transition font-bold text-sm"
                        :class="color === '#ffffff' && !isRainbow ? 'border-white bg-white/20 text-white' : 'border-[#333] bg-[#222] text-gray-400 hover:border-white/50'">
                        ⚪ Putih
                    </button>
                    <button type="button" @click="color = '#00ffff'; isRainbow = false"
                        class="px-4 py-3 rounded-lg border-2 transition font-bold text-sm"
                        :class="color === '#00ffff' && !isRainbow ? 'border-cyan-500 bg-cyan-500/20 text-cyan-500' : 'border-[#333] bg-[#222] text-gray-400 hover:border-cyan-500/50'">
                        🔵 Cyan
                    </button>
                    <button type="button" @click="color = 'rainbow'; isRainbow = true"
                        class="px-4 py-3 rounded-lg border-2 transition font-bold text-sm"
                        :class="isRainbow ? 'border-purple-500 bg-gradient-to-r from-red-500/20 via-green-500/20 to-blue-500/20 text-purple-500' : 'border-[#333] bg-[#222] text-gray-400 hover:border-purple-500/50'">
                        🌈 Rainbow
                    </button>
                </div>
            </div>

            {{-- Preview --}}
            <div>
                <label class="block text-gray-400 text-sm font-bold mb-4">
                    <i class="fa-solid fa-eye text-cyan-500 mr-2"></i>
                    Preview
                </label>
                <div class="border backdrop-blur-sm rounded-xl overflow-hidden h-16 relative shadow-lg"
                    :style="`border-color: ${isRainbow ? '#00ffff' : color}33; background: ${isRainbow ? 'rgba(0,255,255,0.03)' : color}0D;`">
                    <div class="w-full h-full flex items-center px-4 overflow-hidden">
                        <div class="mr-4 flex-shrink-0 flex items-center border-r pr-4 h-full"
                            :style="`border-color: ${isRainbow ? '#00ffff' : color}33;`">
                            <i class="fa-solid fa-bullhorn animate-pulse" 
                                :style="`color: ${isRainbow ? '#00ffff' : color};`"></i>
                        </div>
                        <div class="flex-1 overflow-hidden">
                            <marquee class="font-black text-lg tracking-wider uppercase" scrollamount="6"
                                :class="isRainbow ? 'rainbow-text' : ''"
                                :style="!isRainbow ? `color: ${color}; text-shadow: 0 0 10px ${color}, 0 0 20px ${color};` : ''"
                                x-text="content || 'Isi pengumuman...'">
                            </marquee>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Submit Button --}}
            <div>
                <button type="submit" 
                    class="w-full bg-gradient-to-r from-pink-600 to-purple-600 hover:from-pink-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg transition">
                    <i class="fa-solid fa-save mr-2"></i>
                    Simpan Pengumuman
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .rainbow-text {
        animation: rainbow-colors 3s linear infinite;
    }

    @keyframes rainbow-colors {
        0% { color: #ff0000; text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000; }
        14% { color: #ff7f00; text-shadow: 0 0 10px #ff7f00, 0 0 20px #ff7f00; }
        28% { color: #ffff00; text-shadow: 0 0 10px #ffff00, 0 0 20px #ffff00; }
        42% { color: #00ff00; text-shadow: 0 0 10px #00ff00, 0 0 20px #00ff00; }
        57% { color: #0000ff; text-shadow: 0 0 10px #0000ff, 0 0 20px #0000ff; }
        71% { color: #4b0082; text-shadow: 0 0 10px #4b0082, 0 0 20px #4b0082; }
        85% { color: #9400d3; text-shadow: 0 0 10px #9400d3, 0 0 20px #9400d3; }
        100% { color: #ff0000; text-shadow: 0 0 10px #ff0000, 0 0 20px #ff0000; }
    }
</style>
</div>
</x-app-layout>

<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 md:px-6 space-y-8 md:space-y-12 pb-10 md:pb-20">
        
        <!-- HEADER -->
        <div class="text-center space-y-4 pt-8 animate-fade-in-down">
            <h1 class="text-4xl md:text-5xl font-black text-transparent bg-clip-text bg-gradient-to-r from-[#00ffff] to-purple-500 tracking-tighter drop-shadow-[0_0_20px_rgba(0,255,255,0.3)]">
                LEADERBOARD
            </h1>
            <p class="text-gray-400 font-medium max-w-2xl mx-auto">
                Para sultan dan top earner komunitas kami. Apakah nama Anda ada di sini?
            </p>
        </div>

        <!-- TOP 7 BALANCE SECTION -->
        <section class="space-y-8">
            <div class="flex items-center gap-4 mb-20">
                <div class="h-px bg-gradient-to-r from-transparent via-[#00ffff]/50 to-transparent flex-1"></div>
                <h2 class="text-xl font-black text-white uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-crown text-[#FFC107] animate-bounce"></i> Top Total Pendapatan
                </h2>
                <div class="h-px bg-gradient-to-r from-transparent via-[#00ffff]/50 to-transparent flex-1"></div>
            </div>

            <!-- Top 3 Podium (Grid) -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8 relative items-end mb-10 md:mb-16">
                <!-- Background Glow -->
                <div class="absolute inset-0 bg-[#00ffff]/5 blur-[100px] rounded-full pointer-events-none"></div>

                @if($topBalances->count() > 1)
                <!-- RANK 2 (Silver) -->
                <div class="order-2 md:order-1 relative group hover:-translate-y-2 transition-transform duration-500">
                    <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-gray-600 rounded-2xl p-4 text-center shadow-[0_0_30px_rgba(156,163,175,0.1)] relative overflow-hidden">
                        <div class="relative mb-3 inline-block">
                             <div class="w-16 h-16 rounded-full p-1 bg-gradient-to-tr from-gray-300 to-gray-600 mx-auto">
                                <img src="{{ $topBalances[1]->getAvatarUrl() }}" class="w-full h-full rounded-full object-cover border-2 border-[#0a0a0a]">
                             </div>
                             <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-gray-400 text-black font-black text-[10px] px-2 py-0.5 rounded-full shadow-lg border border-white/20">
                                #2
                             </div>
                        </div>

                        <h3 class="text-white font-bold text-sm truncate mb-3">{{ $topBalances[1]->username }}</h3>
                        
                        <div class="bg-[#111] rounded-lg py-1.5 px-3 inline-block border border-gray-700">
                             <span class="text-[#00ffff] font-black tracking-wide text-xs">Rp {{ number_format($topBalances[1]->total_earnings, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($topBalances->count() > 0)
                <!-- RANK 1 (Gold) -->
                <div class="order-1 md:order-2 relative group -mt-8 z-10 hover:-translate-y-2 transition-transform duration-500">
                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 text-4xl text-[#FFC107] animate-pulse drop-shadow-[0_0_15px_rgba(255,193,7,0.5)]">
                        <i class="fa-solid fa-crown"></i>
                    </div>

                    <div class="bg-[#0a0a0a]/90 backdrop-blur-xl border border-[#FFC107] rounded-2xl p-6 text-center shadow-[0_0_50px_rgba(255,193,7,0.2)] relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-3 opacity-10"><i class="fa-solid fa-trophy text-5xl text-[#FFC107]"></i></div>
                        
                        <div class="relative mb-4 inline-block">
                             <div class="w-24 h-24 rounded-full p-1 bg-gradient-to-tr from-[#FFC107] to-yellow-600 mx-auto shadow-[0_0_30px_rgba(255,193,7,0.3)]">
                                <img src="{{ $topBalances[0]->getAvatarUrl() }}" class="w-full h-full rounded-full object-cover border-4 border-[#0a0a0a]">
                             </div>
                             <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-[#FFC107] text-black font-black text-xs px-3 py-1 rounded-full shadow-xl border border-white/20">
                                #1 KING
                             </div>
                        </div>

                        <h3 class="text-white font-black text-lg truncate mb-4">{{ $topBalances[0]->username }}</h3>
                        
                        <div class="bg-gradient-to-r from-[#FFC107]/10 to-yellow-600/10 rounded-xl py-2 px-5 inline-block border border-[#FFC107]/30">
                             <span class="text-[#FFC107] font-black text-base tracking-wider drop-shadow-sm">Rp {{ number_format($topBalances[0]->total_earnings, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif

                @if($topBalances->count() > 2)
                <!-- RANK 3 (Bronze) -->
                <div class="order-3 relative group hover:-translate-y-2 transition-transform duration-500">
                    <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-orange-700/50 rounded-2xl p-4 text-center shadow-[0_0_30px_rgba(180,83,9,0.1)] relative overflow-hidden">
                        
                        <div class="relative mb-3 inline-block">
                             <div class="w-16 h-16 rounded-full p-1 bg-gradient-to-tr from-orange-400 to-orange-800 mx-auto">
                                <img src="{{ $topBalances[2]->getAvatarUrl() }}" class="w-full h-full rounded-full object-cover border-2 border-[#0a0a0a]">
                             </div>
                             <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-orange-600 text-white font-black text-[10px] px-2 py-0.5 rounded-full shadow-lg border border-white/20">
                                #3
                             </div>
                        </div>

                        <h3 class="text-white font-bold text-sm truncate mb-3">{{ $topBalances[2]->username }}</h3>
                        
                        <div class="bg-[#111] rounded-lg py-1.5 px-3 inline-block border border-gray-700">
                             <span class="text-[#00ffff] font-black tracking-wide text-xs">Rp {{ number_format($topBalances[2]->total_earnings, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Rank 4-7 List -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($topBalances->skip(3) as $index => $user)
                <div class="bg-[#111]/50 backdrop-blur-sm border border-gray-800 rounded-xl p-3 flex items-center justify-between hover:border-[#00ffff]/30 transition-all group">
                    <div class="flex items-center gap-3">
                        <div class="w-6 h-6 flex items-center justify-center font-black text-[10px] text-gray-600 bg-[#0a0a0a] rounded border border-gray-800 group-hover:text-[#00ffff] group-hover:border-[#00ffff]/30 transition-colors">
                            #{{ $loop->iteration + 3 }}
                        </div>
                        <img src="{{ $user->getAvatarUrl() }}" class="w-8 h-8 rounded-full object-cover border border-gray-700">
                        <div>
                            <h4 class="text-xs font-bold text-white group-hover:text-[#00ffff] transition-colors">{{ $user->username }}</h4>
                        </div>
                    </div>
                    <div class="font-bold text-[#00ffff] text-xs">
                        Rp {{ number_format($user->total_earnings, 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <!-- TOP 5 WITHDRAWALS SECTION -->
        <section>
            <div class="flex items-center gap-4 mb-8 mt-12">
                <div class="h-px bg-gradient-to-r from-transparent via-green-500/50 to-transparent flex-1"></div>
                <h2 class="text-xl font-black text-white uppercase tracking-widest flex items-center gap-3">
                    <i class="fa-solid fa-fire text-orange-500 animate-bounce"></i> Top Penghasilan Harian
                </h2>
                <div class="h-px bg-gradient-to-r from-transparent via-green-500/50 to-transparent flex-1"></div>
            </div>

            <div class="bg-[#0a0a0a]/60 backdrop-blur-xl border border-green-500/20 rounded-2xl overflow-hidden relative">
                <!-- Decoration -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-green-500/10 rounded-full blur-[50px] pointer-events-none"></div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-gray-800 bg-[#0a0a0a]">
                                <th class="p-5 text-[10px] font-black text-gray-500 uppercase tracking-widest">Rank</th>
                                <th class="p-5 text-[10px] font-black text-gray-500 uppercase tracking-widest">User</th>
                                <th class="p-5 text-[10px] font-black text-gray-500 uppercase tracking-widest text-right">Penghasilan Hari Ini</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($topDailyEarnings as $index => $stat)
                            <tr class="hover:bg-[#111] transition-colors group">
                                <td class="p-5">
                                    <div class="w-8 h-8 flex items-center justify-center font-black text-xs rounded-lg {{ $index == 0 ? 'bg-green-500 text-black shadow-lg shadow-green-500/20' : 'bg-[#151515] text-gray-500' }}">
                                        {{ $index + 1 }}
                                    </div>
                                </td>
                                <td class="p-5">
                                    <div class="flex items-center gap-3">
                                        @php $avatarType = $stat->user->getAvatarType(); @endphp
                                        
                                        <div class="relative w-10 h-10 rounded-full p-0.5 shrink-0
                                            {{ $avatarType === 'dev' ? 'bg-gradient-to-tr from-red-600 to-blue-600 animate-pulse shadow-[0_0_20px_rgba(220,38,38,0.4)]' : '' }}
                                            {{ $avatarType === 'gold' ? 'bg-gradient-to-tr from-[#FFC107] to-yellow-600 shadow-[0_0_15px_rgba(255,193,7,0.3)]' : '' }}
                                            {{ $avatarType === 'silver' ? 'bg-gradient-to-tr from-gray-300 to-gray-600 shadow-[0_0_15px_rgba(156,163,175,0.3)]' : '' }}
                                            {{ $avatarType === 'bronze' ? 'bg-gradient-to-tr from-orange-400 to-orange-800 shadow-[0_0_15px_rgba(234,88,12,0.3)]' : '' }}
                                            {{ $avatarType === 'default' ? 'bg-[#111] border-2 border-[#00ffff]/30' : '' }}
                                        ">
                                            <img src="{{ $stat->user->getAvatarUrl() }}" class="w-full h-full rounded-full object-cover border-2 border-[#0a0a0a]">
                                            
                                            {{-- Rank Badge --}}
                                            @if($avatarType === 'dev')
                                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-gradient-to-r from-red-600 to-blue-600 text-white font-black text-[8px] px-1.5 py-0.5 rounded-full shadow-lg border border-white/20">
                                                    DEV
                                                </div>
                                            @elseif($avatarType === 'gold')
                                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-[#FFC107] text-black font-black text-[8px] px-1.5 py-0.5 rounded-full shadow-lg border border-white/20">
                                                    #1
                                                </div>
                                            @elseif($avatarType === 'silver')
                                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-gray-400 text-black font-black text-[8px] px-1.5 py-0.5 rounded-full shadow-lg border border-white/20">
                                                    #2
                                                </div>
                                            @elseif($avatarType === 'bronze')
                                                <div class="absolute -bottom-1 left-1/2 -translate-x-1/2 bg-orange-600 text-white font-black text-[8px] px-1.5 py-0.5 rounded-full shadow-lg border border-white/20">
                                                    #3
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <div>
                                            <div class="font-bold text-sm text-white group-hover:text-orange-400 transition-colors">{{ $stat->user->username }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-5 text-right">
                                    <span class="font-mono font-bold text-orange-500 group-hover:text-orange-400 group-hover:drop-shadow-[0_0_5px_rgba(249,115,22,0.5)] transition-all">
                                        Rp {{ number_format($stat->daily_earning, 0, ',', '.') }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>
</x-app-layout>

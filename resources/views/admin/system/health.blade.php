<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-green-900/20 flex items-center justify-center border border-green-500/20">
                        <i class="fa-solid fa-heart-pulse text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Kesehatan Sistem</h1>
                        <p class="text-gray-400 text-sm">Monitor server resources dan error logs</p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                    <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                </a>
            </div>

            <!-- System Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- PHP Version -->
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fa-brands fa-php text-purple-500 text-2xl"></i>
                        <div class="text-gray-400 text-xs font-bold uppercase">PHP Version</div>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $health['php_version'] }}</div>
                </div>

                <!-- Laravel Version -->
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fa-brands fa-laravel text-red-500 text-2xl"></i>
                        <div class="text-gray-400 text-xs font-bold uppercase">Laravel Version</div>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $health['laravel_version'] }}</div>
                </div>

                <!-- Database -->
                <div class="bg-[#0a0a0a] border border-[#222] p-6 rounded-xl">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fa-solid fa-database text-blue-500 text-2xl"></i>
                        <div class="text-gray-400 text-xs font-bold uppercase">Database</div>
                    </div>
                    <div class="text-2xl font-bold text-white">{{ $health['database'] }}</div>
                </div>
            </div>

            <!-- Resource Usage -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Memory Usage -->
                <div class="bg-[#0a0a0a] border border-[#222] rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-memory text-yellow-500"></i>
                        Memory Usage
                    </h3>
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400">Current</span>
                                <span class="text-white font-bold">{{ number_format($health['memory']['used'] / 1024 / 1024, 2) }} MB</span>
                            </div>
                            <div class="w-full bg-[#111] rounded-full h-3">
                                <div class="bg-yellow-500 h-3 rounded-full" style="width: {{ min(($health['memory']['used'] / $health['memory']['peak']) * 100, 100) }}%"></div>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400">Peak</span>
                                <span class="text-white font-bold">{{ number_format($health['memory']['peak'] / 1024 / 1024, 2) }} MB</span>
                            </div>
                        </div>
                        
                        <div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Limit</span>
                                <span class="text-white font-bold">{{ $health['memory']['limit'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Disk Usage -->
                <div class="bg-[#0a0a0a] border border-[#222] rounded-xl p-6">
                    <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-hard-drive text-blue-500"></i>
                        Disk Usage
                    </h3>
                    
                    @php
                        $diskUsedPercent = ($health['disk']['used'] / $health['disk']['total']) * 100;
                    @endphp
                    
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-400">Used</span>
                                <span class="text-white font-bold">{{ number_format($health['disk']['used'] / 1024 / 1024 / 1024, 2) }} GB</span>
                            </div>
                            <div class="w-full bg-[#111] rounded-full h-3">
                                <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $diskUsedPercent }}%"></div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-400">Free</span>
                                <p class="text-white font-bold">{{ number_format($health['disk']['free'] / 1024 / 1024 / 1024, 2) }} GB</p>
                            </div>
                            <div>
                                <span class="text-gray-400">Total</span>
                                <p class="text-white font-bold">{{ number_format($health['disk']['total'] / 1024 / 1024 / 1024, 2) }} GB</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Logs -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-[#222] flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white flex items-center gap-2">
                        <i class="fa-solid fa-bug text-red-500"></i>
                        Recent Error Logs (Last 50 Lines)
                    </h3>
                    <span class="px-3 py-1 bg-red-500/10 border border-red-500/20 text-red-400 rounded text-xs font-bold">
                        {{ count($errorLogs) }} Lines
                    </span>
                </div>
                
                <div class="p-6">
                    @if(count($errorLogs) > 0)
                        <div class="bg-[#111] border border-[#333] rounded-lg p-4 max-h-96 overflow-y-auto">
                            <pre class="text-xs text-gray-400 font-mono whitespace-pre-wrap">@foreach($errorLogs as $log){{ $log }}@endforeach</pre>
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fa-solid fa-check-circle text-4xl mb-3 text-green-500"></i>
                            <p class="text-sm">No error logs found</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

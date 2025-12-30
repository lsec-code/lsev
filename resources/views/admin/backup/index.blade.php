<x-app-layout>
    <div class="max-w-7xl mx-auto px-6 py-8">
        <div class="max-w-full mx-auto space-y-8">
            
            <!-- Header -->
            <div class="flex items-center justify-between mb-8 pb-6 border-b border-[#222]">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-blue-900/20 flex items-center justify-center border border-blue-500/20">
                        <i class="fa-solid fa-database text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Backup & Restore</h1>
                        <p class="text-gray-400 text-sm">Backup database untuk proteksi data</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('admin.backup.create') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition text-sm">
                            <i class="fa-solid fa-plus mr-2"></i> Buat Backup Baru
                        </button>
                    </form>
                    <a href="{{ route('admin.dashboard') }}" class="bg-[#222] hover:bg-[#2a2a2a] border border-[#333] text-gray-400 font-bold py-2 px-4 rounded-lg transition text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Kembali
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-500/10 border border-green-500/20 text-green-400 px-4 py-3 rounded-lg">
                    <i class="fa-solid fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-4 py-3 rounded-lg">
                    <i class="fa-solid fa-exclamation-circle mr-2"></i> {{ session('error') }}
                </div>
            @endif

            <!-- Info & Configuration Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Info Card -->
                <div class="bg-blue-500/10 border border-blue-500/20 rounded-xl p-6">
                    <div class="flex items-start gap-4">
                        <i class="fa-solid fa-info-circle text-blue-500 text-xl mt-1"></i>
                        <div class="text-sm">
                            <h3 class="font-black text-blue-400 uppercase tracking-widest text-xs mb-3">Informasi Backup</h3>
                            <ul class="space-y-2 text-blue-300/80">
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px]"></i> Backup seluruh database MySQL</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px]"></i> Lokasi: <code class="bg-blue-900/30 px-1.5 py-0.5 rounded text-blue-200">storage/app/backups/</code></li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px]"></i> Simpan file secara berkala di tempat aman</li>
                                <li class="flex items-center gap-2"><i class="fa-solid fa-check text-[10px]"></i> Auto-cleanup aktif (simpan 10 file terakhir)</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Setup / Configuration Card -->
                <div class="bg-indigo-500/10 border border-indigo-500/20 rounded-xl p-6">
                    <h3 class="font-black text-indigo-400 uppercase tracking-widest text-xs mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-gears"></i> Konfigurasi Path MySQL
                    </h3>
                    <form action="{{ route('admin.backup.settings.update') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-indigo-300 text-[10px] font-black uppercase tracking-widest mb-2">Perintah / Path mysqldump</label>
                            <input type="text" name="mysqldump_command" value="{{ $mysqldump_command }}" 
                                class="w-full bg-[#0a0a0a] border border-indigo-500/20 rounded-xl px-4 py-3 text-white text-sm focus:border-indigo-500 outline-none transition-all font-mono"
                                placeholder="mysqldump">
                        </div>
                        <div class="bg-indigo-900/20 rounded-lg p-3 space-y-2">
                            <p class="text-[10px] text-indigo-400 font-bold uppercase tracking-tight">💡 Contoh sesuai Environment:</p>
                            <div class="space-y-1 font-mono text-[9px]">
                                <p class="text-gray-500">Ubuntu/Linux: <span class="text-indigo-300">mysqldump</span></p>
                                <p class="text-gray-500">Laragon: <span class="text-indigo-300">C:\laragon\bin\mysql\mysql-X.X\bin\mysqldump</span></p>
                                <p class="text-gray-500">XAMPP: <span class="text-indigo-300">C:\xampp\mysql\bin\mysqldump</span></p>
                            </div>
                        </div>
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-black py-2.5 rounded-xl text-[10px] uppercase tracking-widest transition-all">
                            Simpan Konfigurasi
                        </button>
                    </form>
                </div>
            </div>

            <!-- Upload & Restore SQL -->
            <div class="bg-[#0a0a0a] border border-yellow-500/20 rounded-xl p-6 mb-6">
                <h3 class="text-lg font-bold text-white mb-4">Upload & Restore Database</h3>
                <form action="{{ route('admin.backup.upload.restore') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div class="flex items-end gap-3">
                        <div class="flex-1">
                            <label class="block text-gray-400 text-xs font-bold mb-2">File SQL</label>
                            <input type="file" name="sql_file" accept=".sql" required
                                class="w-full bg-[#111] border border-[#333] text-white rounded-lg px-4 py-2 text-sm file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-yellow-500 file:text-black hover:file:bg-yellow-600">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" id="confirm_restore" name="confirm_restore" class="w-4 h-4" required>
                            <label for="confirm_restore" class="text-xs text-gray-400">Saya paham risikonya</label>
                        </div>
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded-lg text-sm transition">
                            <i class="fa-solid fa-upload mr-2"></i> Restore
                        </button>
                    </div>
                    <p class="text-xs text-yellow-400/60">⚠️ Warning: Ini akan menimpa seluruh database! Backup dulu sebelum restore.</p>
                </form>
            </div>

            <!-- Backup List -->
            <div class="bg-[#0a0a0a] border border-[#222] rounded-xl shadow-[0_0_50px_rgba(0,0,0,0.3)] overflow-hidden">
                <div class="px-6 py-4 border-b border-[#222] flex items-center justify-between">
                    <h3 class="text-lg font-bold text-white">Daftar Backup</h3>
                    <span class="px-3 py-1 bg-blue-500/10 border border-blue-500/20 text-blue-400 rounded text-xs font-bold">
                        {{ count($backups) }} Backup Files
                    </span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-[#111] border-b border-[#222]">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Filename</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Size</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#222]">
                            @forelse($backups as $backup)
                                <tr class="hover:bg-[#111] transition">
                                    <!-- Filename -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-file-zipper text-blue-500"></i>
                                            <span class="text-white font-mono text-sm">{{ $backup['name'] }}</span>
                                        </div>
                                    </td>

                                    <!-- Size -->
                                    <td class="px-6 py-4">
                                        <span class="text-gray-400 text-sm">
                                            {{ number_format($backup['size'] / 1024 / 1024, 2) }} MB
                                        </span>
                                    </td>

                                    <!-- Created -->
                                    <td class="px-6 py-4">
                                        <div class="text-gray-400 text-xs">
                                            <p>{{ date('d M Y H:i', $backup['date']) }}</p>
                                            <p class="text-gray-600">{{ \Carbon\Carbon::createFromTimestamp($backup['date'])->diffForHumans() }}</p>
                                        </div>
                                    </td>

                                    <!-- Actions -->
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <!-- Download -->
                                            <a href="{{ route('admin.backup.download', $backup['name']) }}" 
                                                class="bg-green-500/10 hover:bg-green-500/20 text-green-400 border border-green-500/20 px-3 py-1.5 rounded text-xs font-bold transition">
                                                <i class="fa-solid fa-download mr-1"></i> Download
                                            </a>
                                            <!-- Restore -->
                                            <form id="restore-backup-{{ $loop->index }}" action="{{ route('admin.backup.restore', $backup['name']) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="button" 
                                                    onclick="if(confirm('WARNING: Restore database akan menimpa data saat ini. Lanjutkan?')) { document.getElementById('restore-backup-{{ $loop->index }}').submit(); }"
                                                    class="bg-yellow-500/10 hover:bg-yellow-500/20 text-yellow-400 border border-yellow-500/20 px-3 py-1.5 rounded text-xs font-bold transition">
                                                    <i class="fa-solid fa-database mr-1"></i> Restore
                                                </button>
                                            </form>

                                            <!-- Delete -->
                                            <form id="delete-backup-{{ $loop->index }}" action="{{ route('admin.backup.delete', $backup['name']) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                    onclick="confirmAction('Hapus file backup {{ $backup['name'] }}? Tindakan ini tidak dapat dibatalkan.', () => document.getElementById('delete-backup-{{ $loop->index }}').submit())"
                                                    class="bg-red-500/10 hover:bg-red-500/20 text-red-400 border border-red-500/20 px-3 py-1.5 rounded text-xs font-bold transition">
                                                    <i class="fa-solid fa-trash mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center text-gray-500">
                                            <i class="fa-solid fa-database text-4xl mb-3 text-[#222]"></i>
                                            <p class="text-sm">Belum ada backup</p>
                                            <p class="text-xs text-gray-600 mt-1">Klik "Buat Backup Baru" untuk membuat backup pertama</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

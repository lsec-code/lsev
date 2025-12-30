Set WshShell = CreateObject("WScript.Shell")
Set fso = CreateObject("Scripting.FileSystemObject")

' === UBAH KALAU PERLU ===
projectPath = "C:\laragon\www\v2_laravel"
phpPath = "C:\laragon\bin\php\php-8.3.28-Win32-vs16-x64\php.exe"
artisanPath = projectPath & "\artisan"

' Cek apakah file php.exe ada
If Not fso.FileExists(phpPath) Then
    WScript.Echo "ERROR: File PHP tidak ditemukan!" & vbCrLf & phpPath & vbCrLf & vbCrLf & "Periksa versi PHP di Laragon."
    WScript.Quit
End If

' Cek apakah file artisan ada
If Not fso.FileExists(artisanPath) Then
    WScript.Echo "ERROR: File artisan tidak ditemukan!" & vbCrLf & artisanPath & vbCrLf & vbCrLf & "Pastikan folder project Laravel benar (harus ada file 'artisan' di root)."
    WScript.Quit
End If

' Jalankan server
command = """" & phpPath & """ """ & artisanPath & """ serve --port=8000"

WshShell.CurrentDirectory = projectPath
returnCode = WshShell.Run(command, 0, False)

If returnCode = 0 Then
    WScript.Echo "Server Laravel BERHASIL dijalankan!" & vbCrLf & "Buka: http://localhost:8000" & vbCrLf & vbCrLf & "Tutup pesan ini → server tetap jalan di background."
Else
    WScript.Echo "Gagal menjalankan server. Kode: " & returnCode
End If
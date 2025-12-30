<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = ['setting_key', 'setting_value'];

    public static function getThemePresets()
    {
        return [
            'glass-dark' => [
                'name' => 'Glass Dark',
                'category' => 'Glass',
                'description' => 'Frosted transparency with high blur.',
                'primary' => '#00ffff',
                'secondary' => '#008888',
                'bg' => '#000000',
                'preview_bg' => 'bg-black',
                'preview_accent' => 'text-cyan-400',
                'css_vars' => [
                    '--bg-dark' => '#000000',
                    '--bg-card' => 'rgba(15, 15, 15, 0.7)',
                    '--bg-hover' => 'rgba(255, 255, 255, 0.05)',
                    '--primary' => '#00ffff',
                    '--primary-hover' => '#00cccc',
                    '--text-main' => '#ffffff',
                    '--text-muted' => '#a1a1a1',
                    '--border-color' => 'rgba(255, 255, 255, 0.1)',
                    '--glass-blur' => '12px'
                ]
            ],
            'glass-light' => [
                'name' => 'Glass Light',
                'category' => 'Glass',
                'description' => 'Clean transparent look on white background.',
                'primary' => '#3b82f6',
                'secondary' => '#1d4ed8',
                'bg' => '#f0f2f5',
                'preview_bg' => 'bg-slate-100',
                'preview_accent' => 'text-blue-600',
                'css_vars' => [
                    '--bg-dark' => '#f0f2f5',
                    '--bg-card' => 'rgba(255, 255, 255, 0.7)',
                    '--bg-hover' => 'rgba(0, 0, 0, 0.05)',
                    '--primary' => '#3b82f6',
                    '--primary-hover' => '#2563eb',
                    '--text-main' => '#1f2937',
                    '--text-muted' => '#6b7280',
                    '--border-color' => 'rgba(0, 0, 0, 0.1)',
                    '--glass-blur' => '10px'
                ]
            ],
            'material-dark' => [
                'name' => 'Material Dark',
                'category' => 'Material',
                'description' => 'Solid, elegant, with deep contrast.',
                'primary' => '#bb86fc',
                'secondary' => '#3700b3',
                'bg' => '#121212',
                'preview_bg' => 'bg-[#121212]',
                'preview_accent' => 'text-purple-400',
                'css_vars' => [
                    '--bg-dark' => '#121212',
                    '--bg-card' => '#1e1e1e',
                    '--bg-hover' => '#2c2c2c',
                    '--primary' => '#bb86fc',
                    '--primary-hover' => '#9965f4',
                    '--text-main' => '#ffffff',
                    '--text-muted' => '#b0b0b0',
                    '--border-color' => '#333333',
                    '--glass-blur' => '0px'
                ]
            ],
            'material-light' => [
                'name' => 'Material Light',
                'category' => 'Material',
                'description' => 'Classic Google Material architecture.',
                'primary' => '#6200ee',
                'secondary' => '#3700b3',
                'bg' => '#fafafa',
                'preview_bg' => 'bg-white',
                'preview_accent' => 'text-indigo-600',
                'css_vars' => [
                    '--bg-dark' => '#fafafa',
                    '--bg-card' => '#ffffff',
                    '--bg-hover' => '#f5f5f5',
                    '--primary' => '#6200ee',
                    '--primary-hover' => '#3700b3',
                    '--text-main' => '#000000',
                    '--text-muted' => '#757575',
                    '--border-color' => '#e0e0e0',
                    '--glass-blur' => '0px'
                ]
            ],
            'neon-pink' => [
                'name' => 'Neon Pink',
                'category' => 'Neon',
                'description' => 'Cyberpunk vibe with neon glow.',
                'primary' => '#ff00ff',
                'secondary' => '#700070',
                'bg' => '#050505',
                'preview_bg' => 'bg-black',
                'preview_accent' => 'text-pink-500',
                'css_vars' => [
                    '--bg-dark' => '#050505',
                    '--bg-card' => '#0a0a0a',
                    '--bg-hover' => '#111111',
                    '--primary' => '#ff00ff',
                    '--primary-hover' => '#d000d0',
                    '--text-main' => '#ffffff',
                    '--text-muted' => '#888888',
                    '--border-color' => '#330033',
                    '--glass-blur' => '0px'
                ]
            ],
            'neon-cyan' => [
                'name' => 'Neon Cyan',
                'category' => 'Neon',
                'description' => 'Futuristic touch with electric blue.',
                'primary' => '#00ffff',
                'secondary' => '#007070',
                'bg' => '#000808',
                'preview_bg' => 'bg-black',
                'preview_accent' => 'text-cyan-400',
                'css_vars' => [
                    '--bg-dark' => '#000808',
                    '--bg-card' => '#001212',
                    '--bg-hover' => '#001a1a',
                    '--primary' => '#00ffff',
                    '--primary-hover' => '#00cccc',
                    '--text-main' => '#ffffff',
                    '--text-muted' => '#668888',
                    '--border-color' => '#003333',
                    '--glass-blur' => '0px'
                ]
            ]
        ];
    }
}

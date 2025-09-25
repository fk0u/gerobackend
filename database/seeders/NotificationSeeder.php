<?php

namespace Database\Seeders;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        Notification::query()->delete();

        $now = Carbon::now();
        $endUsers = User::where('role','end_user')->get();
        foreach ($endUsers as $index => $user) {
            Notification::create([
                'user_id' => $user->id,
                'role_scope' => null,
                'title' => 'Selamat Datang, '.$user->name,
                'body' => 'Terima kasih sudah bergabung di Gerobaks! Mulai kumpulkan poin dari sampah terpilah.',
                'type' => 'welcome',
                'is_read' => $index === 0,
                'read_at' => $index === 0 ? $now->copy()->subDays(10) : null,
            ]);

            Notification::create([
                'user_id' => $user->id,
                'role_scope' => null,
                'title' => 'Jadwal Penjemputan Diperbarui',
                'body' => 'Petugas kami sedang menuju lokasi Anda. Mohon siapkan sampah terpilah ya!',
                'type' => 'schedule',
                'is_read' => false,
            ]);
        }

        // Broadcast notifications for mitra role
        Notification::create([
            'user_id' => null,
            'role_scope' => 'mitra',
            'title' => 'Target Mingguan Anda',
            'body' => 'Selesaikan minimal 15 order minggu ini untuk mendapatkan bonus tambahan.',
            'type' => 'target',
            'is_read' => false,
        ]);
    }
}

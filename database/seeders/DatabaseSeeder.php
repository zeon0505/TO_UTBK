<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================================
        // 1. PROGRAM STUDI: KOMUNIKASI & PENYIARAN ISLAM (KPI)
        // =============================================================
        User::updateOrCreate(
            ['email' => 'superadmin@kpi.com'],
            [
                'name' => 'Super Admin (Prodi KPI)',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@kpi.com'],
            [
                'name' => 'Admin Akademik KPI',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@kpi.com'],
            [
                'name' => 'Dr. Ahmad Farhan, M.I.Kom.',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'nip' => '198501012010121001',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@kpi.com'],
            [
                'name' => 'Rizky Pratama (Mahasiswa KPI)',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '210101001',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'semester' => 5,
                'kelas' => 'KPI-5A',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'tes1@kpi.com'],
            [
                'name' => 'tes1',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '210101002',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'semester' => 5,
                'kelas' => 'KPI-5A',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'tes2@kpi.com'],
            [
                'name' => 'tes2',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '210101003',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'semester' => 5,
                'kelas' => 'KPI-5A',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'tes3@kpi.com'],
            [
                'name' => 'tes 3',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '210101004',
                'prodi' => 'Komunikasi dan Penyiaran Islam',
                'semester' => 5,
                'kelas' => 'KPI-5A',
                'is_admin' => false,
            ]
        );


        // =============================================================
        // 2. PROGRAM STUDI: HUKUM TATA NEGARA (HTN)
        // =============================================================
        User::updateOrCreate(
            ['email' => 'superadmin@htn.com'],
            [
                'name' => 'Super Admin (Prodi HTN)',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'prodi' => 'Hukum Tata Negara',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@htn.com'],
            [
                'name' => 'Admin Akademik HTN',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'prodi' => 'Hukum Tata Negara',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@htn.com'],
            [
                'name' => 'Prof. Dr. Hendra Gunawan, S.H., M.H.',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'nip' => '198802022012122002',
                'prodi' => 'Hukum Tata Negara',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@htn.com'],
            [
                'name' => 'Siti Nurhaliza (Mahasiswa HTN)',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '210202002',
                'prodi' => 'Hukum Tata Negara',
                'semester' => 5,
                'kelas' => 'HTN-5B',
                'is_admin' => false,
            ]
        );


        // =============================================================
        // 3. PROGRAM STUDI: PENDIDIKAN AGAMA ISLAM (PAI)
        // =============================================================
        User::updateOrCreate(
            ['email' => 'superadmin@pai.com'],
            [
                'name' => 'Super Admin (Prodi PAI)',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'prodi' => 'Pendidikan Agama Islam',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@pai.com'],
            [
                'name' => 'Admin Akademik PAI',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'prodi' => 'Pendidikan Agama Islam',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@pai.com'],
            [
                'name' => 'Drs. H. Mulyadi, M.Pd.',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'nip' => '199003032015031003',
                'prodi' => 'Pendidikan Agama Islam',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@pai.com'],
            [
                'name' => 'Ahmad Fauzi (Mahasiswa PAI)',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '210303003',
                'prodi' => 'Pendidikan Agama Islam',
                'semester' => 5,
                'kelas' => 'PAI-5A',
                'is_admin' => false,
            ]
        );


        // =============================================================
        // 4. PROGRAM STUDI: EKONOMI SYARIAH (ES)
        // =============================================================
        User::updateOrCreate(
            ['email' => 'superadmin@es.com'],
            [
                'name' => 'Super Admin (Prodi ES)',
                'password' => Hash::make('password'),
                'role' => 'superadmin',
                'prodi' => 'Ekonomi Syariah',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@es.com'],
            [
                'name' => 'Admin Akademik ES',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'prodi' => 'Ekonomi Syariah',
                'is_admin' => true,
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@es.com'],
            [
                'name' => 'Dr. Rina Fatimah, S.E.I., M.E.',
                'password' => Hash::make('password'),
                'role' => 'dosen',
                'nip' => '199204042018041004',
                'prodi' => 'Ekonomi Syariah',
                'is_admin' => false,
            ]
        );

        User::updateOrCreate(
            ['email' => 'mahasiswa@es.com'],
            [
                'name' => 'Dimas Saputra (Mahasiswa ES)',
                'password' => Hash::make('password'),
                'role' => 'mahasiswa',
                'nim' => '210404004',
                'prodi' => 'Ekonomi Syariah',
                'semester' => 5,
                'kelas' => 'ES-5C',
                'is_admin' => false,
            ]
        );
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Jobdesk;
use App\Models\Salary;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Note: Roles, Jobdesks, Holidays, and SystemSettings 
        // are already seeded in their respective migrations

        // Create Admin User
        $adminRole = Role::where('nama', 'admin')->first();
        $backendJobdesk = Jobdesk::where('nama', 'Backend Developer')->first();

        $admin = User::create([
            'nik_npwp' => '1234567890123456',
            'nama' => 'Super Admin',
            'email' => 'admin@gapinode.com',
            'password' => Hash::make('password'),
            'phone' => '08123456789',
            'role_id' => $adminRole->id,
            'jobdesk_id' => $backendJobdesk->id,
            'pangkat' => 'Manager',
            'hire_date' => now()->subYears(2),
            'status_karyawan' => 'aktif',
        ]);

        Salary::create([
            'user_id' => $admin->id,
            'amount' => 15000000,
            'effective_from' => $admin->hire_date,
        ]);

        // Create Boss User
        $bossRole = Role::where('nama', 'boss')->first();
        $pmJobdesk = Jobdesk::where('nama', 'Project Manager')->first();

        $boss = User::create([
            'nik_npwp' => '9876543210987654',
            'nama' => 'Boss Manager',
            'email' => 'boss@gapinode.com',
            'password' => Hash::make('password'),
            'phone' => '08198765432',
            'role_id' => $bossRole->id,
            'jobdesk_id' => $pmJobdesk->id,
            'pangkat' => 'Senior Manager',
            'hire_date' => now()->subYears(3),
            'status_karyawan' => 'aktif',
        ]);

        Salary::create([
            'user_id' => $boss->id,
            'amount' => 20000000,
            'effective_from' => $boss->hire_date,
        ]);

        // Create Sample Pegawai Users
        $pegawaiRole = Role::where('nama', 'pegawai')->first();
        $frontendJobdesk = Jobdesk::where('nama', 'Frontend Developer')->first();
        $devopsJobdesk = Jobdesk::where('nama', 'DevOps Engineer')->first();

        $pegawai1 = User::create([
            'nik_npwp' => '1111111111111111',
            'nama' => 'John Developer',
            'email' => 'john@gapinode.com',
            'password' => Hash::make('password'),
            'phone' => '08111111111',
            'role_id' => $pegawaiRole->id,
            'jobdesk_id' => $backendJobdesk->id,
            'pangkat' => 'Senior',
            'hire_date' => now()->subYear(),
            'status_karyawan' => 'aktif',
            'nikah' => 'menikah',
            'jumlah_keluarga' => 3,
        ]);

        Salary::create([
            'user_id' => $pegawai1->id,
            'amount' => 8000000,
            'effective_from' => $pegawai1->hire_date,
        ]);

        $pegawai2 = User::create([
            'nik_npwp' => '2222222222222222',
            'nama' => 'Jane Frontend',
            'email' => 'jane@gapinode.com',
            'password' => Hash::make('password'),
            'phone' => '08122222222',
            'role_id' => $pegawaiRole->id,
            'jobdesk_id' => $frontendJobdesk->id,
            'pangkat' => 'Junior',
            'hire_date' => now()->subMonths(6),
            'status_karyawan' => 'aktif',
            'nikah' => 'belum_menikah',
        ]);

        Salary::create([
            'user_id' => $pegawai2->id,
            'amount' => 6000000,
            'effective_from' => $pegawai2->hire_date,
        ]);

        $pegawai3 = User::create([
            'nik_npwp' => '3333333333333333',
            'nama' => 'Bob DevOps',
            'email' => 'bob@gapinode.com',
            'password' => Hash::make('password'),
            'phone' => '08133333333',
            'role_id' => $pegawaiRole->id,
            'jobdesk_id' => $devopsJobdesk->id,
            'pangkat' => 'Lead',
            'hire_date' => now()->subYears(2),
            'status_karyawan' => 'aktif',
            'nikah' => 'menikah',
            'jumlah_keluarga' => 2,
        ]);

        Salary::create([
            'user_id' => $pegawai3->id,
            'amount' => 12000000,
            'effective_from' => $pegawai3->hire_date,
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📧 Test Accounts:');
        $this->command->info('Admin: admin@gapinode.com / password');
        $this->command->info('Boss: boss@gapinode.com / password');
        $this->command->info('Pegawai 1: john@gapinode.com / password');
        $this->command->info('Pegawai 2: jane@gapinode.com / password');
        $this->command->info('Pegawai 3: bob@gapinode.com / password');
    }
}

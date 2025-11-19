<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('👤 Creando usuarios...');

        // ============================================
        // SUPER USUARIO - ADMINISTRADOR GENERAL
        // ============================================
        Usuario::firstOrCreate(['correo' => 'admin@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Administrador SGCS',
            'contrasena_hash' => Hash::make('admin123'),
            'correo_verificado_en' => now(),
        ]);

        // Gestor de Configuración
        Usuario::firstOrCreate(['correo' => 'scm@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Carlos Méndez - SCM Manager',
            'contrasena_hash' => Hash::make('scm123'),
            'correo_verificado_en' => now(),
        ]);

        // Administrador CCB
        Usuario::firstOrCreate(['correo' => 'ccb@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Ana López - CCB Admin',
            'contrasena_hash' => Hash::make('ccb123'),
            'correo_verificado_en' => now(),
        ]);

        // Product Owner
        Usuario::firstOrCreate(['correo' => 'po@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'María González - Product Owner',
            'contrasena_hash' => Hash::make('po123'),
            'correo_verificado_en' => now(),
        ]);

        // Scrum Master
        Usuario::firstOrCreate(['correo' => 'sm@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Roberto Castillo - Scrum Master',
            'contrasena_hash' => Hash::make('sm123'),
            'correo_verificado_en' => now(),
        ]);

        // Desarrolladores
        Usuario::firstOrCreate(['correo' => 'dev1@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Luis Hernández - Dev Senior',
            'contrasena_hash' => Hash::make('dev123'),
            'correo_verificado_en' => now(),
        ]);

        Usuario::firstOrCreate(['correo' => 'dev2@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Carmen Ruiz - Developer',
            'contrasena_hash' => Hash::make('dev123'),
            'correo_verificado_en' => now(),
        ]);

        Usuario::firstOrCreate(['correo' => 'dev3@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Diego Morales - Developer',
            'contrasena_hash' => Hash::make('dev123'),
            'correo_verificado_en' => now(),
        ]);

        // QA
        Usuario::firstOrCreate(['correo' => 'qa@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Patricia Vega - QA Lead',
            'contrasena_hash' => Hash::make('qa123'),
            'correo_verificado_en' => now(),
        ]);

        // Project Manager
        Usuario::firstOrCreate(['correo' => 'pm@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Fernando Sánchez - Project Manager',
            'contrasena_hash' => Hash::make('pm123'),
            'correo_verificado_en' => now(),
        ]);

        // Arquitecto
        Usuario::firstOrCreate(['correo' => 'arch@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Alberto Jiménez - Arquitecto',
            'contrasena_hash' => Hash::make('arch123'),
            'correo_verificado_en' => now(),
        ]);

        // Analista
        Usuario::firstOrCreate(['correo' => 'analyst@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Laura Martínez - Analista',
            'contrasena_hash' => Hash::make('analyst123'),
            'correo_verificado_en' => now(),
        ]);

        // Tester
        Usuario::firstOrCreate(['correo' => 'tester@sgcs.com'], [
            'id' => Str::uuid()->toString(),
            'nombre_completo' => 'Ricardo Pérez - Tester',
            'contrasena_hash' => Hash::make('test123'),
            'correo_verificado_en' => now(),
        ]);

        $this->command->info("   ✅ Usuarios: " . Usuario::count());
        $this->command->info('   📧 SUPER USUARIO: admin@sgcs.com / admin123');
    }
}

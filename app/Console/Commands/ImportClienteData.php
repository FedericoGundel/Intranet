<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportClienteData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:cliente-data {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importar datos del cliente desde archivo SQL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("El archivo {$file} no existe.");
            return 1;
        }

        $this->info("Leyendo archivo: {$file}");

        // Leer el archivo SQL
        $sqlContent = file_get_contents($file);

        // Dividir en líneas y reconstruir statements completos
        $lines = explode("\n", $sqlContent);
        $statements = [];
        $currentStatement = '';

        foreach ($lines as $line) {
            $line = trim($line);

            // Saltar comentarios y líneas vacías
            if (empty($line) || strpos($line, '--') === 0 || strpos($line, '/*') === 0) {
                continue;
            }

            $currentStatement .= $line . ' ';

            // Si la línea termina con ;, tenemos un statement completo
            if (substr($line, -1) === ';') {
                $statements[] = trim($currentStatement);
                $currentStatement = '';
            }
        }

        $imported = 0;
        $errors = 0;

        $this->info('Procesando ' . count($statements) . ' statements...');

        foreach ($statements as $statement) {
            // Solo procesar INSERT statements
            if (strpos($statement, 'INSERT INTO') === 0) {
                try {
                    // Deshabilitar verificaciones de claves foráneas temporalmente
                    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');
                    DB::statement($statement);
                    DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
                    $imported++;

                    // Mostrar progreso cada 10 imports
                    if ($imported % 10 === 0) {
                        $this->info("Importados: {$imported}");
                    }
                } catch (\Exception $e) {
                    $errors++;
                    $this->warn('Error en statement: ' . substr($statement, 0, 100) . '...');
                    $this->warn('Error: ' . $e->getMessage());
                }
            }
        }

        $this->info("\nResumen:");
        $this->info("Importados: {$imported}");
        $this->info("Errores: {$errors}");

        return 0;
    }
}

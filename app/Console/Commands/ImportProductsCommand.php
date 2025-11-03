<?php

namespace App\Console\Commands;

use App\Enums\ProductCategory;
use App\Models\Product;
use Illuminate\Console\Command;

class ImportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-products-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from CSV files';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->importBatteries();
        $this->importConnectors();
        $this->importPanels();
        
        $this->info('Products imported successfully!');
    }

    private function importBatteries(): void
    {
        $csv = array_map('str_getcsv', file(storage_path('app/batteries.csv')));
        array_shift($csv); // Remove header row

        foreach ($csv as $row) {
            Product::create([
                'id' => $row[0],
                'name' => $row[1],
                'manufacturer' => $row[2],
                'price' => $row[3],
                'capacity' => $row[4],
                'description' => $row[5],
                'category' => ProductCategory::BATTERY,
            ]);
        }
    }

    private function importConnectors(): void
    {
        $csv = array_map('str_getcsv', file(storage_path('app/connectors.csv')));
        array_shift($csv);

        foreach ($csv as $row) {
            Product::create([
                'id' => $row[0],
                'name' => $row[1],
                'manufacturer' => $row[2],
                'price' => $row[3],
                'connector_type' => $row[4],
                'description' => $row[5],
                'category' => ProductCategory::CONNECTOR,
            ]);
        }
    }

    private function importPanels(): void
    {
        $csv = array_map('str_getcsv', file(storage_path('app/solar_panels.csv')));
        array_shift($csv);

        foreach ($csv as $row) {
            Product::create([
                'id' => $row[0],
                'name' => $row[1],
                'manufacturer' => $row[2],
                'price' => $row[3],
                'power_output' => $row[4],
                'description' => $row[5],
                'category' => ProductCategory::PANEL,
            ]);
        }
    }
}

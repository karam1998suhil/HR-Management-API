<?php


namespace App\Console\Commands;

use App\Models\Employee;
use Faker\Factory as Faker;
use Illuminate\Console\Command;


class InsertEmployees extends Command
{
    protected $signature   = 'employees:insert {count : Number of employees to insert}';
    protected $description = 'Insert a given number of fake employee records with a progress bar';

    public function handle(): void
    {
        $count = (int) $this->argument('count');

        if ($count <= 0) {
            $this->error('Count must be greater than 0.');
            return;
        }

        // make sure a founder exists before inserting employees
        $founder = Employee::where('is_founder', true)->first();

        if (!$founder) {
            $this->warn('No founder found. Creating one first...');

            $founder = Employee::create([
                'name'       => 'Company Founder',
                'email'      => 'founder@company.com',
                'salary'     => 15000,
                'manager_id' => null,
                'is_founder' => true,
            ]);

            $this->info("Founder created: {$founder->name}");
        }

        $faker     = Faker::create();
        $inserted  = 0;
        $failed    = 0;

        $this->info("Inserting {$count} employee(s)...");
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {

            // pick a random existing employee as manager
            $manager = Employee::inRandomOrder()->first();

            try {
                Employee::create([
                    'name'       => $faker->name(),
                    'email'      => $faker->unique()->safeEmail(),
                    'salary'     => $faker->numberBetween(3000, 20000),
                    'manager_id' => $manager->id,
                    'is_founder' => false,
                ]);

                $inserted++;

            } catch (\Exception $e) {
                $failed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done! {$inserted} inserted, {$failed} failed.");
    }
}
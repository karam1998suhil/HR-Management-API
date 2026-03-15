<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\Position;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding users...');
        User::firstOrCreate(
            ['email' => 'admin@hr.com'],
            [
                'name'     => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        $this->command->info('Seeding positions...');
        $positionTitles = [
            'Software Engineer',
            'Senior Software Engineer',
            'Product Manager',
            'HR Manager',
            'Data Analyst',
            'DevOps Engineer',
            'UI/UX Designer',
            'QA Engineer',
        ];

        foreach ($positionTitles as $title) {
            Position::firstOrCreate(
                ['title' => $title],
                ['description' => fake()->sentence()]
            );
        }

        $positions = Position::all();

        $this->command->info('Seeding founder...');
        $founder = Employee::firstOrCreate(
            ['email' => 'founder@company.com'],
            [
                'name'        => 'John Founder',
                'salary'      => 15000,
                'manager_id'  => null,
                'is_founder'  => true,
                'position_id' => $positions->random()->id,
            ]
        );

        $this->command->info('Seeding managers...');
        $managerData = [
            ['name' => 'Alice Manager', 'email' => 'alice@company.com', 'salary' => 10000],
            ['name' => 'Bob Manager',   'email' => 'bob@company.com',   'salary' => 9500],
            ['name' => 'Carol Manager', 'email' => 'carol@company.com', 'salary' => 9000],
        ];

        $managers = collect();
        foreach ($managerData as $data) {
            $managers->push(Employee::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'        => $data['name'],
                    'salary'      => $data['salary'],
                    'manager_id'  => $founder->id,
                    'is_founder'  => false,
                    'position_id' => $positions->random()->id,
                ]
            ));
        }

        $this->command->info('Seeding employees...');
        $employeeData = [
            ['name' => 'David Lee',    'email' => 'david@company.com',   'salary' => 6000],
            ['name' => 'Eva Brown',    'email' => 'eva@company.com',     'salary' => 5500],
            ['name' => 'Frank Wilson', 'email' => 'frank@company.com',   'salary' => 5000],
            ['name' => 'Grace Kim',    'email' => 'grace@company.com',   'salary' => 4800],
            ['name' => 'Henry Davis',  'email' => 'henry@company.com',   'salary' => 4500],
            ['name' => 'Isla Scott',   'email' => 'isla@company.com',    'salary' => 4200],
            ['name' => 'Jack Taylor',  'email' => 'jack@company.com',    'salary' => 4000],
            ['name' => 'Karen White',  'email' => 'karen@company.com',   'salary' => 3800],
            ['name' => 'Liam Harris',  'email' => 'liam@company.com',    'salary' => 3600],
            ['name' => 'Mia Martin',   'email' => 'mia@company.com',     'salary' => 3500],
            ['name' => 'Noah Garcia',  'email' => 'noah@company.com',    'salary' => 3400],
            ['name' => 'Olivia Hall',  'email' => 'olivia@company.com',  'salary' => 3300],
            ['name' => 'Paul Allen',   'email' => 'paul@company.com',    'salary' => 3200],
            ['name' => 'Quinn Young',  'email' => 'quinn@company.com',   'salary' => 3100],
            ['name' => 'Rachel King',  'email' => 'rachel@company.com',  'salary' => 3000],
        ];

        foreach ($employeeData as $data) {
            Employee::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name'        => $data['name'],
                    'salary'      => $data['salary'],
                    'manager_id'  => $managers->random()->id,
                    'is_founder'  => false,
                    'position_id' => $positions->random()->id,
                ]
            );
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->table(
            ['Item', 'Count'],
            [
                ['Users',     User::count()],
                ['Positions', Position::count()],
                ['Employees', Employee::count()],
            ]
        );
    }
}
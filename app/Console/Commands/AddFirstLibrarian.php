<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class AddFirstLibrarian extends Command
{
    protected $signature = 'app:add-first-librarian';

    protected $description = 'Command to create the first librarian';

    public function handle()
    {
        $firstName = $this->ask('Enter the first name of the librarian');
        $lastName = $this->ask('Enter the last name of the librarian');
        $email = $this->ask('Enter the email address of the librarian');
        $username = $this->ask('Enter the username of the librarian');
        $jmbg = $this->ask('Enter the JMBG of the librarian');

        $validator = Validator::make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'username' => $username,
            'jmbg' => $jmbg,
        ], [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'username' => ['required', 'string', 'unique:users,username'],
            'jmbg' => ['required', 'digits:13', 'unique:users,jmbg'],
        ]);

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        $librarian = new User;
        $librarian->first_name = $firstName;
        $librarian->last_name = $lastName;
        $librarian->email = $email;
        $librarian->username = $username;
        $librarian->jmbg = $jmbg;
        $librarian->role_id = User::ROLE_LIBRARIAN;

        $librarian->save();

        $this->info('First librarian added successfully!');

        return 0;
    }
}

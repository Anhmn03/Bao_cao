<?php

namespace App\Imports;

use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Session;

class UsersImport implements ToCollection
{
    protected $existingEmails = []; // Array to store existing emails

    public function collection(Collection $rows)
    {
        $dataRows = $rows->skip(1); // Skip header row

        foreach ($dataRows as $row) {
            try {
                // Find department_id based on department name
                $department = Department::where('name', $row[4])->first();

                if (!$department) {
                    throw new \Exception("Department not found: " . $row[4]);
                }

                // Check for existing user by email
                $user = User::where('email', $row[1])->first();

                // Get the ID of the user who is updating (assuming from session)
                $updatedById = auth()->id();

                if ($user) {
                    // If email already exists, add to existing emails array
                    $this->existingEmails[] = $row[1];
                    continue; // Skip this record
                } else {
                    // Create new user
                    User::create([
                        'name' => $row[0],
                        'email' => $row[1],
                        'password' => bcrypt($row[2]),
                        'phone_number' => $row[3],
                        'department_id' => $department->id,
                        'position' => $row[5],
                        'status' => 1,
                        'role' => 2,
                        'created_at' => now(),
                        'created_by' => $updatedById,
                    ]);
                }
            } catch (\Exception $e) {
                // Log the error or handle accordingly
                // \Log::error("Error importing row: " . json_encode($row) . " - " . $e->getMessage());
                continue; // Skip to next row
            }
        }

        // If there are any existing emails, store them in session
        if (!empty($this->existingEmails)) {
            // Flash existing emails to the session for display in the view
            Session::flash('error', 'Những email đã tồn tại: ' . implode(', ', $this->existingEmails));
        } else {
            // Flash success message
            Session::flash('success', 'Nhập khẩu thành công!');
        }
    }
}

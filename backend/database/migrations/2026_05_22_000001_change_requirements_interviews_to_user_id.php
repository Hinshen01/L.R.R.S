<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite doesn't support renaming columns with foreign keys easily,
        // so we'll use raw SQL to alter the tables
        
        // Disable foreign key checks temporarily
        DB::statement('PRAGMA foreign_keys=OFF');
        
        try {
            // Recreate requirements table with user_id instead of applicant_id
            DB::statement('
                CREATE TABLE requirements_new (
                    id INTEGER PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    document_name VARCHAR NOT NULL,
                    status VARCHAR NOT NULL DEFAULT "missing",
                    file_path VARCHAR,
                    expiration_date DATE,
                    created_at DATETIME,
                    updated_at DATETIME,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ');
            
            // Copy data
            DB::statement('
                INSERT INTO requirements_new (id, user_id, document_name, status, file_path, expiration_date, created_at, updated_at)
                SELECT id, applicant_id, document_name, status, file_path, expiration_date, created_at, updated_at FROM requirements
            ');
            
            // Drop old table and rename new one
            DB::statement('DROP TABLE requirements');
            DB::statement('ALTER TABLE requirements_new RENAME TO requirements');
            
            // Recreate interviews table with user_id instead of applicant_id
            DB::statement('
                CREATE TABLE interviews_new (
                    id INTEGER PRIMARY KEY,
                    user_id INTEGER NOT NULL,
                    interview_date DATETIME NOT NULL,
                    company_name VARCHAR NOT NULL,
                    venue VARCHAR NOT NULL,
                    status VARCHAR NOT NULL DEFAULT "scheduled",
                    created_at DATETIME,
                    updated_at DATETIME,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                )
            ');
            
            // Copy data
            DB::statement('
                INSERT INTO interviews_new (id, user_id, interview_date, company_name, venue, status, created_at, updated_at)
                SELECT id, applicant_id, interview_date, company_name, venue, status, created_at, updated_at FROM interviews
            ');
            
            // Drop old table and rename new one
            DB::statement('DROP TABLE interviews');
            DB::statement('ALTER TABLE interviews_new RENAME TO interviews');
            
        } finally {
            // Re-enable foreign key checks
            DB::statement('PRAGMA foreign_keys=ON');
        }
    }

    public function down(): void
    {
        DB::statement('PRAGMA foreign_keys=OFF');
        
        try {
            // Recreate requirements table with applicant_id
            DB::statement('
                CREATE TABLE requirements_new (
                    id INTEGER PRIMARY KEY,
                    applicant_id INTEGER NOT NULL,
                    document_name VARCHAR NOT NULL,
                    status VARCHAR NOT NULL DEFAULT "missing",
                    file_path VARCHAR,
                    expiration_date DATE,
                    created_at DATETIME,
                    updated_at DATETIME,
                    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
                )
            ');
            
            DB::statement('
                INSERT INTO requirements_new (id, applicant_id, document_name, status, file_path, expiration_date, created_at, updated_at)
                SELECT id, user_id, document_name, status, file_path, expiration_date, created_at, updated_at FROM requirements
            ');
            
            DB::statement('DROP TABLE requirements');
            DB::statement('ALTER TABLE requirements_new RENAME TO requirements');
            
            // Recreate interviews table with applicant_id
            DB::statement('
                CREATE TABLE interviews_new (
                    id INTEGER PRIMARY KEY,
                    applicant_id INTEGER NOT NULL,
                    interview_date DATETIME NOT NULL,
                    company_name VARCHAR NOT NULL,
                    venue VARCHAR NOT NULL,
                    status VARCHAR NOT NULL DEFAULT "scheduled",
                    created_at DATETIME,
                    updated_at DATETIME,
                    FOREIGN KEY (applicant_id) REFERENCES applicants(id) ON DELETE CASCADE
                )
            ');
            
            DB::statement('
                INSERT INTO interviews_new (id, applicant_id, interview_date, company_name, venue, status, created_at, updated_at)
                SELECT id, user_id, interview_date, company_name, venue, status, created_at, updated_at FROM interviews
            ');
            
            DB::statement('DROP TABLE interviews');
            DB::statement('ALTER TABLE interviews_new RENAME TO interviews');
            
        } finally {
            DB::statement('PRAGMA foreign_keys=ON');
        }
    }
};

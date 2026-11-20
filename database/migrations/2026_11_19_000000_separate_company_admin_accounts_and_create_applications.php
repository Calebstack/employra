<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $employerIds = DB::table('users')->where('role', 'employer')->pluck('id');
        $ownerAccounts = collect();
        if (Schema::hasTable('company_user')) {
            $ownerAccounts = DB::table('company_user')
                ->join('users', 'users.id', '=', 'company_user.user_id')
                ->where('users.role', 'employer')
                ->where('company_user.member_role', 'owner')
                ->orderBy('company_user.company_id')
                ->orderBy('company_user.user_id')
                ->get(['company_user.company_id', 'company_user.user_id']);

            if ($ownerAccounts->isEmpty() && $employerIds->isNotEmpty()
                && Schema::hasColumn('companies', 'legacy_user_id')) {
                $ownerAccounts = DB::table('companies')
                    ->join('users', 'users.id', '=', 'companies.legacy_user_id')
                    ->where('users.role', 'employer')
                    ->orderBy('companies.id')
                    ->get(['companies.id as company_id', 'users.id as user_id']);
            }
        }
        $ownerUserIds = $ownerAccounts->pluck('user_id')->unique();

        if ($employerIds->diff($ownerUserIds)->isNotEmpty()
            || $ownerAccounts->groupBy('company_id')->contains(fn ($owners) => $owners->count() > 1)
            || $ownerAccounts->groupBy('user_id')->contains(fn ($companies) => $companies->count() > 1)) {
            throw new RuntimeException('Each employer account must own exactly one company before account separation can proceed.');
        }

        if (! Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email', 190)->unique();
                $table->string('password');
                $table->string('status', 20)->default('active');
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->string('remember_token')->nullable();
                $table->unsignedBigInteger('legacy_user_id')->nullable()->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasColumn('companies', 'email')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->string('contact_name', 120)->nullable();
                $table->string('email', 190)->nullable()->unique();
                $table->string('password')->nullable();
                $table->string('account_status', 20)->default('active');
                $table->timestamp('email_verified_at')->nullable();
                $table->timestamp('last_login_at')->nullable();
                $table->string('remember_token')->nullable();
                $table->unsignedBigInteger('legacy_user_id')->nullable()->unique();
            });
        }

        foreach ($ownerAccounts as $account) {
            $user = DB::table('users')->where('id', $account->user_id)->first();
            DB::table('companies')->where('id', $account->company_id)->update([
                'contact_name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'email_verified_at' => $user->email_verified_at,
                'last_login_at' => $user->last_login_at,
                'remember_token' => $user->remember_token,
                'legacy_user_id' => $user->id,
            ]);
        }

        $adminUsers = DB::table('users')->where('role', 'admin')->get();
        foreach ($adminUsers as $user) {
            DB::table('admins')->updateOrInsert(
                ['id' => $user->id],
                [
                    'name' => $user->name,
                    'email' => $user->email,
                    'password' => $user->password,
                    'status' => $user->status,
                    'email_verified_at' => $user->email_verified_at,
                    'last_login_at' => $user->last_login_at,
                    'remember_token' => $user->remember_token,
                    'legacy_user_id' => $user->id,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            );
        }

        $hasUserIdColumn = Schema::hasColumn('employer_jobs', 'user_id');
        $hasUserIdForeign = $this->hasForeignKey('employer_jobs', 'user_id');
        $hasModeratedByForeign = $this->hasForeignKey('employer_jobs', 'moderated_by');
        if ($hasUserIdColumn || $hasUserIdForeign || $hasModeratedByForeign) {
            Schema::table('employer_jobs', function (Blueprint $table) use ($hasUserIdColumn, $hasUserIdForeign, $hasModeratedByForeign) {
                if ($hasUserIdForeign) {
                    $table->dropForeign(['user_id']);
                }
                if ($hasModeratedByForeign) {
                    $table->dropForeign(['moderated_by']);
                }
                if ($hasUserIdColumn) {
                    $table->dropColumn('user_id');
                }
            });
        }
        foreach ([
            'reports' => 'resolved_by',
            'platform_settings' => 'updated_by',
            'admin_activity_logs' => 'admin_id',
        ] as $tableName => $column) {
            if ($this->hasForeignKey($tableName, $column)) {
                Schema::table($tableName, fn (Blueprint $table) => $table->dropForeign([$column]));
            }
        }

        $movedUserIds = $employerIds->merge($adminUsers->pluck('id'))->unique()->values();
        if ($movedUserIds->isNotEmpty()) {
            DB::table('reports')->whereIn('reported_by', $movedUserIds)->update(['reported_by' => null]);
            DB::table('sessions')->whereIn('user_id', $movedUserIds)->delete();
        }

        if (Schema::hasTable('company_user')) {
            DB::table('company_user')->delete();
            foreach (Schema::getForeignKeys('company_user') as $foreignKey) {
                Schema::table('company_user', function (Blueprint $table) use ($foreignKey) {
                    $table->dropForeign($foreignKey['columns']);
                });
            }
        }
        DB::table('users')->whereIn('id', $movedUserIds)->delete();

        foreach ([
            'employer_jobs' => 'moderated_by',
            'reports' => 'resolved_by',
            'platform_settings' => 'updated_by',
            'admin_activity_logs' => 'admin_id',
        ] as $tableName => $column) {
            if (! $this->hasForeignKey($tableName, $column)) {
                Schema::table($tableName, function (Blueprint $table) use ($column) {
                    $table->foreign($column)->references('id')->on('admins')->nullOnDelete();
                });
            }
        }

        if (! Schema::hasTable('job_applications')) {
            Schema::create('job_applications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('job_id')->constrained('employer_jobs')->cascadeOnDelete();
                $table->foreignId('cv_id')->nullable()->constrained('cvs')->nullOnDelete();
                $table->string('status', 30)->default('submitted')->index();
                $table->text('cover_note')->nullable();
                $table->timestamps();
                $table->unique(['user_id', 'job_id']);
                $table->index(['job_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');

        $companyUserIds = [];
        foreach (DB::table('companies')->orderBy('id')->get() as $company) {
            $email = $company->email ?: 'company-'.$company->id.'@migrated.invalid';
            $userId = $company->legacy_user_id;
            $attributes = [
                'name' => $company->contact_name ?: $company->name,
                'email' => $email,
                'password' => $company->password ?: Hash::make(Str::random(48)),
                'role' => 'employer',
                'status' => 'active',
                'last_login_at' => $company->last_login_at,
                'email_verified_at' => $company->email_verified_at,
                'created_at' => $company->created_at,
                'updated_at' => $company->updated_at,
            ];
            if ($userId !== null) {
                $attributes['id'] = $userId;
                DB::table('users')->insert($attributes);
            } else {
                $userId = DB::table('users')->insertGetId($attributes);
            }
            $companyUserIds[$company->id] = $userId;
        }

        $adminUserIds = [];
        foreach (DB::table('admins')->orderBy('id')->get() as $admin) {
            $attributes = [
                'name' => $admin->name,
                'email' => $admin->email,
                'password' => $admin->password,
                'role' => 'admin',
                'status' => $admin->status,
                'last_login_at' => $admin->last_login_at,
                'email_verified_at' => $admin->email_verified_at,
                'created_at' => $admin->created_at,
                'updated_at' => $admin->updated_at,
            ];
            if ($admin->legacy_user_id !== null) {
                $attributes['id'] = $admin->legacy_user_id;
                DB::table('users')->insert($attributes);
                $adminUserIds[$admin->id] = $admin->legacy_user_id;
            } else {
                $adminUserIds[$admin->id] = DB::table('users')->insertGetId($attributes);
            }
        }

        Schema::table('employer_jobs', function (Blueprint $table) {
            $table->dropForeign(['moderated_by']);
        });
        Schema::table('reports', function (Blueprint $table) {
            $table->dropForeign(['resolved_by']);
        });
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->dropForeign(['updated_by']);
        });
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->dropForeign(['admin_id']);
        });

        foreach ($adminUserIds as $adminId => $userId) {
            DB::table('employer_jobs')->where('moderated_by', $adminId)->update(['moderated_by' => $userId]);
            DB::table('reports')->where('resolved_by', $adminId)->update(['resolved_by' => $userId]);
            DB::table('platform_settings')->where('updated_by', $adminId)->update(['updated_by' => $userId]);
            DB::table('admin_activity_logs')->where('admin_id', $adminId)->update(['admin_id' => $userId]);
        }

        Schema::table('employer_jobs', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
        if (! Schema::hasTable('company_user')) {
            Schema::create('company_user', function (Blueprint $table) {
                $table->foreignId('company_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('member_role', 20)->default('owner');
                $table->unique(['company_id', 'user_id']);
            });
        } else {
            DB::table('company_user')->delete();
        }
        foreach ($companyUserIds as $companyId => $userId) {
            DB::table('company_user')->insert([
                'company_id' => $companyId,
                'user_id' => $userId,
                'member_role' => 'owner',
            ]);
            DB::table('employer_jobs')->where('company_id', $companyId)->update(['user_id' => $userId]);
        }
        foreach (['company_id' => 'companies', 'user_id' => 'users'] as $column => $referencedTable) {
            if (! $this->hasForeignKey('company_user', $column)) {
                Schema::table('company_user', function (Blueprint $table) use ($column, $referencedTable) {
                    $table->foreign($column)->references('id')->on($referencedTable)->cascadeOnDelete();
                });
            }
        }

        Schema::table('employer_jobs', function (Blueprint $table) {
            $table->foreign('moderated_by')->references('id')->on('users')->nullOnDelete();
        });
        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('resolved_by')->references('id')->on('users')->nullOnDelete();
        });
        Schema::table('platform_settings', function (Blueprint $table) {
            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
        Schema::table('admin_activity_logs', function (Blueprint $table) {
            $table->foreign('admin_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->dropUnique(['legacy_user_id']);
            $table->dropColumn([
                'contact_name',
                'email',
                'password',
                'account_status',
                'email_verified_at',
                'last_login_at',
                'remember_token',
                'legacy_user_id',
            ]);
        });
        Schema::dropIfExists('admins');
    }

    private function hasForeignKey(string $table, string $column): bool
    {
        foreach (Schema::getForeignKeys($table) as $foreignKey) {
            if (in_array($column, $foreignKey['columns'], true)) {
                return true;
            }
        }

        return false;
    }
};

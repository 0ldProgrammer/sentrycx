<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCnxEmployees extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cnx_employees', function (Blueprint $table) {
            $table->id();			
            $table->timestamps();			
			$table->string('firstname')->nullable(true);
			$table->string('lastname')->nullable(true);
			$table->string('email')->nullable(true);
			$table->string('middle_name')->nullable(true);
			$table->string('employee_number')->nullable(true);
			$table->string('previous_payroll_id')->nullable(true);
			$table->string('gender')->nullable(true);
			$table->string('person_type')->nullable(true);
			$table->string('worker_category')->nullable(true);
			$table->string('location_type')->nullable(true);
			$table->string('assignment_category')->nullable(true);
			$table->string('working_hours_frequency')->nullable(true);
			$table->string('country')->nullable(true);
			$table->dateTime('hire_date')->nullable(true);
			$table->dateTime('legal_employer_hire_date')->nullable(true);
			$table->string('salary_frequency')->nullable(true);
			$table->string('cnx_bu')->nullable(true);
			$table->string('local_currency')->nullable(true);
			$table->string('location')->nullable(true);
			$table->string('location_identifier')->nullable(true);
			$table->string('position_number')->nullable(true);
			$table->string('position_description')->nullable(true);
			$table->string('cost_center')->nullable(true);
			$table->string('dept_id')->nullable(true);
			$table->string('company')->nullable(true);
			$table->dateTime('acquisition_date')->nullable(true);
			$table->string('job_level')->nullable(true);
			$table->string('new_portfolio')->nullable(true);
			$table->string('programme_msa')->nullable(true);
			$table->string('supervisor_id')->nullable(true);
			$table->string('supervisor_full_name')->nullable(true);
			$table->string('supervisor_email_id')->nullable(true);
			$table->string('cost_center_leader')->nullable(true);
			$table->string('status')->nullable(true);
			$table->string('working_hours')->nullable(true);
			$table->string('assignment_number')->nullable(true);
			$table->string('payroll_group')->nullable(true);
			$table->string('comp_code')->nullable(true);
			$table->string('primary_address_city')->nullable(true);
			$table->string('location_name')->nullable(true);
			$table->string('tenure')->nullable(true);
			$table->string('region')->nullable(true);
			$table->string('active_staff')->nullable(true);
			$table->string('management_level')->nullable(true);
			$table->string('job_role')->nullable(true);
			$table->string('geography_name')->nullable(true);
			$table->string('senior_executive_team')->nullable(true);
			$table->string('lob')->nullable(true);
			$table->string('msa_client')->nullable(true);
			$table->string('job_family_group')->nullable(true);
			$table->string('cost_center_bu')->nullable(true);
			$table->string('job_family')->nullable(true);
			$table->string('compensation_grade')->nullable(true);
			$table->string('job_profile')->nullable(true);
			$table->string('building')->nullable(true);
			$table->string('floor')->nullable(true);
			$table->dateTime('resignation_date')->nullable(true);
			$table->string('sam_account_name')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cnx_employees');
    }
}
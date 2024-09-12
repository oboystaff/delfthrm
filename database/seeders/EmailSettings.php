<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;
use App\Models\EmailTemplateLang;
use App\Models\UserEmailTemplate;

class EmailSettings extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $leaveRequestTemplate = EmailTemplate::firstOrCreate(
            ['slug' => 'leave_request'],
            [
                'name' => 'Leave Request',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Insert or get 'releave_status' email template
        $releaveStatusTemplate = EmailTemplate::firstOrCreate(
            ['slug' => 'releave_status'],
            [
                'name' => 'Releave Status',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $leaveRequestTemplateId = $leaveRequestTemplate->id;
        $releaveStatusTemplateId = $releaveStatusTemplate->id;

        // Insert or get related 'email_template_langs' for 'leave_request'
        EmailTemplateLang::firstOrCreate(
            [
                'parent_id' => $leaveRequestTemplateId,
                'lang' => 'en'
            ],
            [
                'subject' => 'Leave Request',
                'content' => '<p><strong>Leave Request</strong></p><p>Leave request details here.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Insert or get related 'email_template_langs' for 'releave_status'
        EmailTemplateLang::firstOrCreate(
            [
                'parent_id' => $releaveStatusTemplateId,
                'lang' => 'en'
            ],
            [
                'subject' => 'Releave Status',
                'content' => '<p><strong>Releave Status</strong></p><p>Releave status details here.</p>',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Insert for leave_request
        UserEmailTemplate::firstOrCreate(
            [
                'template_id' => $leaveRequestTemplateId,
                'user_id' => 2
            ],
            [
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Insert for releave_status
        UserEmailTemplate::firstOrCreate(
            [
                'template_id' => $releaveStatusTemplateId,
                'user_id' => 2
            ],
            [
                'is_active' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}

<?php
/**
 * Script to fix migration files and make project GitHub ready
 * Run this script once to fix all migration issues
 */

echo "🚀 Fixing Academy LMS Migration Files...\n\n";

// Define tables that need complete migration structures
$incompleteFiles = [
    'database/migrations/2023_04_09_111750_create_notifications_table.php' => [
        'type' => 'notifications',
        'columns' => [
            '$table->string(\'type\')->nullable();',
            '$table->unsignedBigInteger(\'notifiable_id\');',
            '$table->string(\'notifiable_type\');',
            '$table->text(\'data\');',
            '$table->timestamp(\'read_at\')->nullable();',
        ]
    ],
    'database/migrations/2023_04_09_111836_create_payment_histories_table.php' => [
        'type' => 'payment_histories',
        'columns' => [
            '$table->string(\'payment_type\')->nullable();',
            '$table->double(\'amount\')->nullable();',
            '$table->string(\'currency\')->nullable();',
            '$table->string(\'payment_method\')->nullable();',
            '$table->text(\'payment_details\')->nullable();',
            '$table->integer(\'status\')->default(1);',
        ]
    ],
    'database/migrations/2023_04_09_111929_create_messages_table.php' => [
        'type' => 'messages',
        'columns' => [
            '$table->unsignedBigInteger(\'sender_id\');',
            '$table->unsignedBigInteger(\'receiver_id\');',
            '$table->text(\'message\');',
            '$table->timestamp(\'read_at\')->nullable();',
        ]
    ],
    'database/migrations/2023_04_09_112008_create_payment_gateways_table.php' => [
        'type' => 'payment_gateways',
        'columns' => [
            '$table->string(\'identifier\')->unique();',
            '$table->string(\'currency\')->nullable();',
            '$table->string(\'title\')->nullable();',
            '$table->text(\'description\')->nullable();',
            '$table->text(\'keys\')->nullable();',
            '$table->integer(\'model_name\')->nullable();',
            '$table->integer(\'enabled_test_mode\')->nullable();',
            '$table->integer(\'status\')->nullable();',
            '$table->integer(\'is_addon\')->nullable();',
        ]
    ],
    'database/migrations/2023_04_09_112148_create_wishlists_table.php' => [
        'type' => 'wishlists',
        'columns' => [
            '$table->unsignedBigInteger(\'user_id\');',
            '$table->unsignedBigInteger(\'course_id\');',
        ]
    ],
    'database/migrations/2023_04_09_112905_create_blogs_table.php' => [
        'type' => 'blogs',
        'columns' => [
            '$table->unsignedBigInteger(\'user_id\')->nullable();',
            '$table->string(\'category_id\')->nullable();',
            '$table->string(\'title\')->nullable();',
            '$table->string(\'slug\')->nullable();',
            '$table->longText(\'description\')->nullable();',
            '$table->string(\'thumbnail\')->nullable();',
            '$table->string(\'banner\')->nullable();',
            '$table->text(\'keywords\')->nullable();',
            '$table->integer(\'is_popular\')->nullable();',
            '$table->integer(\'status\')->nullable();',
        ]
    ],
    'database/migrations/2023_04_09_114442_create_reviews_table.php' => [
        'type' => 'reviews',
        'columns' => [
            '$table->unsignedBigInteger(\'user_id\');',
            '$table->unsignedBigInteger(\'course_id\');',
            '$table->integer(\'rating\');',
            '$table->text(\'comment\')->nullable();',
        ]
    ],
    'database/migrations/2023_04_09_114547_create_watche_durations_table.php' => [
        'type' => 'watche_durations',
        'columns' => [
            '$table->unsignedBigInteger(\'watching_id\');',
            '$table->string(\'current_duration\')->nullable();',
            '$table->string(\'total_duration\')->nullable();',
        ]
    ],
    'database/migrations/2023_04_09_114558_create_watche_histories_table.php' => [
        'type' => 'watche_histories',
        'columns' => [
            '$table->unsignedBigInteger(\'course_id\');',
            '$table->unsignedBigInteger(\'lesson_id\');',
            '$table->unsignedBigInteger(\'user_id\');',
            '$table->string(\'current_duration\')->nullable();',
            '$table->string(\'total_duration\')->nullable();',
            '$table->integer(\'watching_id\')->nullable();',
        ]
    ],
    'database/migrations/2024_06_03_044349_create_questions_table.php' => [
        'type' => 'questions',
        'columns' => [
            '$table->unsignedBigInteger(\'quiz_id\');',
            '$table->text(\'title\');',
            '$table->integer(\'type\');',
            '$table->text(\'options\')->nullable();',
            '$table->text(\'correct_answers\');',
            '$table->integer(\'sort\')->nullable();',
        ]
    ],
    'database/migrations/2024_07_11_062654_create_team_training_packages_table.php' => [
        'type' => 'team_training_packages',
        'columns' => [
            '$table->string(\'title\');',
            '$table->text(\'description\')->nullable();',
            '$table->double(\'price\');',
            '$table->integer(\'allocation\');',
            '$table->string(\'course_privacy\');',
            '$table->integer(\'course_accessibility\');',
            '$table->string(\'package_type\');',
            '$table->integer(\'status\')->default(1);',
        ]
    ]
];

echo "📝 Found " . count($incompleteFiles) . " migration files to fix...\n\n";

foreach ($incompleteFiles as $filePath => $config) {
    $fullPath = __DIR__ . '/' . $filePath;

    if (!file_exists($fullPath)) {
        echo "❌ File not found: $filePath\n";
        continue;
    }

    $tableName = $config['type'];
    $columns = $config['columns'];

    // Read current file content
    $content = file_get_contents($fullPath);

    // Create the new up() method
    $newUpMethod = "    public function up(): void\n    {\n        Schema::create('$tableName', function (Blueprint \$table) {\n            \$table->id();\n";

    foreach ($columns as $column) {
        $newUpMethod .= "            $column\n";
    }

    $newUpMethod .= "            \$table->timestamps();\n        });\n    }";

    // Replace the old up() method
    $pattern = '/public function up\(\): void\s*\{[^}]*Schema::create\([^}]*\}\s*\}\);?\s*\}/s';
    $newContent = preg_replace($pattern, $newUpMethod, $content);

    if ($newContent && $newContent !== $content) {
        file_put_contents($fullPath, $newContent);
        echo "✅ Fixed: $filePath\n";
    } else {
        echo "⚠️  Could not fix: $filePath (manual fix needed)\n";
    }
}

echo "\n🔧 Creating .env.example file...\n";

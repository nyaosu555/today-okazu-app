<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'test@example.com')->first();

        // 1. ハンバーグ
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => 'ハンバーグ',
            'type_id' => 1,
            'recipe_url' => 'https://recipe.example.com/hamburg',
            'image_path' => 'https://github.com/user-attachments/assets/ac53b46c-08bc-4638-809e-d8ff4edbd6a7',
        ]);

        // 2. ナポリタンスパゲッティ
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => 'ナポリタンスパゲッティ',
            'type_id' => 1,
            'image_path' => 'https://github.com/user-attachments/assets/b3569a60-d7ba-48a8-8c4f-fe6f4c3483c9',
        ]);

        // 3. 肉じゃが
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => '肉じゃが',
            'type_id' => 1,
            'image_path' => 'https://github.com/user-attachments/assets/956b067e-3d38-4eb3-bb94-ccf9c8c514c6',
        ]);

        // 4. カブとエノキ煮
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => 'カブとエノキ煮',
            'type_id' => 2,
            'image_path' => 'https://github.com/user-attachments/assets/773c21ed-41cb-41d3-b70c-edd3b4d95c65',
        ]);

        // 5. フキのおかか煮
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => 'フキのおかか煮',
            'type_id' => 2,
            'image_path' => 'https://github.com/user-attachments/assets/d6569fcf-f497-49ee-a651-9ccd738736b5',
        ]);

        // 6. 大根としらすとしその醤油がけ
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => '大根としらすとしその醤油がけ',
            'type_id' => 2,
            'image_path' => 'https://github.com/user-attachments/assets/99fc99a5-b380-4671-a020-0aea0ed5edb9',
        ]);

        // 7. ポテトサラダ
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => 'ポテトサラダ',
            'type_id' => 3,
            'image_path' => 'https://github.com/user-attachments/assets/007d00c5-65d6-4939-992a-1b5b0f859497',
        ]);

        // 8. 豆腐サラダ
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => '豆腐サラダ',
            'type_id' => 3,
            'image_path' => 'https://github.com/user-attachments/assets/9a5be8a9-b53d-455f-a47e-28b93a716a37',
        ]);

        // 9. えびサラダ
        $user->menus()->create([
            'user_id' => $user->id,
            'name' => 'えびサラダ',
            'type_id' => 3,
            'recipe_url' => 'https://recipe.example.com/potatosalada',
            'image_path' => 'https://github.com/user-attachments/assets/5aa6c4f4-2ba5-4e08-8830-b980a5d1c19d',
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Song;
use App\Models\SongLeader;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::updateOrCreate(['username' => 'Jesus The Great Minister'], [
            'name' => 'Jesus The Great Minister',
            'church_name' => 'Jesus The Great Minister',
            'email' => 'jtgm@local.ichords',
            'password' => bcrypt('JTGM0000'),
            'role' => 'admin',
        ]);

        $leaders = [
            ['name' => 'Sis. Chin', 'slug' => 'sis-chin'],
            ['name' => 'Sis. Gerlie', 'slug' => 'sis-gerlie'],
            ['name' => 'Sis. Praisee', 'slug' => 'sis-praisee'],
            ['name' => 'Sis. Joy', 'slug' => 'sis-joy'],
            ['name' => 'Sis. Lolit', 'slug' => 'sis-lolit'],
            ['name' => 'Bro. Christian', 'slug' => 'bro-christian'],
        ];

        foreach ($leaders as $leader) {
            SongLeader::updateOrCreate(['slug' => $leader['slug']], $leader + ['user_id' => $owner->id]);
        }

        $songs = [
            ['title' => 'How Great Is Our God', 'artist' => 'Chris Tomlin', 'key' => 'G', 'leader' => 'sis-chin', 'lines' => [['G', 'The splendor of the King'], ['Em', 'Clothed in majesty'], ['C', 'Let all the earth rejoice'], ['D', 'All the earth rejoice'], ['G', 'How great is our God, sing with me'], ['Em', 'How great is our God, and all will see'], ['C', 'How great, how great is our God']]],
            ['title' => 'Goodness of God', 'artist' => 'Jenn Johnson', 'key' => 'G', 'leader' => 'sis-chin', 'lines' => [['G', 'I love You, Lord'], ['C', 'For Your mercy never fails me'], ['Em', 'All my days, I have been held in Your hands'], ['D', 'From the moment that I wake up'], ['G', 'Until I lay my head'], ['C', 'I will sing of the goodness of God']]],
            ['title' => 'Way Maker', 'artist' => 'Sinach', 'key' => 'E', 'leader' => 'sis-chin', 'lines' => [['E', 'You are here, moving in our midst'], ['B', 'I worship You, I worship You'], ['C#m', 'You are here, working in this place'], ['A', 'I worship You, I worship You'], ['E', 'Way maker, miracle worker'], ['B', 'Promise keeper, light in the darkness'], ['C#m', 'My God, that is who You are']]],
            ['title' => '10,000 Reasons', 'artist' => 'Matt Redman', 'key' => 'G', 'leader' => 'sis-chin', 'lines' => [['G', 'Bless the Lord, O my soul'], ['D', 'O my soul, worship His holy name'], ['Em', 'Sing like never before'], ['C', 'O my soul, I worship Your holy name']]],
            ['title' => 'Great Are You Lord', 'artist' => 'All Sons & Daughters', 'key' => 'G', 'leader' => 'sis-gerlie', 'lines' => [['G', 'You give life, You are love'], ['Em', 'You bring light to the darkness'], ['C', 'You give hope, You restore'], ['D', 'Every heart that is broken'], ['G', 'Great are You, Lord']]],
            ['title' => 'Build My Life', 'artist' => 'Housefires', 'key' => 'C', 'leader' => 'sis-gerlie', 'lines' => [['C', 'Worthy of every song we could ever sing'], ['Am', 'Worthy of all the praise we could ever bring'], ['F', 'Worthy of every breath we could ever breathe'], ['G', 'We live for You']]],
        ];

        foreach ($songs as $data) {
            $song = Song::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                ['title' => $data['title'], 'artist' => $data['artist'], 'original_key' => $data['key'], 'content' => $data['lines'], 'notes' => null, 'user_id' => $owner->id]
            );
            $song->leaders()->syncWithoutDetaching([SongLeader::where('slug', $data['leader'])->value('id')]);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\SongLeader;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LibraryController extends Controller
{
    public function home()
    {
        $leaders = $this->leaders();

        return view('home', compact('leaders'));
    }

    public function storeLeader(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100']]);
        SongLeader::create(['name' => $validated['name'], 'slug' => Str::slug($validated['name']) . '-' . Str::lower(Str::random(4)), 'user_id' => Auth::id()]);
        return back()->with('success', $validated['name'] . ' was added to your leaders.');
    }

    public function leader(string $slug)
    {
        $record = SongLeader::where('slug', $slug)->where('user_id', Auth::id())->first();
        $leader = $record ? ['name' => $record->name, 'slug' => $record->slug, 'role' => 'Worship leader', 'songs' => $record->songs()->count(), 'initials' => collect(explode(' ', $record->name))->map(fn ($part) => Str::substr($part, 0, 1))->implode(''), 'color' => 'gold'] : null;
        abort_unless($leader, 404);

        return view('leaders.show', [
            'leader' => $leader,
            'songs' => $this->songsForLeader($slug),
        ]);
    }

    public function search(Request $request, string $slug)
    {
        $query = Str::lower(trim($request->string('q')->toString()));
        $songs = collect($this->songsForLeader($slug));

        if ($query !== '') {
            $songs = $songs->filter(fn (array $song) => Str::contains(Str::lower($song['title'] . ' ' . $song['artist']), $query));
        }

        return response()->json(['songs' => $songs->values()]);
    }

    public function song(string $slug)
    {
        $song = $this->songArray(Song::with('leaders')->where('slug', $slug)->where('user_id', Auth::id())->first());
        abort_unless($song, 404);

        return view('songs.show', compact('song'));
    }

    public function createSong(string $leaderSlug)
    {
        $record = SongLeader::where('slug', $leaderSlug)->where('user_id', Auth::id())->first();
        $leader = $record ? ['name' => $record->name, 'slug' => $record->slug] : null;
        abort_unless($leader, 404);

        return view('songs.create', compact('leader'));
    }

    public function storeSong(Request $request, string $leaderSlug)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'artist' => ['nullable', 'string', 'max:120'],
            'original_key' => ['nullable', 'string', 'max:10'],
            'lyrics_chords' => ['required', 'string'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $leader = SongLeader::where('slug', $leaderSlug)->where('user_id', Auth::id())->firstOrFail();
        $song = Song::create([
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']) . '-' . Str::lower(Str::random(5)),
            'artist' => $validated['artist'] ?? null,
            'original_key' => $validated['original_key'] ?? null,
            'content' => collect(preg_split('/\r?\n/', $validated['lyrics_chords']))->map(function (string $line) {
                preg_match('/^\[([^\]]+)\](.*)$/', $line, $matches);
                return [$matches[1] ?? '', $matches[2] ?? $line];
            })->values()->all(),
            'notes' => $validated['notes'] ?? null,
            'user_id' => Auth::id(),
        ]);
        $leader->songs()->attach($song);

        return redirect()->route('leaders.show', $leaderSlug)->with('success', $validated['title'] . ' was added to the lineup.');
    }

    public function deleteSong(string $slug)
    {
        $song = Song::where('slug', $slug)->where('user_id', Auth::id())->firstOrFail();
        $leaderSlug = $song->leaders()->value('slug') ?? null;
        $song->delete();

        return redirect()->route($leaderSlug ? 'leaders.show' : 'home', $leaderSlug ? ['slug' => $leaderSlug] : [])->with('success', 'Song deleted from the library.');
    }

    private function leaders(): array
    {
        $colors = ['gold', 'coral', 'sage', 'sky', 'lavender', 'stone'];
        return SongLeader::where('user_id', Auth::id())->withCount('songs')->orderBy('id')->get()->values()->map(function (SongLeader $leader, int $index) use ($colors) {
            return ['name' => $leader->name, 'slug' => $leader->slug, 'role' => 'Worship leader', 'songs' => $leader->songs_count, 'initials' => collect(explode(' ', $leader->name))->map(fn ($part) => Str::substr($part, 0, 1))->implode(''), 'color' => $colors[$index % count($colors)]];
        })->all();
    }

    private function allSongs(): array
    {
        return Song::where('user_id', Auth::id())->with('leaders')->get()->map(fn (Song $song) => $this->songArray($song))->all();
        /* return [
            ['title' => 'How Great Is Our God', 'slug' => 'how-great-is-our-god', 'artist' => 'Chris Tomlin', 'key' => 'G', 'leader' => 'sis-chin', 'lines' => [['G', 'The splendor of the King'], ['Em', 'Clothed in majesty'], ['C', 'Let all the earth rejoice'], ['D', 'All the earth rejoice'], ['G', 'How great is our God, sing with me'], ['Em', 'How great is our God, and all will see'], ['C', 'How great, how great is our God']], 'tag' => 'Sunday set'],
            ['title' => 'Goodness of God', 'slug' => 'goodness-of-god', 'artist' => 'Jenn Johnson', 'key' => 'G', 'leader' => 'sis-chin', 'lines' => [['G', 'I love You, Lord'], ['C', 'For Your mercy never fails me'], ['Em', 'All my days, I have been held in Your hands'], ['D', 'From the moment that I wake up'], ['G', 'Until I lay my head'], ['C', 'I will sing of the goodness of God']], 'tag' => 'Favorite'],
            ['title' => 'Way Maker', 'slug' => 'way-maker', 'artist' => 'Sinach', 'key' => 'E', 'leader' => 'sis-chin', 'lines' => [['E', 'You are here, moving in our midst'], ['B', 'I worship You, I worship You'], ['C#m', 'You are here, working in this place'], ['A', 'I worship You, I worship You'], ['E', 'Way maker, miracle worker'], ['B', 'Promise keeper, light in the darkness'], ['C#m', 'My God, that is who You are']], 'tag' => 'Sunday set'],
            ['title' => '10,000 Reasons', 'slug' => '10000-reasons', 'artist' => 'Matt Redman', 'key' => 'G', 'leader' => 'sis-chin', 'lines' => [['G', 'Bless the Lord, O my soul'], ['D', 'O my soul, worship His holy name'], ['Em', 'Sing like never before'], ['C', 'O my soul, I worship Your holy name']], 'tag' => 'Classic'],
            ['title' => 'Great Are You Lord', 'slug' => 'great-are-you-lord', 'artist' => 'All Sons & Daughters', 'key' => 'G', 'leader' => 'sis-gerlie', 'lines' => [['G', 'You give life, You are love'], ['Em', 'You bring light to the darkness'], ['C', 'You give hope, You restore'], ['D', 'Every heart that is broken'], ['G', 'Great are You, Lord']], 'tag' => 'Sunday set'],
            ['title' => 'Build My Life', 'slug' => 'build-my-life', 'artist' => 'Housefires', 'key' => 'C', 'leader' => 'sis-gerlie', 'lines' => [['C', 'Worthy of every song we could ever sing'], ['Am', 'Worthy of all the praise we could ever bring'], ['F', 'Worthy of every breath we could ever breathe'], ['G', 'We live for You']], 'tag' => 'Prayer'],
        ]; */
    }

    private function songsForLeader(string $slug): array
    {
        $leader = SongLeader::where('slug', $slug)->where('user_id', Auth::id())->first();
        return $leader ? $leader->songs()->with('leaders')->get()->map(fn (Song $song) => $this->songArray($song))->all() : [];
    }

    private function songArray(?Song $song): ?array
    {
        if (! $song) return null;
        $leader = $song->leaders->first();
        return ['title' => $song->title, 'slug' => $song->slug, 'artist' => $song->artist ?? 'Unknown artist', 'key' => $song->original_key ?? 'C', 'leader' => $leader?->slug, 'lines' => $song->content, 'tag' => 'Internal library'];
    }
}

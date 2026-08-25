const root = document.documentElement;
const savedTheme = localStorage.getItem('ichords-theme');
const savedPalette = localStorage.getItem('ichords-palette') || 'meadow';
if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) root.classList.add('dark');
root.dataset.palette = savedPalette;

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
	button.addEventListener('click', () => {
		root.classList.toggle('dark');
		localStorage.setItem('ichords-theme', root.classList.contains('dark') ? 'dark' : 'light');
	});
});

document.querySelectorAll('[data-theme-choice]').forEach((button) => {
	button.classList.toggle('is-selected', button.dataset.themeChoice === savedPalette);
	button.addEventListener('click', () => {
		root.dataset.palette = button.dataset.themeChoice;
		localStorage.setItem('ichords-palette', button.dataset.themeChoice);
		document.querySelectorAll('[data-theme-choice]').forEach((choice) => choice.classList.toggle('is-selected', choice === button));
	});
});

const search = document.querySelector('[data-song-search]');
if (search) {
	const results = document.querySelector('[data-song-results]');
	const status = document.querySelector('[data-search-status]');
	const clear = document.querySelector('[data-search-clear]');
	let timer;
	const render = (songs) => {
		results.innerHTML = songs.length ? songs.map((song) => `<a href="/songs/${song.slug}" class="song-row group"><span><span class="song-title">${song.title}</span><span class="song-meta">${song.artist} · Key ${song.key}</span></span><span class="row-arrow">↗</span></a>`).join('') : '<div class="empty-state">No songs found. Try another search.</div>';
	};
	const runSearch = () => {
		status.textContent = 'Searching...';
		fetch(search.dataset.url + '?q=' + encodeURIComponent(search.value))
			.then((response) => response.json())
			.then((data) => { render(data.songs); status.textContent = `${data.songs.length} song${data.songs.length === 1 ? '' : 's'}`; })
			.catch(() => { status.textContent = 'Search unavailable'; });
	};
	search.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(runSearch, 280); clear.hidden = !search.value; });
	clear.addEventListener('click', () => { search.value = ''; clear.hidden = true; runSearch(); search.focus(); });
}

document.querySelectorAll('[data-transpose]').forEach((control) => {
	const output = document.querySelector('[data-chord-sheet]');
	const label = document.querySelector('[data-transpose-label]');
	const original = JSON.parse(output.dataset.lines);
	let offset = 0;
	const notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
	const transpose = (chord) => chord.replace(/[A-G](#|b)?/, (root) => { const normalized = root.replace('Db', 'C#').replace('Eb', 'D#').replace('Gb', 'F#').replace('Ab', 'G#').replace('Bb', 'A#'); return notes[(notes.indexOf(normalized) + offset + 120) % 12]; });
	const draw = () => { output.innerHTML = original.map((line) => `<div class="chord-line"><span class="chord">${transpose(line[0])}</span><span class="lyric">${line[1]}</span></div>`).join(''); label.textContent = offset === 0 ? 'Original' : `${offset > 0 ? '+' : ''}${offset} semitone${Math.abs(offset) === 1 ? '' : 's'}`; };
	control.querySelector('[data-step="down"]').addEventListener('click', () => { offset--; draw(); });
	control.querySelector('[data-step="up"]').addEventListener('click', () => { offset++; draw(); });
	draw();
});

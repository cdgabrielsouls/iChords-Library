const root = document.documentElement;
const savedTheme = localStorage.getItem('ichords-theme');
const savedPalette = localStorage.getItem('ichords-palette') || 'meadow';
if (savedTheme === 'dark' || (!savedTheme && window.matchMedia('(prefers-color-scheme: dark)').matches)) root.classList.add('dark');
root.dataset.palette = savedPalette;

let installPrompt;
const installButtons = document.querySelectorAll('[data-install-app]');
const installHelp = document.querySelector('[data-install-help]');
window.addEventListener('beforeinstallprompt', (event) => {
	event.preventDefault();
	installPrompt = event;
	installButtons.forEach((button) => { button.hidden = false; });
	if (installHelp) installHelp.textContent = 'Choose Download iChords to install the app on this device.';
});
installButtons.forEach((button) => button.addEventListener('click', async () => {
	if (!installPrompt) {
		if (installHelp) installHelp.textContent = 'On iPhone, open Safari, tap Share, then choose Add to Home Screen. On Android or desktop, use your browser menu and choose Install app.';
		return;
	}
	installPrompt.prompt();
	await installPrompt.userChoice;
	installPrompt = null;
	installButtons.forEach((installButton) => { installButton.hidden = true; });
}));
window.addEventListener('appinstalled', () => {
	installPrompt = null;
	installButtons.forEach((button) => { button.hidden = true; });
});
if ('serviceWorker' in navigator && window.isSecureContext) {
	navigator.serviceWorker.register('/service-worker.js').catch(() => {});
}

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
const librarySearch = document.querySelector('[data-library-search]');
if (librarySearch) {
	const results = document.querySelector('[data-library-search-results]');
	const status = document.querySelector('[data-library-search-status]');
	const pagination = document.querySelector('[data-library-search-pagination]');
	let timer;
	const escape = (value) => String(value).replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character]));
	const render = (data) => {
		results.innerHTML = data.songs.length ? data.songs.map((song) => `<a href="/songs/${encodeURIComponent(song.slug)}" class="song-row group"><span><span class="song-title">${escape(song.title)}</span><span class="song-meta">${escape(song.artist)} · Key ${escape(song.key)}</span></span><span class="row-arrow">↗</span></a>`).join('') : '<div class="empty-state">No songs found.</div>';
		status.textContent = data.total ? `${data.total} song${data.total === 1 ? '' : 's'}` : '';
		pagination.innerHTML = data.last_page > 1 ? `<button type="button" data-library-page="${data.current_page - 1}" ${data.current_page === 1 ? 'disabled' : ''}>←</button><span>Page ${data.current_page} of ${data.last_page}</span><button type="button" data-library-page="${data.current_page + 1}" ${data.current_page === data.last_page ? 'disabled' : ''}>→</button>` : '';
		pagination.querySelectorAll('[data-library-page]').forEach((button) => button.addEventListener('click', () => runSearch(Number(button.dataset.libraryPage))));
	};
	const runSearch = (page = 1) => fetch(`${librarySearch.dataset.url}?q=${encodeURIComponent(librarySearch.value)}&page=${page}`).then((response) => response.json()).then(render).catch(() => { status.textContent = 'Search unavailable'; });
	librarySearch.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(() => runSearch(), 220); });
}

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
	const keyPill = document.querySelector('.key-pill');
	const originalKey = keyPill?.textContent ?? 'C';
	const original = JSON.parse(output.dataset.lines);
	let offset = 0;
	const notes = ['C', 'C#', 'D', 'D#', 'E', 'F', 'F#', 'G', 'G#', 'A', 'A#', 'B'];
	const normalize = (note) => note.replace('Db', 'C#').replace('Eb', 'D#').replace('Gb', 'F#').replace('Ab', 'G#').replace('Bb', 'A#');
	const transpose = (chord) => chord.replace(/[A-G](#|b)?/g, (root) => notes[(notes.indexOf(normalize(root)) + offset + 120) % 12]);
	const transposedKey = () => notes[(notes.indexOf(normalize(originalKey)) + offset + 120) % 12];
	const draw = () => {
		output.innerHTML = original.map((line) => line.section ? `<div class="chord-section">${line.section}</div>` : `<div class="chord-line"><span class="chord">${transpose(line[0])}</span><span class="lyric">${line[1]}</span></div>`).join('');
		label.textContent = offset === 0 ? 'Original' : `${offset > 0 ? '+' : ''}${offset} semitone${Math.abs(offset) === 1 ? '' : 's'}`;
		if (keyPill) keyPill.textContent = transposedKey();
	};
	control.querySelector('[data-step="down"]').addEventListener('click', () => { offset--; draw(); });
	control.querySelector('[data-step="up"]').addEventListener('click', () => { offset++; draw(); });
	draw();
});

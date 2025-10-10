(function($){
function el(id){ return document.getElementById(id); }
const btnStart = el('bis-start');
const btnCancel= el('bis-cancel');
const barWrap = document.querySelector('.bis-progress');
const bar = document.querySelector('.bis-bar');
const meta = el('bis-meta');


let cancel = false;
let state = null;


function setProgress(p){ if(barWrap&&bar){ barWrap.style.display='block'; bar.style.width=Math.max(0,Math.min(100,p))+'%'; } }
function setMeta(t){ if(meta){ meta.textContent=t; } }
function disable(dis){ ['bis_from','bis_to','bis_batch','bis_limit'].forEach(i=>{ const x=el(i); if(x) x.disabled=dis; }); if(btnStart) btnStart.disabled=dis; if(btnCancel) btnCancel.style.display= dis ? 'inline-block' : 'none'; }


async function loop(){
if(cancel){ setMeta('Scansione annullata.'); disable(false); return; }
const fd = new FormData();
fd.append('action','broken_images_scan_step');
fd.append('_ajax_nonce', BROKEN_IMAGES.nonce);
fd.append('from', state.from);
fd.append('to', state.to);
fd.append('batch', state.batch);
fd.append('limit', state.limit);
fd.append('page', state.page);
fd.append('checked', state.checked);


try{
const res = await fetch(BROKEN_IMAGES.ajax_url, { method:'POST', body: fd, credentials:'same-origin' });
if(!res.ok) throw new Error('HTTP '+res.status);
const json = await res.json();
if(!json || !json.success) throw new Error((json && json.data && json.data.message) || 'AJAX error');
const d = json.data;


state.checked = d.checked;
state.page = d.next_page || (state.page + 1);
state.maxPages = d.max_pages || state.maxPages || 0;


const pct = state.maxPages>0 ? Math.min(100, Math.round((state.page-1)/state.maxPages*100)) : Math.min(100, Math.round(state.checked/state.limit*100));
setProgress(pct);
setMeta(BROKEN_IMAGES.strings.scanning + ` — Controllati: ${state.checked} — Rotti: ${d.broken_count}`);


if(d.finished){
setProgress(100);
setMeta(BROKEN_IMAGES.strings.done + ` — Controllati: ${state.checked} — Rotti: ${d.broken_count}. Ricarico…`);
window.location = window.location.href.split('&deleted=')[0];
return;
}
loop();
} catch(e){
console.error(e);
setMeta(BROKEN_IMAGES.strings.error + ': ' + (e.message||e));
disable(false);
}
}


if(btnStart){
btnStart.addEventListener('click', function(){
cancel=false;
state = {
from: (el('bis_from') && el('bis_from').value) || '',
to: (el('bis_to') && el('bis_to').value) || '',
batch: parseInt((el('bis_batch') && el('bis_batch').value) || '100',10),
limit: parseInt((el('bis_limit') && el('bis_limit').value) || '2000',10),
page: 1,
checked: 0,
maxPages: 0
};
disable(true);
setProgress(0);
setMeta(BROKEN_IMAGES.strings.starting);
loop();
});
}
if(btnCancel){ btnCancel.addEventListener('click', function(){ cancel=true; disable(false); setMeta('Annullato dall\'utente.'); }); }
})(jQuery);
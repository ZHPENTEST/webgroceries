document.getElementById('filterForm')?.addEventListener('change',e=>{if(e.target.name==='sort')e.target.form.submit();});
// On small screens the filter panel starts collapsed to save space
if(matchMedia('(max-width:900px)').matches)document.querySelectorAll('details.filters[open]').forEach(d=>d.removeAttribute('open'));

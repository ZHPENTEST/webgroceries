// Map pin picker: Leaflet/OSM (free, no key) or Google Maps (key from admin settings).
// Drag pin or tap map -> reverse-geocode -> auto-fill empty address fields + save lat/lng.
(function(){
  const box = document.getElementById('mapPicker');
  if (!box) return;
  const latI = document.getElementById('f_lat'), lngI = document.getElementById('f_lng');
  const read = document.getElementById('pinRead');
  const KEY = (box.dataset.key || '').trim();
  const DEF = [3.139, 101.6869]; // Kuala Lumpur
  const lat0 = parseFloat(latI.value) || DEF[0], lng0 = parseFloat(lngI.value) || DEF[1];

  function setRead(lat, lng){ if (read) read.textContent = 'Pin: ' + lat.toFixed(5) + ', ' + lng.toFixed(5); }
  function fill(addr){
    const f = document.getElementById('coForm');
    if (!f) return;
    let touched = false;
    if (addr.road && !f.line1.value) { f.line1.value = addr.road; touched = true; }
    if (addr.city && !f.city.value) { f.city.value = addr.city; touched = true; }
    if (addr.postcode && !f.postcode.value) { f.postcode.value = addr.postcode; touched = true; }
    if (touched) toast('Alamat diisi dari pin — boleh ubah');
  }
  async function revOSM(lat, lng){
    const r = await fetch('https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=' + lat + '&lon=' + lng);
    const j = await r.json(), a = j.address || {};
    return { road: [a.road, a.suburb, a.neighbourhood].filter(Boolean).join(', '), city: a.city || a.town || a.village || a.state || '', postcode: a.postcode || '' };
  }
  function pin(lat, lng, rev){
    latI.value = lat.toFixed(6); lngI.value = lng.toFixed(6); setRead(lat, lng);
    if (rev) rev(lat, lng).then(fill).catch(() => {});
  }

  document.getElementById('locateBtn')?.addEventListener('click', () => {
    if (!navigator.geolocation) return toast('GPS tidak disokong');
    navigator.geolocation.getCurrentPosition(p => move(p.coords.latitude, p.coords.longitude, true),
      () => toast('Tidak dapat kesan lokasi'), { timeout: 8000 });
  });

  // ---- Google Maps mode (needs API key) ----
  if (KEY) {
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=' + encodeURIComponent(KEY);
    s.onload = () => {
      try {
        const gmap = new google.maps.Map(box, { center: { lat: lat0, lng: lng0 }, zoom: 14 });
        const mk = new google.maps.Marker({ position: { lat: lat0, lng: lng0 }, map: gmap, draggable: true });
        const geo = new google.maps.Geocoder();
        window.move = (la, ln, rev) => { mk.setPosition({ lat: la, lng: ln }); gmap.setCenter({ lat: la, lng: ln }); pin(la, ln, rev ? gRev : null); };
        const gRev = (la, ln) => new Promise(res => geo.geocode({ location: { lat: la, lng: ln } }, r => {
          const a = (r && r[0] && r[0].address_components) || [];
          const pick = t => (a.find(c => c.types.includes(t)) || {}).long_name || '';
          res({ road: pick('route'), city: pick('locality') || pick('administrative_area_level_1'), postcode: pick('postal_code') });
        }));
        mk.addListener('dragend', () => { const p = mk.getPosition(); pin(p.lat(), p.lng(), gRev); });
        gmap.addListener('click', e => move(e.latLng.lat(), e.latLng.lng(), true));
        setRead(lat0, lng0);
      } catch (e) { box.innerHTML = '<p class="muted">Peta Google gagal dimuat.</p>'; }
    };
    s.onerror = () => { box.innerHTML = '<p class="muted">Peta Google gagal dimuat.</p>'; };
    document.head.appendChild(s);
    return;
  }

  // ---- Free mode: Leaflet + dark CARTO tiles ----
  if (typeof L === 'undefined') { box.innerHTML = '<p class="muted">Peta gagal dimuat — isi alamat manual.</p>'; return; }
  const map = L.map(box).setView([lat0, lng0], 13);
  L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
    attribution: '&copy; OpenStreetMap &copy; CARTO', subdomains: 'abcd', maxZoom: 19
  }).addTo(map);
  const mk = L.marker([lat0, lng0], { draggable: true }).addTo(map);
  window.move = (la, ln, rev) => { mk.setLatLng([la, ln]); map.setView([la, ln], 15); pin(la, ln, rev ? revOSM : null); };
  mk.on('dragend', () => { const p = mk.getLatLng(); pin(p.lat, p.lng, revOSM); });
  map.on('click', e => move(e.latlng.lat, e.latlng.lng, true));
  setRead(lat0, lng0);
  setTimeout(() => map.invalidateSize(), 300);
})();
